<?php
/* 
How this file works

removes -- commands from URL
identifies and captures the values for pitch
identifies and captures the values for clean
derives name from URL
checks if MD5 already exists in the database
if MD5 not found, send URL for downloading
    store response details in the database
Select * from database where name = $name

Changes:
dont use md5 for checking if file exists, use name instead
seperate 
    checking if exists for download, return exists = false
    when sent response from http://127.0.0.1:7865/run/download_from_url save result in database
    return array or error message

What I am fixing:
this file should execute in less than a second regardless of request so http://127.0.0.1:7865/run/download_from_url must be moved to python script

if name exists and return array
if name exists but missing files return error
if name does not exist return error needs download
save returns array or error

input url or save data
output array or error (missing or needs download)
*/


set_time_limit(300);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}

// if POST url contains "huggingface" and /blob/main/ str replace with /resolve/main/
if (strpos($_POST['url'], 'huggingface') !== false && strpos($_POST['url'], '/blob/main/') !== false) {
    $_POST['url'] = str_replace('/blob/main/', '/resolve/main/', $_POST['url']);
}

// detect is save data or url
if (isset($_POST['name']) && $_POST['md5_hash'] != "T") {
    // save data
    $url = $_POST['url'];
    $name = $_POST['name'];
    $md5_hash = $_POST['md5_hash'];
    $index_name = $_POST['index_name'];
    $original_name = $_POST['original_name'];
    // if file exists logs/{name}/original.txt check contents and if not empty and different to original_name then original_name = contents
    $original_file = "logs/$name/original.txt";
    if (file_exists($original_file)) {
        $original_contents = file_get_contents($original_file);
        if (!empty($original_contents) && $original_contents != $original_name) {
            $original_name = $original_contents;
        }
    }
    $query = "INSERT INTO files (url, name, md5, original_name, index_name) VALUES (?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("sssss", $url, $name, $md5_hash, $original_name, $index_name);
    $stmt->execute();
    $array = array(
        "url" => $url,
        "name" => $name,
        "md5_hash" => $md5_hash,
        "index_name" => $index_name,
        "original_name" => $original_name
    );
    $stmt->close();
}


$url = $_POST['url'];
$user_id = $_POST['user_id'];

// Step 1: Remove -- commands from URL
$pattern = '/--[a-zA-Z]+(\s*[\w\-]+)?/';
$url = preg_replace($pattern, '', $_POST['url']);
//trim spaces
$url = trim($url);

// Step 2: Identify and capture the values
if (preg_match('/--p(?:itch)?\s*(-?\d{1,2})/', $_POST['url'], $matches)) {
    $pitch = $matches[1];
} else {
    $pitch = 0;
}

if (preg_match('/--clean\s+(true|false)/', $_POST['url'], $matches)) {
    $clean = ($matches[1] == 'true') ? true : false;
} else {
    $clean = false;
}

$name = deriveNameFromURL($url);


// select * from files where md5 = 'd41d8cd98f00b204e9800998ecf8427e'
$query = "SELECT * FROM files WHERE name = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();


// if no row is empty return error message "that model does not exist" as json and exit
if (empty($row)) {
    $array = array(
        "error" => "no model",
        "name" => $name,
        "url" => $url
    );
    header('Content-Type: application/json');
    echo json_encode($array);
    exit;
}

// if $row['index_name'] empty return error message "that model has no index" as json and exit
// if (empty($row['index_name'])) {
//     $array = array(
//         "error" => "that model has no index"
//     );
//     header('Content-Type: application/json');
//     echo json_encode($array);
//     exit;
// }

// if $url ends with ?usp=drive_link error message "private models are not supported yet" as json and exit
// if (substr($url, -strlen('?usp=drive_link')) === '?usp=drive_link') {
//     $array = array(
//         "error" => "private models are not supported yet"
//     );
//     header('Content-Type: application/json');
//     echo json_encode($array);
//     exit;
// }

// echo JSON with url, name, md5_hash, index_name, original_name
$array = array(
    "url" => $row['url'],
    "name" => $row['name'],
    "md5_hash" => $row['md5'],
    "index_name" => $row['index_name'],
    "original_name" => $row['original_name']
);


if (!is_null($pitch)) {
    $array["pitch"] = $pitch;
}

// if post pitch is set
if (isset($_POST['pitch']) && $_POST['pitch'] != 0) {
    $pitch = $_POST['pitch'];
    $array["pitch"] = $pitch;
}

if (!is_null($clean)) {
    $array["clean"] = $clean;
}



// echo JSON
header('Content-Type: application/json');

echo json_encode($array);


// Derived name from URL
function deriveNameFromURL($url)
{
    // Step 1: Remove common starts
    $starts = [
        'https://mega.nz/file/',
        'https://drive.google.com/file/d/',
        'https://pixeldrain.com/u/',
        'https://huggingface.co/'
    ];

    foreach ($starts as $start) {
        if (strpos($url, $start) === 0) {
            $url = str_replace($start, '', $url);
            break;
        }
    }

    // Step 2: Remove common ends
    $ends = [
        '/view?usp=sharing',
        '.zip',
        '/view',
        '/view?usp=drive_link',
        '/view?usp=share_link',
    ];

    foreach ($ends as $end) {
        if (substr($url, -strlen($end)) === $end) {
            $url = substr($url, 0, -strlen($end));
            break;
        }
    }

    // Step 3: Replace common mid parts with _
    $url = str_replace('/resolve/main/', '_', $url);

    // Step 4: Replace / with _
    $url = str_replace('/', '_', $url);

    // Step 5: Remove unsuitable characters for filenames
    $url = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $url);
    return $url;
}


// Check if MD5 already exists in the database
function checkMd5Exists($con, $url)
{
    $query = "SELECT md5 FROM files WHERE url = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $url);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row['md5'] ?? false;
}

// Send URL for downloading
function downloadFile($url, $name)
{
    $postData = json_encode([
        "data" => [
            $url,
            $name
        ]
    ]);
    $url = "http://127.0.0.1:7865/run/download_from_url";
    // $ch = curl_init();
    // curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:7865/run/download_from_url");
    // curl_setopt($ch, CURLOPT_POST, 1);
    // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["data" => ["url" => $url, "model" => $name]]));
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);
    // echo "Response to $url $name :\n";
    // echo $response;
    return json_decode($response, true)['data'];
}

// Store response details in the database
function storeInDatabase($con, $url, $response, $name)
{
    // print_r($response);
    $query = "INSERT INTO files (url, name, md5, original_name, index_name) VALUES (?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("sssss", $url, $name, $response[0], $response[2], $response[1]);
    // echo "INSERT INTO files (url, name, md5, original_name, index_name) VALUES ('$url', '$name', '$response[0]', '$response[2]', '$response[1]')";
    // if $response[0] != T execute
    if ($response[0] != 'T') {
        $stmt->execute();
    }
    $stmt->close();
    // $stmt->execute();
}
