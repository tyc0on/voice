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
	// $_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
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

// if post existingFiles not empty
if (!empty($_POST['existingFiles'])) {
	// get existingFiles
	$existingFiles = $_POST['existingFiles'];
	// foreach existingFiles check table audio_files that user_id == $_SESSION['id'] and get file_path
	foreach ($existingFiles as $existingFile) {
		$sql = "SELECT * FROM audio_files WHERE user_id = ? AND id = ?";
		$stmt = $con->prepare($sql);
		$stmt->bind_param('is', $_SESSION['id'], $existingFile);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		// if file_path exists
		if ($result->num_rows > 0) {
			// add to audioFiles
			$audioFiles[] = $existingFile;
		}
	}
}

// if files not empty 
if (!empty($_FILES['files']['name'][0])) {
	// foreach  add to audio_files table with user_id, file_path, original_name, file_format
	foreach ($_FILES['files']['name'] as $key => $name) {
		$tmp_name = $_FILES['files']['tmp_name'][$key];
		$error = $_FILES['files']['error'][$key];
		$size = $_FILES['files']['size'][$key];
		$type = $_FILES['files']['type'][$key];
		$original_name = $name;
		$file_format = pathinfo($name, PATHINFO_EXTENSION);
		$file_path = 'audios/' . md5_file($tmp_name) . '.' . $file_format;
		// if file_path already exists get id and add to audioFiles or insert into audio_files table and add to audioFiles
		if (file_exists($file_path)) {
			$sql = "SELECT * FROM audio_files WHERE file_path = ?";
			$stmt = $con->prepare($sql);
			$stmt->bind_param('s', $file_path);
			$stmt->execute();
			$result = $stmt->get_result();
			$stmt->close();
			if ($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				$audioFiles[] = $row['id'];
			} else {
				$sql = "INSERT INTO audio_files (user_id, file_path, original_name, file_format) VALUES (?, ?, ?, ?)";
				$stmt = $con->prepare($sql);
				$stmt->bind_param('isss', $_SESSION['id'], $file_path, $original_name, $file_format);
				$stmt->execute();
				$audioFiles[] = $con->insert_id;
				$stmt->close();
			}
		} else {
			// move file to audios folder
			move_uploaded_file($tmp_name, $file_path);
			// add to audio_files table with user_id, file_path, original_name, file_format
			$sql = "INSERT INTO audio_files (user_id, file_path, original_name, file_format) VALUES (?, ?, ?, ?)";
			$stmt = $con->prepare($sql);
			$stmt->bind_param('isss', $_SESSION['id'], $file_path, $original_name, $file_format);
			$stmt->execute();
			$audioFiles[] = $con->insert_id;
			$stmt->close();
		}
	}
}

//remove duplicates from audioFiles
$audioFiles = array_unique($audioFiles);

if (empty($audioFiles)) {
	die("No files.");
} elseif (count($audioFiles) > 10) {
	die("Too many files.");
	// } elseif (count($audioFiles) == 1) {
	//     $batch = 0;
} else {

	// if batch
	// add to batch table with user_id, status
	$sql = "INSERT INTO batch (user_id, status) VALUES (?, ?)";
	$stmt = $con->prepare($sql);
	$status = "pending";
	$stmt->bind_param('is', $_SESSION['id'], $status);
	$stmt->execute();
	$batch = $con->insert_id;
	$stmt->close();
}

// get model_id from files where POST name = files.name
$sql = "SELECT * FROM files WHERE name = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('s', $_POST['name']);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
// if model_id exists set model_id
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$model_id = $row['id'];
} else {
	// die("Model not found.");
	$model_id = 0;
}


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require 'vendor/autoload.php';

$connection = new AMQPStreamConnection($sqlh, 5672, 'admintycoon', $rabbitp, 'voice');
$channel = $connection->channel();

$channel->queue_declare('job_queue', false, true, false, false);

$pitches = array(-16, -12, -8, -4, 4, 8, 12, 16);

