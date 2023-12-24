<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();
// Where to send on successful login.
include('variables.php');
$goto = $siteapp;
// Change this to your connection info.
include 'include.php';

// Try and connect using the info above.
$con = mysqli_connect($sqlh, $sqlu, $sqlp, $sqld);

if (mysqli_connect_errno()) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if (!isset($_POST['email'], $_POST['password'])) {
	// Could not get the data that should have been sent.
	exit('Please fill both the email and password fields!');
}

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $con->prepare('SELECT id, password, accounttype, username FROM accounts WHERE email = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the email is a string so we use "s"
	$stmt->bind_param('s', $_POST['email']);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$stmt->store_result();


	if ($stmt->num_rows > 0) {
		$stmt->bind_result($id, $password, $accounttype, $fullname);
		$stmt->fetch();
		// Account exists, now we verify the password.
		// Note: remember to use password_hash in your registration file to store the hashed passwords.
		if (password_verify($_POST['password'], $password)) {
			// Verification success! User has loggedin!
			// Create sessions so we know the user is logged in, they basically act like cookies but remember the data on the server.
			session_regenerate_id();
			$_SESSION['loggedin'] = TRUE;
			$_SESSION['username'] = $fullname;
			$_SESSION['email'] = $_POST['email'];
			$_SESSION['id'] = $id;
			$_SESSION['accounttype'] = $accounttype;

			$_POST['remember_me'] = 'on';
			if (isset($_POST['remember_me']) && $_POST['remember_me'] == 'on') {
				$remember_token = bin2hex(random_bytes(32));

				if ($stmtToken = $con->prepare('INSERT INTO remember_tokens (user_id, token) VALUES (?, ?)')) {
					$stmtToken->bind_param('is', $id, $remember_token);
					$stmtToken->execute();
					setcookie('remember_token', $remember_token, time() + 31536000, '/');
					$stmtToken->close();
				}
			}
			// calculate credits
			// if ($stmt2 = $con->prepare('SELECT camount FROM credits WHERE cuserid = ?')) {
			// 	// Bind parameters (s = string, i = int, b = blob, etc), in our case the email is a string so we use "s"
			// 	$stmt2->bind_param('i', $id);
			// 	$stmt2->execute();
			// 	// Store the result so we can check if the account exists in the database.
			// 	$stmt2->store_result();
			// 	$total = 0;
			// 	if (!empty($stmt2->num_rows)) {
			// 		for ($i = 0; $i < $stmt2->num_rows; $i++) {
			// 			$stmt2->bind_result($camount);
			// 			$stmt2->fetch();
			// 			$total += $camount;
			// 		}
			// 	}
			// 	$_SESSION['credits'] =  $total;
			// 	$stmt2->close();
			// }

			// if isset session return_url set goto to that and unset return_url
			if (isset($_SESSION['return_url'])) {
				$goto = $_SESSION['return_url'];
				unset($_SESSION['return_url']);
			}

			header('Location: ' . $goto);
		} else {
			echo 'Incorrect password!';
		}
	} else {
		echo 'Incorrect email!';
	}
	$stmt->close();
}
