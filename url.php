<?php
session_start();
include 'include.php';

// make sure logged in
if (!isset($_SESSION['loggedin'])) {
    die("You must be logged in to access this page");
}

$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}

// get the url from table notifications where instance = $_SESSION[colab]
$sql = "SELECT ngrok_url FROM notifications WHERE instance = '" . $_SESSION['colab'] . "' ORDER BY created_at DESC LIMIT 1";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $ngrok_url = $row['ngrok_url'];
} else {
    $ngrok_url = "";
}

echo $ngrok_url;
