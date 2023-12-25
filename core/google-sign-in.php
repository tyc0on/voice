<?php
// error_reporting(-1); // reports all errors
// ini_set("display_errors", "1"); // shows all errors
// ini_set("log_errors", 1);
// ini_set("error_log", "php-error.log");

// use sessions
session_start();

// include google API client
require_once "vendor/autoload.php";
include('include.php');
include('variables.php');


use SendGrid\Mail\Mail;

// Try and connect using the info above.
$con = mysqli_connect($sqlh, $sqlu, $sqlp, $sqld);
if (mysqli_connect_errno()) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// set google client ID
// $google_oauth_client_id = "";

// create google client object with client ID
$client = new Google_Client([
    'client_id' => $google_oauth_client_id
]);

// verify the token sent from AJAX
$id_token = $_POST["id_token"];

$payload = $client->verifyIdToken($id_token);
if ($payload && $payload['aud'] == $google_oauth_client_id) {
    // get user information from Google
    $user_google_id = $payload['sub'];

    $name = $payload["name"];
    $email = $payload["email"];
    $picture = $payload["picture"];

    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $ipAddress2 = $_SERVER['HTTP_X_FORWARDED_FOR'];


    // if user exists, login
    if ($stmt = $con->prepare('SELECT id, accounttype, username, picture, inst FROM accounts WHERE email = ?')) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();


        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $accounttype, $setusername, $existing_picture, $instid);
            $stmt->fetch();

            if ($existing_picture != $picture) {
                $picture_changed = true;
                $existing_picture = $picture; // Update the existing picture variable
            } else {
                $picture_changed = false;
            }

            // Account exists, now we verify the password.
            // Note: remember to use password_hash in your registration file to store the hashed passwords.
            // if (password_verify($_POST['password'], $password)) {
            // Verification success! User has loggedin!
            // Create sessions so we know the user is logged in, they basically act like cookies but remember the data on the server.
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $name;
            $_SESSION['username'] = $setusername;
            $_SESSION['email'] = $email;
            $_SESSION['id'] = $id;
            $_SESSION['accounttype'] = $accounttype;
            $_SESSION['picture'] = $picture;
            $_SESSION['colab'] = $instid;

            // First, update the picture column for the user
            if ($stmt = $con->prepare('UPDATE accounts SET picture = ? WHERE email = ?')) {
                $stmt->bind_param('ss', $picture, $email);
                $stmt->execute();
            }

            $remember_token = bin2hex(random_bytes(32));
            // Now, insert the new remember token into the remember_tokens table
            if ($stmt = $con->prepare('INSERT INTO remember_tokens (user_id, token) VALUES (?, ?)')) {
                $stmt->bind_param('is', $id, $remember_token); // Using $id here as it's the user's ID
                $stmt->execute();
                setcookie('remember_token', $remember_token, time() + 31536000, '/');
            }

            if ($picture_changed) {
                // if ($stmt = $con->prepare('UPDATE other_table SET picture = ? WHERE user_id = ?')) {
                //     $stmt->bind_param('si', $existing_picture, $id);
                //     $stmt->execute();
                // }
            }

            $userId = $id;
            $eventType = "user_signin";
            $page = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $elementId = null;
            $monitorData = "google_login";

            $mntr = $con->prepare("INSERT INTO analytics (event_type, page, user_id, ip_address, ip_address2, element_id, monitor_data) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $mntr->bind_param("ssissss", $eventType, $page, $userId, $ipAddress, $ipAddress2, $elementId, $monitorData);
            $mntr->execute();
            $mntr->close();
        } else {
            // Create New User, sign up
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";
            $rpass = substr(str_shuffle($chars), 0, 16);
            $password = password_hash($rpass, PASSWORD_DEFAULT);
            if ($stmt = $con->prepare('INSERT INTO accounts (fullname, password, email, ref, refcode, picture, registerip, registerip2) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')) {
                // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
                // $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                // $stmt->bind_param('sss', $name, $email, $password);
                $refcode = substr(hash('sha256', $email), 0, 8);
                $stmt->bind_param('ssssssss', $name, $password, $email, $_SESSION['ref'], $refcode, $picture, $ipAddress, $ipAddress2);

                $stmt->execute();
                $id = $con->insert_id;

                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $id;
                $_SESSION['accounttype'] = "TRIAL";
                $_SESSION['picture'] = $picture;

                // find an unused inst and assign to user
                // $stmt2 = $con->prepare('SELECT id FROM inst WHERE user_id IS NULL ORDER BY RAND() LIMIT 1');

                // $stmt2->execute();
                // $stmt2->store_result();

                // if ($stmt2->num_rows > 0) {
                //     $stmt2->bind_result($instid);
                //     $stmt2->fetch();

                //     $stmt2 = $con->prepare('UPDATE inst SET user_id = ? WHERE id = ?');
                //     $stmt2->bind_param('ii', $id, $instid);
                //     $stmt2->execute();
                // }
                // $stmt2->close();

                // // set session colab as inst id
                // $_SESSION['colab'] = $instid;

                $remember_token = bin2hex(random_bytes(32));
                if ($stmt = $con->prepare('INSERT INTO remember_tokens (user_id, token) VALUES (?, ?)')) {
                    $stmt->bind_param('is', $id, $remember_token); // Using $id here as it's the user's ID
                    $stmt->execute();
                    setcookie('remember_token', $remember_token, time() + 31536000, '/');
                }
                // SELECT id FROM inst WHERE user_id IS NULL ORDER BY RAND() LIMIT 1 

                $useremail = $email;

                // give starting credits
                // $stmt2 = $con->prepare('INSERT INTO credits (cuserid, camount, cnote) VALUES (?,?,?)');
                // $note = "join via google";
                // $deduct = 100;
                // $stmt2->bind_param('iis', $id, $deduct, $note);
                // $stmt2->execute();
                // $_SESSION['credits'] = $_SESSION['credits'] + $deduct;

                // if refered credit referrer
                // if ($_SESSION['ref'] != "") {
                //     $stmt2 = $con->prepare('SELECT id FROM accounts WHERE refcode = ? LIMIT 1');
                //     $stmt2->bind_param('s', $_SESSION['ref']);
                //     $stmt2->execute();
                //     //Find user id from referer to credit account
                //     $stmt2->store_result();
                //     if (!empty($stmt2->num_rows)) {
                //         $stmt2->bind_result($refid);
                //         $stmt2->fetch();

                //         $stmt2 = $con->prepare('INSERT INTO credits (cuserid, camount, cnote) VALUES (?,?,?)');
                //         $deduct = 50;
                //         $note = "refer " . $id;
                //         $stmt2->bind_param('iis', $refid, $deduct, $note);
                //         $stmt2->execute();
                //     }
                // }
                // $stmt2->close();

                // $email = new Mail();
                // $email->setFrom("hello@nocodelog.com", "Nocode Log");
                // $email->setSubject("New user for " . $sitename . " " . date("Y-m-d"));
                // $email->addTo("easyaistudio@gmail.com", "EasyAi Studio");
                // $email->addContent("text/plain", $useremail . " just signed up for " . $sitename . ", time to start coding.");
                // $email->addContent(
                //     "text/html",
                //     "<strong>" . $useremail . " just signed up for " . $sitename . ", time to start coding.</strong>"
                // );
                // $sendgrid = new \SendGrid($sendgrid_api);
                // try {
                //     $response = $sendgrid->send($email);
                //     print $response->statusCode() . "\n";
                //     print_r($response->headers());
                //     print $response->body() . "\n";
                // } catch (Exception $e) {
                //     echo 'Caught exception: ' .  $e->getMessage() . "\n";
                // }

                $userId = $id;
                $ipAddress = $_SERVER['REMOTE_ADDR'];
                $ipAddress2 = $_SERVER['HTTP_X_FORWARDED_FOR'];
                $eventType = "user_signup";
                $page = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                $elementId = null;
                $monitorData = "google_login";

                $mntr = $con->prepare("INSERT INTO analytics (event_type, page, user_id, ip_address, ip_address2, element_id, monitor_data) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $mntr->bind_param("ssissss", $eventType, $page, $userId, $ipAddress, $ipAddress2, $elementId, $monitorData);
                $mntr->execute();
                $mntr->close();



                //echo 'You have successfully registered, you can now login! <a href="login.php">Login</a>';
                // header('Location: ' . $goto);
            } else {
                // Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
                header("HTTP/1.1 500 Internal Server Error");
                //echo 'Could not prepare statement!';

            }
            // header("HTTP/1.1 500 Internal Server Error");
            //echo 'Incorrect email!';
        }

        $stmt->close();
    }
    // login the user
    $_SESSION["user"] = $user_google_id;

    // send the response back to client side
    // echo "Successfully logged in. " . $user_google_id . ", " . $name . ", " . $email . ", " . $picture;
} else {
    // token is not verified or expired
    // echo "Failed to login.";
    header("HTTP/1.1 500 Internal Server Error");
}
