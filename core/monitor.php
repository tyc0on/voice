<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'include.php';
$mysqli = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

$userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;

$ipAddress = $_SERVER['REMOTE_ADDR'];
$ipAddress2 = $_SERVER['HTTP_X_FORWARDED_FOR'];

$postData = json_decode(file_get_contents('php://input'), true);
$eventType = $postData['event_type'];
$page = $postData['page'];
$elementId = isset($postData['element_id']) ? $postData['element_id'] : null;
$monitorData = isset($postData['monitor_data']) ? $postData['monitor_data'] : null;

if (mb_strlen($monitorData) > 252) {
    $monitorData = mb_substr($monitorData, 0, 252) . "...";
}

$stmt = $mysqli->prepare("INSERT INTO analytics (event_type, page, user_id, ip_address, ip_address2, element_id, monitor_data) VALUES (?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("ssissss", $eventType, $page, $userId, $ipAddress, $ipAddress2, $elementId, $monitorData);

$stmt->execute();

$stmt->close();
$mysqli->close();
