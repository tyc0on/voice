<?php

$loggedin = "false";
if (!isset($_SESSION['loggedin'])) {


    if (isset($_COOKIE['remember_token'])) {
        $remember_token = $_COOKIE['remember_token'];

        $userFound = false;
        if ($stmt = $con->prepare('SELECT id, accounttype, username, email, fullname, picture FROM accounts WHERE remember_token = ?')) {
            $stmt->bind_param('s', $remember_token);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $accounttype, $setusername, $email, $name, $picture);
                $stmt->fetch();
                $userFound = true;
            }
            $stmt->close();
        }

        if (!$userFound && $stmt = $con->prepare('SELECT a.id, a.accounttype, a.username, a.email, a.fullname, a.picture FROM accounts a JOIN remember_tokens rt ON a.id = rt.user_id WHERE rt.token = ?')) {
            $stmt->bind_param('s', $remember_token);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $accounttype, $setusername, $email, $name, $picture);
                $stmt->fetch();
                $userFound = true;
            }
            $stmt->close();
        }

        if ($userFound) {
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

        if ($loggedin == "false") {
            // $_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
            if ($_SERVER['HTTP_HOST'] == "localhost:5014") {
                $_SESSION['loggedin'] = true;
                $_SESSION['email'] = "mikem1@gmail.com";
                $_SESSION['accounttype'] = "Trial";
                $_SESSION['name'] = "Penis Vagina";
                $_SESSION['id'] = 1;
                $loggedin = "true";
            }
        }
    }
} else {
    $loggedin = "true";
}
