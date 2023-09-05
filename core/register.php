<?php
session_start();
// Where to send on successful login.
include('variables.php');
$goto = $siteapp;

// Change this to your connection info.
include 'include.php';
require 'vendor/autoload.php';

use SendGrid\Mail\Mail;

$DATABASE_HOST = $sqlh;
$DATABASE_USER = $sqlu;
$DATABASE_PASS = $sqlp;
$DATABASE_NAME = $sqld;
// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Now we check if the data was submitted, isset() function will check if the data exists.
if (!isset($_POST['password'], $_POST['email'])) {
	// Could not get the data that should have been sent.
	exit('Please complete the registration form!');
}
// Make sure the submitted registration values are not empty.
if (empty($_POST['password']) || empty($_POST['email'])) {
	// One or more values are empty.
	exit('Please complete the registration form');
}


if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	exit('Email is not valid!');
}
if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 1) {
	exit('Password must be between 1 and 20 characters long!');
}

// We need to check if the account with that email exists.
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE email = ? OR username = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
	$stmt->bind_param('ss', $_POST['email'], $_POST['name']);
	$stmt->execute();
	$stmt->store_result();
	// Store the result so we can check if the account exists in the database.
	if ($stmt->num_rows > 0) {
		// email already exists
		echo 'Email or Username exists, please choose another!';
	} else {
		// Username doesnt exists, insert new account
		// print_r($_POST);
		// die;

		if ($stmt = $con->prepare('INSERT INTO accounts (username, password, email, ref, refcode) VALUES (?, ?, ?, ?, ?)')) {
			// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
			$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
			$refcode = substr(hash('sha256', $_SESSION['email']), 0, 8);
			$stmt->bind_param('sssss', $_POST['name'], $password, $_POST['email'], $_SESSION['ref'], $refcode);
			$stmt->execute();
			$id = $con->insert_id;

			session_regenerate_id();
			$_SESSION['loggedin'] = TRUE;
			$_SESSION['username'] = $_POST['name'];
			$_SESSION['email'] = $_POST['email'];
			$_SESSION['id'] = $id;
			$_SESSION['accounttype'] = "TRIAL";

			$remember_token = bin2hex(random_bytes(32));
			if ($stmtToken = $con->prepare('INSERT INTO remember_tokens (user_id, token) VALUES (?, ?)')) {
				$stmtToken->bind_param('is', $id, $remember_token);
				$stmtToken->execute();
				setcookie('remember_token', $remember_token, time() + 31536000, '/');
				$stmtToken->close();
			}

			$useremail = $_POST['email'];

			// give starting credits
			// $stmt2 = $con->prepare('INSERT INTO credits (cuserid, camount, cnote) VALUES (?,?,?)');
			// $note = "join via sign-up";
			// $deduct = 50;
			// $stmt2->bind_param('iis', $id, $deduct, $note);
			// $stmt2->execute();
			// $_SESSION['credits'] = $_SESSION['credits'] + $deduct;
			// $claimed = "true";

			// if refered credit referrer
			// if ($_SESSION['ref'] != "") {
			// 	$stmt2 = $con->prepare('SELECT id FROM accounts WHERE refcode = ? LIMIT 1');
			// 	$stmt2->bind_param('s', $_SESSION['ref']);
			// 	$stmt2->execute();
			// 	//Find user id from referer to credit account
			// 	$stmt2->store_result();
			// 	if (!empty($stmt2->num_rows)) {
			// 		$stmt2->bind_result($refid);
			// 		$stmt2->fetch();

			// 		$stmt2 = $con->prepare('INSERT INTO credits (cuserid, camount, cnote) VALUES (?,?,?)');
			// 		$deduct = 50;
			// 		$note = "refer " . $id;
			// 		$stmt2->bind_param('iis', $refid, $deduct, $note);
			// 		$stmt2->execute();
			// 	}
			// }
			// $stmt2->close();


			$email = new Mail();
			$email->setFrom("hello@nocodelog.com", "Nocode Log");
			$email->setSubject("New user for " . $sitename . " " . date("Y-m-d"));
			$email->addTo("easyaistudio@gmail.com", "EasyAi Studio");
			$email->addContent("text/plain", $useremail . " just signed up for " . $sitename . ", time to start coding.");
			$email->addContent(
				"text/html",
				"<strong>" . $useremail . " just signed up for " . $sitename . ", time to start coding.</strong>"
			);
			$sendgrid = new \SendGrid($sendgrid_api);
			try {
				$response = $sendgrid->send($email);
				print $response->statusCode() . "\n";
				print_r($response->headers());
				print $response->body() . "\n";
			} catch (Exception $e) {
				echo 'Caught exception: ' .  $e->getMessage() . "\n";
			}
			//echo 'You have successfully registered, you can now login! <a href="login.php">Login</a>';
			header('Location: ' . $goto);
		} else {
			// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
			echo 'Could not prepare statement!';
		}
	}
	$stmt->close();
} else {
	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
	echo 'Could not prepare statement!';
}
$con->close();
