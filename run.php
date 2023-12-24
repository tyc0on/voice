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
include 'variables.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);
include 'config.php';

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
	if ($loggedin == "false") {
		$_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
		//if localhost fake login
		if ($_SERVER['HTTP_HOST'] == "localhost:5011") {
			$_SESSION['loggedin'] = true;
			$_SESSION['email'] = "mikem1@gmail.com";
			$_SESSION['accounttype'] = "Trial";
			$_SESSION['name'] = "Local User";
			$_SESSION['id'] = 1;
			$loggedin = "true";
		} else {
			header('Location: /sign-in');
		}
	}
	// exit;
} else {
	$loggedin = "true";
}


// print_r($_SESSION);

// open in colab button
echo '';
include 'core/header.php';

$modelUrl = isset($_GET['url']) ? $_GET['url'] : 'https://huggingface.co/CxronaBxndit/Morgan-Freeman/resolve/main/Morgan-Freeman.zip';

// if no url then show message "No voice model selected, please visit voice-models.com and select a voice model"
if ($modelUrl == "") {
	echo '<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
	<div class="d-flex flex-column flex-column-fluid" style="background: url(\'1f1e371d-cb89-4735-b106-2f9c30de9be5.jpeg\') repeat-y center top; background-size: 100% auto;">
		<div id="kt_app_content" class="app-content flex-column-fluid">
			<div id="kt_app_content_container" class="app-container container-fluid">
				<h1>No voice model selected, please visit <a href="https://voice-models.com" target="_blank">voice-models.com</a> and select a voice model</h1>
			</div>
		</div>
	</div>
</div>';
} else {
?>
	<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
		<div class="d-flex flex-column flex-column-fluid" style="background: url('1f1e371d-cb89-4735-b106-2f9c30de9be5.jpeg') repeat-y center top; background-size: 100% auto;">
			<div id="kt_app_content" class="app-content flex-column-fluid">
				<div id="kt_app_content_container" class="app-container container-fluid">
					<form action="/process" method="post" enctype="multipart/form-data">
						<h1>Upload Audio File</h1>
						<h2>Selected Voice Model: <?php echo $modelUrl; ?></h2>
						<label for="audioFile">Upload Audio File (max 6 minutes):</label>
						<input type="file" name="files[]" accept="audio/*" required><br><br>
						<input type="hidden" name="name" value="<?php echo htmlspecialchars($modelUrl); ?>">
						<button type="submit" name="submit">Convert Audio</button>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php
}
?>




<div id="kt_app_footer" class="app-footer">
	<!--begin::Footer container-->
	<div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
		<!--begin::Copyright-->
		<div class="text-dark order-2 order-md-1">
			<span class="text-muted fw-semibold me-1">2023&copy;</span>
			<a href="https://easyaivoice.com" target="_blank" class="text-gray-800 text-hover-primary">EasyAIVoice</a>
		</div>
		<!--end::Copyright-->
		<!--begin::Menu-->
		<ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
			<li class="menu-item">
				<a href="https://easyaivoice.com" target="_blank" class="menu-link px-2">About</a>
			</li>
			<li class="menu-item">
				<a href="https://blog.easyai.studio/contact" target="_blank" class="menu-link px-2">Support</a>
			</li>
			<!-- footerlink -->
		</ul>
		<!--end::Menu-->
	</div>
	<!--end::Footer container-->
</div>
<!--end::Footer-->
<!-- <script src="assets/js/custom/documentation/forms/nouislider.js"></script> -->
<?php include 'core/footer.php'; ?>