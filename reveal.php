<?php
/*
Resolve file locations
- if batch add to batch_jobs
- check user_id for existingFiles
- save as <md5>.<extension>
- add existing files to audioFiles

Add to jobs table
- 1 per file
- calculate how many in queue
*/

session_start();
if (@$_SESSION['id'] == 1) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: /sign-in');
    exit();
}




print_r($_POST);


echo "\n<br><br>\n";

print_r($_FILES);



include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}

// if folder audios not exists create
if (!file_exists('audios')) {
    mkdir('audios', 0755, true);
}

// if post existingFiles not empty
if (!empty($_POST['existingFiles'])) {
    // get existingFiles
    $existingFiles = $_POST['existingFiles'];
    // foreach existingFiles check table audio_files that user_id == $_SESSION['id'] and get file_path
    foreach ($existingFiles as $existingFile) {
        $sql = "SELECT * FROM audio_files WHERE user_id = ? AND id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('is', $_SESSION['id'], $existingFile);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        // if file_path exists
        if ($result->num_rows > 0) {
            // add to audioFiles
            $audioFiles[] = $existingFile;
        }
    }
}

// if files not empty 
if (!empty($_FILES['files']['name'][0])) {
    // foreach  add to audio_files table with user_id, file_path, original_name, file_format
    foreach ($_FILES['files']['name'] as $key => $name) {
        $tmp_name = $_FILES['files']['tmp_name'][$key];
        $error = $_FILES['files']['error'][$key];
        $size = $_FILES['files']['size'][$key];
        $type = $_FILES['files']['type'][$key];
        $original_name = $name;
        $file_format = pathinfo($name, PATHINFO_EXTENSION);
        $file_path = 'audios/' . md5_file($tmp_name) . '.' . $file_format;
        // if file_path already exists get id and add to audioFiles or insert into audio_files table and add to audioFiles
        if (file_exists($file_path)) {
            $sql = "SELECT * FROM audio_files WHERE file_path = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('s', $file_path);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $audioFiles[] = $row['id'];
            } else {
                $sql = "INSERT INTO audio_files (user_id, file_path, original_name, file_format) VALUES (?, ?, ?, ?)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param('isss', $_SESSION['id'], $file_path, $original_name, $file_format);
                $stmt->execute();
                $audioFiles[] = $con->insert_id;
                $stmt->close();
            }
        } else {
            // move file to audios folder
            move_uploaded_file($tmp_name, $file_path);
            // add to audio_files table with user_id, file_path, original_name, file_format
            $sql = "INSERT INTO audio_files (user_id, file_path, original_name, file_format) VALUES (?, ?, ?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('isss', $_SESSION['id'], $file_path, $original_name, $file_format);
            $stmt->execute();
            $audioFiles[] = $con->insert_id;
            $stmt->close();
        }
    }
}

//remove duplicates from audioFiles
$audioFiles = array_unique($audioFiles);

if (empty($audioFiles)) {
    die("No files.");
} elseif (count($audioFiles) > 10) {
    die("Too many files.");
    // } elseif (count($audioFiles) == 1) {
    //     $batch = 0;
} else {

    // if batch
    // add to batch table with user_id, status
    $sql = "INSERT INTO batch (user_id, status) VALUES (?, ?)";
    $stmt = $con->prepare($sql);
    $status = "pending";
    $stmt->bind_param('is', $_SESSION['id'], $status);
    $stmt->execute();
    $batch = $con->insert_id;
    $stmt->close();
}

// get model_id from files where POST name = files.name
$sql = "SELECT * FROM files WHERE name = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('s', $_POST['name']);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
// if model_id exists set model_id
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $model_id = $row['id'];
} else {
    die("Model not found.");
}


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require 'vendor/autoload.php';

$connection = new AMQPStreamConnection($sqlh, 5672, 'admintycoon', $rabbitp, 'voice');
$channel = $connection->channel();

$channel->queue_declare('job_queue', false, true, false, false, false, new \PhpAmqpLib\Wire\AMQPTable(['x-max-priority' => 10]));

$pitches = array(-16, -12, -8, -4, 4, 8, 12, 16);

// while ($row = $result->fetch_assoc()) {
foreach ($audioFiles as $audioFile) {
    $sql = "INSERT INTO jobs (user_id, audio_id, model_id, pitch, status, batch_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $status = "pending";
    $stmt->bind_param('iiissi', $_SESSION['id'], $audioFile, $model_id, $_POST['pitch'], $status, $batch);
    $stmt->execute();
    $job = $con->insert_id;
    $stmt->close();



    $sql = "SELECT * FROM audio_files WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $audioFile);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    // if file_path exists add to audioFiles
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // url is the url of the audio file to process
        $url = "https://easyaivoice.com/" . $row['file_path'];
        // $name = $row['name'];
        //model url
        $name = $_POST['name'];
        $pitch = $_POST['pitch'];

        $jobData = array(
            'audio_url' => $url,
            'voice_model' => $name,
            'type' => 'eav',
            'pitch' => $pitch,
            'job' => $job,
            'credits' => 99999999,
            'settings' => 'none',
            'guild_id' => 1,
            'channel_id' => 1,
            'message_id' => 1,
            'interaction_id' => 1,
            'interaction_token' => 'None',
            'interaction' => 1,
            'application_id' => 1,
            'metadata' => array(
                'member' => array(
                    'user' => array(
                        'id' => 1,
                        'username' => 'None',
                        'global_name' => 'None',
                    )
                )
            )
        );

        $msg = new AMQPMessage(json_encode($jobData));
        $channel->basic_publish($msg, '', 'job_queue');
    }
    // die;
}
// }
$channel->close();
$connection->close();


// foreach audioFiles get file_path from audio_files where id = audioFiles

// $row['file_path'];

// add job to rabbitmq

print_r($audioFiles);

// $sql = "SELECT * FROM batch WHERE user_id = ?";
// $stmt = $con->prepare($sql);
// $stmt->bind_param('i', $_SESSION['id']);
// $stmt->execute();
// loop through each row
