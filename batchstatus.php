<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'include.php';
include 'variables.php';

// use PhpAmqpLib\Connection\AMQPStreamConnection;
// use PhpAmqpLib\Message\AMQPMessage;

// require 'vendor/autoload.php';

$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    echo json_encode(['error' => "Connection failed: " . $con->connect_error]);
    exit();
}



// $connection = new AMQPStreamConnection($sqlh, 5672, 'admintycoon', $rabbitp, 'voice');
// $channel = $connection->channel();

// $queue_info = $channel->queue_declare('job_queue', false, true, false, false, false, new \PhpAmqpLib\Wire\AMQPTable(['x-max-priority' => 10]));
// if ($queue_info[1] == 0) {
//     echo json_encode(['status' => 'failed', 'error' => 'Queue is empty']);
//     $channel->close();
//     $connection->close();
//     exit();
// }

if (!isset($_SESSION['id'], $_GET['batch'])) {
    echo json_encode(['error' => 'Access denied: user not logged in or batch ID missing']);
    exit();
}

$userId = $_SESSION['id'];
$batchId = $con->real_escape_string($_GET['batch']);

$query = "SELECT `status` FROM `batch` WHERE `id` = '{$batchId}' AND `user_id` = '{$userId}' LIMIT 1";
$result = $con->query($query);

if ($result) {
    if ($result->num_rows > 0) {
        $batch = $result->fetch_assoc();
        if ($batch['status'] == 'failed') {
            // show error and status
            echo json_encode(['status' => $batch['status'], 'error' => 'Batch failed']);
        } else {
            echo json_encode(['status' => $batch['status']]);
        }
    } else {
        echo json_encode(['error' => 'Batch not found or does not belong to the current user']);
    }
} else {
    echo json_encode(['error' => 'Error querying the database']);
}

// $channel->close();
// $connection->close();
$con->close();
