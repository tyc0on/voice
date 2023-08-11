<?php
include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['type']) && $input['type'] == 1) {
    echo json_encode(['type' => 1]);
    exit();
}


// Check if the POST request is not empty
if (!empty($_POST)) {

    // Capture all $_POST data
    $logData = json_encode($_POST);

    // Prepare a SQL statement to insert the data into the apitest table
    $stmt = $con->prepare("INSERT INTO apitest (log, created_at) VALUES (?, NOW())");

    // Bind the log data to the prepared statement
    $stmt->bind_param('s', $logData);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Data logged successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "No POST data to log.";
}
