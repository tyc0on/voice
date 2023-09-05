<?php
include('include.php');
$con = mysqli_connect($sqlh, $sqlu, $sqlp, $sqld);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

session_start();

// Clear the remember_token from the accounts table
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
    if ($stmt = $con->prepare('UPDATE accounts SET remember_token = NULL WHERE id = ?')) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();
    }

    // Delete all the associated tokens for that user from the remember_tokens table
    if ($stmt = $con->prepare('DELETE FROM remember_tokens WHERE user_id = ?')) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();
    }
}

session_destroy();

// Clear the remember_token cookie if it exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/'); // This will effectively remove the cookie
}

// Redirect to the login page:
header('Location: ./');
