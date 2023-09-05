<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'include.php';
// Your MySQL database connection
$mysqli = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// Get user id from session, or set to null if not logged in
$userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;

// Get IP address
$ipAddress = $_SERVER['REMOTE_ADDR'];
$ipAddress2 = $_SERVER['HTTP_X_FORWARDED_FOR'];

// Get event details from POST data
$postData = json_decode(file_get_contents('php://input'), true);
$eventType = $postData['event_type'];
$page = $postData['page'];
$elementId = isset($postData['element_id']) ? $postData['element_id'] : null;
$monitorData = isset($postData['monitor_data']) ? $postData['monitor_data'] : null;

// Prepare an SQL statement for execution
$stmt = $mysqli->prepare("INSERT INTO analytics (event_type, page, user_id, ip_address, ip_address2, element_id, monitor_data) VALUES (?, ?, ?, ?, ?, ?, ?)");

// Bind variables to a prepared statement as parameters
$stmt->bind_param("ssissss", $eventType, $page, $userId, $ipAddress, $ipAddress2, $elementId, $monitorData);

// Execute the prepared statement
$stmt->execute();

// Close the prepared statement and the database connection
$stmt->close();
$mysqli->close();
