<?php

ini_set('session.cookie_lifetime', 60 * 60 * 24 * 365);

session_start();
if (@$_SESSION['id'] == 1) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
// If the user is not logged in redirect to the login page...

include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}


$loggedin = "false";
if (!isset($_SESSION['loggedin'])) {
    $_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
    // header('Location: ' . $rdir);

    // Check for the remember token
    if (isset($_COOKIE['remember_token'])) {
        $remember_token = $_COOKIE['remember_token'];
        if ($stmt = $con->prepare('SELECT id, accounttype, username, email, fullname, picture FROM accounts WHERE remember_token = ?')) {
            $stmt->bind_param('s', $remember_token);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $accounttype, $setusername, $email, $name, $picture);
                $stmt->fetch();
                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $name;
                $_SESSION['username'] = $setusername;
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $id;
                $_SESSION['accounttype'] = $accounttype;
                $_SESSION['picture'] = $picture;
                $loggedin = "true";
            }
            $stmt->close();
        }
    }

    // If the user is not logged in and the remember token doesn't exist, save the return URL
    if (!$loggedin) {
        $_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
        //if localhost fake login
        if ($_SERVER['HTTP_HOST'] == "localhost:5011") {
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = "mikem1@gmail.com";
            $_SESSION['accounttype'] = "Trial";
            $_SESSION['name'] = "Local User";
            $_SESSION['id'] = 1;
            $loggedin = "true";
        }
    }
    // exit;
} else {
    $loggedin = "true";
}
