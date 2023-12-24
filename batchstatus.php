<?php
session_start();

include 'include.php';
include 'variables.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);
include 'config.php';

header('Content-Type: application/json'); // Set proper header for JSON response

if ($con->connect_errno) {
    echo json_encode(['error' => "Connection failed: " . $con->connect_error]);
    exit();
}

// Check if the user is logged in and the batch ID is provided
if (!isset($_SESSION['id'], $_GET['batch'])) {
    echo json_encode(['error' => 'Access denied: user not logged in or batch ID missing']);
    exit();
}

$userId = $_SESSION['id'];
$batchId = $con->real_escape_string($_GET['batch']);

// Query the database for the batch status
$query = "SELECT `status` FROM `batch` WHERE `id` = '{$batchId}' AND `user_id` = '{$userId}' LIMIT 1";
$result = $con->query($query);

// Check if the batch exists and belongs to the user
if ($result) {
    if ($result->num_rows > 0) {
        $batch = $result->fetch_assoc();
        echo json_encode(['status' => $batch['status']]);
    } else {
        echo json_encode(['error' => 'Batch not found or does not belong to the current user']);
    }
} else {
    echo json_encode(['error' => 'Error querying the database']);
}

$con->close();
