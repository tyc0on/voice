<?php

ini_set('session.cookie_lifetime', 60 * 60 * 24 * 365);

session_start();
if (@$_SESSION['id'] == 1) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

// if (@$_SESSION['id'] == 617) {
// 	ini_set('display_errors', 1);
// 	ini_set('display_startup_errors', 1);
// 	error_reporting(E_ALL);
// }
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// If the user is not logged in redirect to the login page...

include 'include.php';
include 'variables.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);
include 'config.php';

if ($con->connect_errno) {
	printf("connection failed: %s\n", $con->connect_error());
	exit();
}

// if session id = 617 print $_POST
// if (@$_SESSION['id'] == 617) {
// 	print_r($_POST);
// }
// die;


include 'auth.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$s3Client = new S3Client([
	'version' => 'latest',
	'region'  => 'sfo3',
	'endpoint' => 'https://sfo3.digitaloceanspaces.com',
	'credentials' => [
		'key'    => $spaces_key,
		'secret' => $spaces_secret,
	],
]);

if (!empty($_POST['existingFiles'])) {
	$existingFiles = $_POST['existingFiles'];
	foreach ($existingFiles as $existingFile) {
		$sql = "SELECT * FROM audio_files WHERE user_id = ? AND id = ?";
		$stmt = $con->prepare($sql);
		$stmt->bind_param('is', $_SESSION['id'], $existingFile);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		if ($result->num_rows > 0) {
			$audioFiles[] = $existingFile;
		}
	}
}

if (!empty($_FILES['files']['name'][0])) {
	foreach ($_FILES['files']['name'] as $key => $name) {
		$tmp_name = $_FILES['files']['tmp_name'][$key];
		$error = $_FILES['files']['error'][$key];
		$size = $_FILES['files']['size'][$key];
		$type = $_FILES['files']['type'][$key];
		$original_name = $name;
		$file_format = pathinfo($name, PATHINFO_EXTENSION);
		$file_path = 'audios/' . md5_file($tmp_name) . '.' . $file_format;
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
			move_uploaded_file($tmp_name, $file_path);
			$sql = "INSERT INTO audio_files (user_id, file_path, original_name, file_format) VALUES (?, ?, ?, ?)";
			$stmt = $con->prepare($sql);
			$stmt->bind_param('isss', $_SESSION['id'], $file_path, $original_name, $file_format);
			$stmt->execute();
			$audioid = $con->insert_id;
			$audioFiles[] = $audioid;
			$stmt->close();
		}

		try {

			$fileContent = file_get_contents($file_path);

			if ($fileContent === false) {
				throw new Exception("Failed to read file content");
			}

			$result = $s3Client->putObject([
				'Bucket' => 'voe',
				'Key'    => $file_path,
				'Body'   => $fileContent,
				'ACL'    => 'public-read'
			]);


			$file_url = $result->get('ObjectURL');

			$sql = "UPDATE audio_files SET file_url = ? WHERE id = ?";
			$stmt = $con->prepare($sql);
			$stmt->bind_param('si', $file_url, $audioid);
			$stmt->execute();
			$stmt->close();
		} catch (AwsException $e) {
			echo $e->getMessage();
			echo "\n";
		} catch (Exception $e) {
			echo $e->getMessage();
			echo "\n";
		}

		// remove file from server
		unlink($file_path);
	}
}

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
$sql = "SELECT * FROM files WHERE url = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('s', $_POST['name']);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$model_id = $row['id'];
} else {
	$model_id = 0;
}




$connection = new AMQPStreamConnection($sqlh, 5672, 'admintycoon', $rabbitp, 'voice');
$channel = $connection->channel();

// $channel->queue_declare('job_queue', false, true, false, false);
$channel->queue_declare('job_queue', false, true, false, false, false, new \PhpAmqpLib\Wire\AMQPTable(['x-max-priority' => 10]));

$pitches = array(-16, -12, -8, -4, 4, 8, 12, 16);

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
	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();


		// $url = "https://easyaivoice.com/" . $row['file_path'];
		$url = "https://voe.sfo3.cdn.digitaloceanspaces.com/" . $row['file_path'];
		$name = $_POST['name'];
		$pitch = $_POST['pitch'];
		// timestamp for job submission
		$timestamp = time();

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
			'timestamp' => $timestamp,
			'metadata' => array(
				'member' => array(
					'user' => array(
						'id' => $_SESSION['id'],
						'username' => 'None',
						'global_name' => 'None',
					)
				)
			)
		);



		// $msg = new AMQPMessage(json_encode($jobData));
		// if $url exists in files.url table give + 1 priority
		$sql = "SELECT * FROM files WHERE url = ?";
		$stmt = $con->prepare($sql);
		$stmt->bind_param('s', $url);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		if ($result->num_rows > 0) {
			$priority = 1;
		} else {
			$priority = 0;
		}

		if ($_SESSION['accounttype'] == "TRIAL") {
			$msg = new AMQPMessage(json_encode($jobData), ['priority' => 2 + $priority]);
		} elseif ($_SESSION['accounttype'] == "BASIC") {
			$msg = new AMQPMessage(json_encode($jobData), ['priority' => 3 + $priority]);
		} elseif ($_SESSION['accounttype'] == "ADVANCED") {
			$msg = new AMQPMessage(json_encode($jobData), ['priority' => 4 + $priority]);
		} else {
			$msg = new AMQPMessage(json_encode($jobData), ['priority' => 2 + $priority]);
		}


		$channel->basic_publish($msg, '', 'job_queue');
	}
}
$channel->close();
$connection->close();

