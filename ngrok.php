<?php

session_start();
include('include-scriptease.php');
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);
if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}

$ngrok_url = $con->real_escape_string($_GET['u']);
$instance = $con->real_escape_string($_GET['inst']);

$sql = "INSERT INTO notifications (ngrok_url, instance) VALUES ('$ngrok_url', '$instance')";

if ($con->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $con->error;
}

$con->close();
