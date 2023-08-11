<?php
include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}

// $input = json_decode(file_get_contents('php://input'), true);

// if (isset($input['type']) && $input['type'] == 1) {
//     echo json_encode(['type' => 1]);
//     exit();
// }
$postData = $_POST;

// Convert $_POST data to JSON
$jsonData = json_encode($postData);

// Prepare the SQL statement
$sql = "INSERT INTO log (log) VALUES (?)";

// Prepare the statement
$stmt = $con->prepare($sql);

// Bind the parameter and execute the statement
$stmt->bind_param('s', $jsonData);

if ($stmt->execute()) {
    // Data inserted successfully
    echo 'Data inserted into the log table.';
} else {
    // Error inserting data
    echo 'Error: ' . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$con->close();