// while ($row = $result->fetch_assoc()) {
foreach ($audioFiles as $audioFile) {
	$sql = "INSERT INTO jobs (user_id, audio_id, model_id, pitch, status, batch_id) VALUES (?, ?, ?, ?, ?, ?)";
	$stmt = $con->prepare($sql);
	$status = "pending";
	$stmt->bind_param('iiissi', $_SESSION['id'], $audioFile, $model_id, $_POST['pitch'], $status, $batch);
	$stmt->execute();
	$job = $con->insert_id;
	$stmt->close();



	$sql = "SELECT * FROM audio_files WHERE id = ?";
	$stmt = $con->prepare($sql);
	$stmt->bind_param('i', $audioFile);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	// if file_path exists add to audioFiles
	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		// url is the url of the audio file to process
		$url = "https://easyaivoice.com/" . $row['file_path'];
		// $name = $row['name'];
		//model url
		$name = $_POST['name'];
		$pitch = $_POST['pitch'];

		$jobData = array(
			'audio_url' => $url,
			'voice_model' => $name,
			'type' => 'eav',
			'pitch' => $pitch,
			'job' => $job,
			'credits' => 99999999,
			'settings' => 'none',
			'guild_id' => 1,
			'channel_id' => 1,
			'message_id' => 1,
			'interaction_id' => 1,
			'interaction_token' => 'None',
			'interaction' => 1,
			'application_id' => 1,
			'metadata' => array(
				'member' => array(
					'user' => array(
						'id' => 1,
						'username' => 'None',
						'global_name' => 'None',
					)
				)
			)
		);

		$msg = new AMQPMessage(json_encode($jobData));
		$channel->basic_publish($msg, '', 'job_queue');
	}
	// die;
}
// }
$channel->close();
$connection->close();

// print_r($_SESSION);

// open in colab button
echo '';
include 'core/header.php';
?>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
	<!--begin::Content wrapper-->
	<div class="d-flex flex-column flex-column-fluid" style="background: url('1f1e371d-cb89-4735-b106-2f9c30de9be5.jpeg') repeat-y center top; background-size: 100% auto;">
		<!--begin::Toolbar-->

		<!--end::Toolbar-->
		<!--begin::Content-->
		<div id="kt_app_content" class="app-content flex-column-fluid">
			<!--begin::Content container-->
			<div id="kt_app_content_container" class="app-container container-xxl">
				<!--begin::Row-->
				<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
					<div style="text-align:center; margin-top:100px;">
						<img src="/assets/media/misc/loading.webp" alt="loading" style="width: 480px; height: 480px;">
						<h1 style="font-size:60px;">Processing your audio files....</h1>
						<div class="spinner-border text-primary" style="width: 5rem; height: 5rem;" role="status">
							<span class="visually-hidden">Loading...</span>
						</div>
						<div id="countdown-timer" style="font-size:30px; margin-top:20px;">
							Next check in <span id="countdown-value">10</span> seconds.
						</div>
						<button id="manual-check" onclick="manualCheck()" style="margin-top:20px;">Check Now</button>
						<div id="error-message" style="display:none;">
							<p style="color:red; font-size:24px;">An error occurred while processing your files.</p>
							<button onclick="location.href='<?php echo $_SESSION['return_url']; ?>'">Return</button>
						</div>
					</div>
					<script type="text/javascript">
						(function() {
							var delay = 1000;
							var countdownValue = delay / 1000;
							var batchId = <?php echo json_encode($batch ?? 'null'); ?>;

							function displayError() {
								document.getElementById('error-message').style.display = 'block';
							}

							function updateCountdown() {
								document.getElementById('countdown-value').textContent = countdownValue;
								if (countdownValue > 0) {
									countdownValue--;
									setTimeout(updateCountdown, 1000);
								}
							}

							function resetCountdown(newDelay) {
								delay = newDelay !== undefined ? newDelay : delay;
								countdownValue = delay / 1000;
								updateCountdown();
							}

							function manualCheck() {
								resetCountdown(1000); // Reset the delay to 1000 milliseconds
								checkBatchStatus();
							}

							function checkBatchStatus() {
								var xhr = new XMLHttpRequest();
								xhr.open('GET', '/batchstatus.php?batch=' + batchId, true);
								xhr.onload = function() {
									if (xhr.status === 200) {
										var response = JSON.parse(xhr.responseText);
										if (response.status === 'complete') {
											window.location.href = '/processed?batch=' + batchId;
										} else if (response.error) {
											displayError();
										} else {
											delay *= 2;
											resetCountdown(); // Use the increased delay for automatic checks
											setTimeout(checkBatchStatus, delay);
										}
									} else {
										console.error('Error checking batch status');
										displayError();
									}
								};
								xhr.onerror = function() {
									console.error('Network error trying to check batch status');
									displayError();
								};
								xhr.send();
							}

							setTimeout(checkBatchStatus, delay);
							updateCountdown();
						})();
					</script>

				</div>



				<!-- <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 mt-0">

						<h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
							Step 1:
						</h1>

					</div> -->



			</div>



			<!--end::Content container-->
		</div>
		<!--end::Content-->
	</div>
	<!--end::Content wrapper-->
	<!--begin::Footer-->
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