// print_r($_SESSION);

// open in colab button
echo '';
include 'core/header.php';
?>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
	<!--begin::Content wrapper-->
	<div class="d-flex flex-column flex-column-fluid" style="background: url('/assets/media/misc/dc5251e3-c26b-434b-a449-19e48d6874c1.webp') repeat-y center top; background-size: 100% auto;">
		<!--begin::Toolbar-->

		<!--end::Toolbar-->
		<!--begin::Content-->
		<div id="kt_app_content" class="app-content flex-column-fluid" style="background: rgba(27, 27, 27, 0.5);">
			<!--begin::Content container-->
			<div id="kt_app_content_container" class="app-container container-xxl">
				<!--begin::Row-->
				<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
					<div style="text-align:center; margin-top:15px;">
						<img id="hero-image" src="/assets/media/misc/loading.webp" alt="loading" style="width: 480px; height: 480px;">
						<h1 id="hero-h" style="font-size:60px;">Processing your audio files....</h1>
						<div id="hero-load" class="spinner-border text-primary" style="width: 5rem; height: 5rem;" role="status">
							<span class="visually-hidden">Loading...</span>
						</div>
						<div id="countdown-timer" style="font-size:30px; margin-top:20px;">
							Next check in <span id="countdown-value">10</span> seconds.
						</div>

						<button id="manual-check" style="margin-top:20px;">Check Now</button>
						<div id="error-message" style="display:none;">
							<p style="color:red; font-size:48px; font-weight:bold;">An error occurred while processing your files.
							<div style="color:red; font-size:36px; font-weight:bold;" id="msg"></div>
							</p>
							<button onclick="location.href='<?php echo $_SESSION['return_url']; ?>'">Try Again</button> <span style="font-size:24px; ">or look for a new voice model <a href="https://voice-models.com">here</a></span>.
						</div>
						<h2 id="hero-sub"><?php
											$messages = array(
												"Hang tight! We're currently convincing a pack of sleepy electrons to move a little faster, negotiating a peace treaty between rival bandwidth tribes, and looking for the 'speed up' button we lost last week.", "Just a sec! Your request is jumping through hoops of fire, dodging lazy zeros and ones, and teaching an old computer new tricks. It's a tough job, but someone's gotta do it!", "Processing... Our code wizards are currently in a heated debate with a stubborn server gnome who loves taking long breaks. We've promised him an extra vacation day, so we should be back on track shortly!", "Brace yourself! We're wrangling the digital hamsters, negotiating with time-traveling tourists, and bribing the internet gremlins to speed things up. Hold on tight; your request is surfing through the cosmic internet waves!"
											);
											$message = $messages[array_rand($messages)];
											echo $message;
											?></h2>
					</div>
					<script type="text/javascript">
						(function() {
							var delay = 1000;
							var countdownValue = delay / 1000;
							var batchId = <?php echo json_encode($batch ?? 'null'); ?>;
							var startTime = Date.now();
							var oneHour = 3600000;
							var twentyFourHours = 86400000;
							var tenMinutes = 600000;

							function displayError(error_msg) {
								document.getElementById('error-message').style.display = 'block';
								document.getElementById('hero-h').style.display = 'none';
								document.getElementById('hero-load').style.display = 'none';
								document.getElementById('countdown-timer').style.display = 'none';
								document.getElementById('manual-check').style.display = 'none';
								document.getElementById('hero-sub').style.display = 'none';
								document.getElementById('hero-image').src = '/assets/media/misc/fail.jpg';
								document.getElementById('msg').textContent = error_msg || 'An unknown error occurred';

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
								resetCountdown(1000);
								checkBatchStatus();
							}

							function adjustDelay() {
								var currentTime = Date.now();
								var elapsedTime = currentTime - startTime;

								if (elapsedTime < tenMinutes) {
									// delay = Math.min(delay + 5000, 60000);
									delay = 3000;
								} else if (elapsedTime < oneHour) {
									delay = 15000;
								} else if (elapsedTime < twentyFourHours) {
									delay = 600000;
								} else {
									delay = null;
								}
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
											displayError(response.error);
										} else {
											adjustDelay();
											resetCountdown();
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

							document.getElementById('manual-check').addEventListener('click', manualCheck);

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
	<?php include 'core/footer.php';
	if ($model_id == 0) {
		// $scriptPath = __DIR__ . '/samplesmaker.php';
		// exec("/usr/bin/php $scriptPath");
		// $scriptPath = __DIR__ . '/samplesmaker2.php';
		// exec("/usr/bin/php $scriptPath");
		// include('samplesmaker.php');
		// include('samplesmaker2.php');
	}
	?>