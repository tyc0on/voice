<?php
//errors on
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}

// if $_POST['type'] == discord
if ($_POST['type'] == 'discord') {


    // $url = "https://discord.com/api/webhooks/1139786475911794738/KcbCIMYym1NicZ9tn-a8bdwmOnj7ZUuhijc4TxhYMp5vtfEICC-ZUyZANUo76PXZyPvC/messages/1140239155041939496";
    // $url = "https://discord.com/api/webhooks/1139786475911794738/KcbCIMYym1NicZ9tn-a8bdwmOnj7ZUuhijc4TxhYMp5vtfEICC-ZUyZANUo76PXZyPvC";

    // switch case $_POST['channel_id'] set url
    switch ($_POST['channel_id']) {
        case "1142759953103335464":
            $url = "https://discord.com/api/webhooks/1142760352178769941/AO1_OUEq8d9iREiBLJwzpuNoW0raQPkYRBotq9sxH4FRtg2-mGcrHtgtDZWwa6zhDxeC";
            break;
        case "1143077531902296074":
            // #voice-2
            $url = "https://discord.com/api/webhooks/1143077993015672852/CE9LAKVOfv_H2MtmELlSPJWh5Zx820DKF_kGznfeX_EbdGm5hCguwUg6J_Tw-llgFV9-";
            break;
        case "1143077533735198761":
            // #voice-3
            $url = "https://discord.com/api/webhooks/1143078096333975624/1vwuKb_95rmpmtSvo4XXMp9PWwmDL3tGqBuhdILM0y3Tqbr_utBEadYhiTUxGXIOcL27";
            break;
        default:
            $url = "https://discord.com/api/webhooks/1142760352178769941/AO1_OUEq8d9iREiBLJwzpuNoW0raQPkYRBotq9sxH4FRtg2-mGcrHtgtDZWwa6zhDxeC";
    }



    if (isset($_POST['error_message'])) {
        $errorMessage = $_POST['error_message'];
        $error_payload = [
            "username" => "VoicezeBot Worker ⛏️",
            "content" => "<@" . $_POST['user_id'] . "> Error: " . $errorMessage,
        ];
        // $json_data = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($error_payload));

        $response = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode == 200) {
            echo "Error reported successfully to webhook.";
        } else {
            echo "Failed to report error to webhook.";
            // If necessary, you can throw an exception here.
        }

        curl_close($ch);
        die;
    }


    // Step 1: Receive the .mp3 file from Python Worker
    if (!isset($_FILES['audioFile'])) {
        die("No audio file uploaded.");
    }
    if ($_FILES['audioFile']['error'] !== UPLOAD_ERR_OK) {
        $errorCode = $_FILES['audioFile']['error'];
        $errorMessages = array(
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.'
        );

        $errorMessage = array_key_exists($errorCode, $errorMessages) ? $errorMessages[$errorCode] : "Unknown error with code $errorCode.";

        die("Error uploading audio file: $errorMessage");
    }

    $tempFile = $_FILES['audioFile']['tmp_name'];

    if (!file_exists($tempFile)) {
        die("Error: Uploaded file not found.");
    }
    // Since we already have the file, we skip downloading it again

    // Step 2: Send the .mp3 file to Discord
    $ch = curl_init($url);

    // YYMMDD-HHMMSS
    $timestamp = date("ymd-His");
    // if $_POST[pitch] != 0 then $pitch = $_POST[pitch] else $pitch = ""
    if (isset($_POST['pitch'])) {
        $pitch = "_p" . $_POST['pitch'];
    } else {
        $pitch = "";
    }

    // extension of the file
    $extension = pathinfo($_FILES['audioFile']['name'], PATHINFO_EXTENSION);

    $postFields = [
        "username" => "VoicezeBot Worker ⛏️",
        "content" => "<@" . $_POST['user_id'] . "> Here is your audio file:",
        "file0" => curl_file_create($tempFile, "audio/mpeg", $timestamp . "_" . $_POST['original_name']  . $pitch . "_" . $_POST['username'] . "." . $extension)
    ];

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));

    $response = curl_exec($ch);

    // Cleaning up
    unlink($tempFile);
    echo $response;


    $sql = "INSERT INTO log (log) VALUES (?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $response);
    if ($stmt->execute()) {
        //get last id
        $webhook = $con->insert_id;
    } else {
    }
    $stmt->close();
    curl_close($ch);

    $user_id = $_POST['user_id'];
    $query = "SELECT id FROM users WHERE discord_id = ?";
    $stmt = $con->prepare($query);
    if (!$stmt) {
        die("Query preparation failed: " . $con->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $userid = $row['id'];

    // turn $response to array
    $response = json_decode($response, true);

    // round $_POST['audio_length'] to 2 decimal places
    $audio_url = $response['attachments'][0]['url'];
    $audio_length = "-" . round($_POST['audio_length'], 2);
    $interaction = $_POST['interaction'];
    $msg = "spending";
    $query = "INSERT INTO credits (user_id, amount, source, notes, interaction, webhook) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("iissii", $userid, $audio_length, $msg, $audio_url, $interaction, $webhook);
    $stmt->execute();
    $stmt->close();
} elseif ($_POST['type'] == 'eav') {

    if (!file_exists('samples')) {
        mkdir('samples', 0755, true);
    }


    if (!isset($_FILES['audioFile'])) {
        if ($_POST['job'] > 0) {
            $sql = "UPDATE jobs SET status = ? WHERE id = ?";
            $stmt = $con->prepare($sql);
            $status = "failed";
            $stmt->bind_param('si', $status, $_POST['job']);
            $stmt->execute();
            $stmt->close();

            $sql = "SELECT batch_id FROM jobs WHERE id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('i', $_POST['job']);
            $stmt->execute();
            $stmt->bind_result($batch_id);
            $stmt->fetch();
            $stmt->close();

            $sql = "UPDATE batch SET status = ? WHERE id = ?";
            $stmt = $con->prepare($sql);
            $status = "failed";
            $stmt->bind_param('si', $status, $batch_id);
            $stmt->execute();
            $stmt->close();
        }
        die("No audio file uploaded...");
    }
    if ($_FILES['audioFile']['error'] !== UPLOAD_ERR_OK) {
        $errorCode = $_FILES['audioFile']['error'];
        $errorMessages = array(
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.2',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.'
        );

        $errorMessage = array_key_exists($errorCode, $errorMessages) ? $errorMessages[$errorCode] : "Unknown error with code $errorCode.";

        die("Error uploading audio file: $errorMessage");
    }

    $tempFile = $_FILES['audioFile']['tmp_name'];

    if (!file_exists($tempFile)) {
        die("Error: Uploaded file not found.");
    }

    // if post[pitch] set $pitch = ".p" . $_POST[pitch] else $pitch = ""
    if (isset($_POST['pitch']) && $_POST['pitch'] != 0) {
        $pitch = ".p" . $_POST['pitch'];
    } else {
        $pitch = "";
    }

    // if folder audios not exists create
    if (!file_exists('paudios')) {
        mkdir('paudios', 0755, true);
    }


    // if isset POST job save in paudios folder with name of timestamp . ".j" . job . ".p" . $pitch . .mp3
    if ($_POST['job'] != 0) {
        $timestamp = date("ymd-His");
        $job = $_POST['job'];
        $extension = pathinfo($_FILES['audioFile']['name'], PATHINFO_EXTENSION);
        $newFile = "paudios/" . $timestamp . ".j" . $job . $pitch . "." . $extension;
        move_uploaded_file($tempFile, $newFile);
        // insert into paudio_files table with user_id, file_path, file_format, job_id
        $sql = "INSERT INTO paudio_files (user_id, file_path, file_format, job_id) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('isss', $_POST['user_id'], $newFile, $extension, $job);
        $stmt->execute();
        $paudio_id = $con->insert_id;
        $stmt->close();

        // update table jobs set paudio_id = $paudio_id where id = $_POST['job']
        $sql = "UPDATE jobs SET paudio_id = ?, status = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        $status = "complete";
        $stmt->bind_param('isi', $paudio_id, $status, $_POST['job']);
        $stmt->execute();
        $stmt->close();

        $sql = "SELECT batch_id FROM jobs WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $_POST['job']);
        $stmt->execute();
        $stmt->bind_result($batch_id);
        $stmt->fetch();
        $stmt->close();

        // select * from jobs where id = $_POST['job'] and status != "complete", if empty then update table batch set status = "complete" where id = jobs.batch_id
        $sql = "SELECT * FROM jobs WHERE batch_id = ? AND status != ?";
        $stmt = $con->prepare($sql);
        $status = "complete";
        $stmt->bind_param('is', $batch_id, $status);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows === 0) {
            $sql = "UPDATE batch SET status = ? WHERE id = ?";
            $stmt = $con->prepare($sql);
            $status = "complete";
            $stmt->bind_param('si', $status, $batch_id);
            $stmt->execute();
            $stmt->close();
        }

        // if jobs.model_id == 0 then use original_name to search files.original_name and update jobs.model_id = files.model_id
        $sql = "SELECT model_id FROM jobs WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $_POST['job']);
        $stmt->execute();
        $stmt->bind_result($model_id);
        $stmt->fetch();
        $stmt->close();
        if ($model_id == 0) {
            $sql = "SELECT id FROM files WHERE original_name = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('s', $_POST['original_name']);
            $stmt->execute();
            $stmt->bind_result($model_id);
            $stmt->fetch();
            $stmt->close();

            $sql = "UPDATE jobs SET model_id = ? WHERE id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('ii', $model_id, $_POST['job']);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        // save in samples folder with name of $_POST['name'].mp3
        $name = $_POST['name'];
        $extension = pathinfo($_FILES['audioFile']['name'], PATHINFO_EXTENSION);
        if (!file_exists('samplesfail')) {
            mkdir('samplesfail', 0755, true);
        }
        if ($_POST['username'] == "None" || $_POST['username'] == "") {
            $username = "samplesfail";
        } else {
            $username = $_POST['username'];
        }

        $newFile = $username . "/" . $name . $pitch . "." . $extension;
        move_uploaded_file($tempFile, $newFile);
    }
    // return success json

}


$con->close();
$response = [
    "success" => true
];
echo json_encode($response);
