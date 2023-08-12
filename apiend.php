<?php
include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}

$postData = $_POST;

$jsonData = json_encode($postData);
$sql = "INSERT INTO log (log) VALUES (?)";
$stmt = $con->prepare($sql);
$stmt->bind_param('s', $jsonData);
if ($stmt->execute()) {
    // echo 'Data inserted into the log table.';
} else {
    // echo 'Error: ' . $stmt->error;
}
$stmt->close();
$con->close();

// return JSON code success (200) with $postData
echo $jsonData;
