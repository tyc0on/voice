<?php

require_once 'include.php';

$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

// Prepare statement
$stmt = $con->prepare("INSERT INTO inst (user_id) VALUES (?)");

// Insert 100 rows with NULL user_id
$null_value = NULL;

$stmt->bind_param("i", $null_value);

for ($i = 0; $i < 100; $i++) {
    $stmt->execute();
}

echo "100 rows inserted successfully.";

$stmt->close();
