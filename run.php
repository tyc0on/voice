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
					<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
						<form class="form mt-15" action="/process" method="post" enctype="multipart/form-data" id="audios">
							<h1>Upload Audio File</h1>
							<h2>Selected Voice Model: <?php echo $modelUrl; ?></h2>
							<div class="fv-row">
								<input type="file" name="files[]" id="fileInput" multiple style="display: none;">
								<div class="custom-file-upload" style="border: 1px dashed #9b00ff; background-color: #000000; padding: 10px; text-align: center; cursor: pointer;">
									<i class="ki-duotone ki-file-up fs-3x text-primary"></i>
									<div class="ms-4 pb-5">
										<h3 class="fs-3 fw-bold text-gray-900 mb-1">Drop files here or click to upload.</h3>
										<span class="fs-7 fw-semibold text-gray-400" id="fileNames">Upload your voice files here</span>
									</div>
								</div>
								<button type="button" id="pickExistingFiles" class="btn btn-success mt-3">Pick from existing audio files</button>
								<select multiple id="existingFilesDropdown" style="display:none;"><?php
																									$sql = "SELECT * FROM audio_files WHERE user_id = ? ORDER BY id DESC";
																									$stmt = $con->prepare($sql);
																									$stmt->bind_param('i', $_SESSION['id']);
																									$stmt->execute();
																									$result = $stmt->get_result();

																									while ($row = $result->fetch_assoc()) {
																										$filepath = str_replace("audios/", "", $row['file_path']);
																										$audioFiles[] = ['id' => $row['id'], 'file_name' => $row['original_name']];
																									}

																									$stmt->close();

																									foreach ($audioFiles as $audioFile) {
																										echo "<option value='{$audioFile['id']}'>{$audioFile['file_name']}</option>";
																									}
																									?></select>
							</div>
							<input type="hidden" name="name" value="<?php echo htmlspecialchars($modelUrl); ?>">
							<button class="btn btn-primary mt-3" type="submit" name="submit">Submit -></button>
							<div class="fv-row">
								<button type="button" id="toggleAdvancedSettings" class="btn btn-secondary mt-3">Advanced Settings</button>
								<div id="advancedSettings" style="display: none;">
									<h3>Advanced settings</h3>
									<div class="fv-row">
										<label for="pitch">Pitch:</label>
										<input type="number" id="pitch" name="pitch" value="0" min="-100" max="100">
									</div>
								</div>
							</div>
						</form>

						<script>
							// JavaScript to toggle the advanced settings visibility
							document.getElementById('toggleAdvancedSettings').addEventListener('click', function() {
								var advSettings = document.getElementById('advancedSettings');
								if (advSettings.style.display === 'none') {
									advSettings.style.display = 'block';
								} else {
									advSettings.style.display = 'none';
								}
							});
						</script>

						<script>
							document.querySelector(".custom-file-upload").addEventListener("click", function() {
								document.getElementById("fileInput").click();
							});

							document.getElementById("fileInput").addEventListener("change", function() {
								const selectedFiles = Array.from(this.files);
								const fileNames = selectedFiles.map(file => file.name).join(", ");
								document.getElementById("fileNames").textContent = fileNames;
							});

							const dropzone = document.querySelector(".custom-file-upload");
							dropzone.addEventListener("dragover", function(e) {
								e.preventDefault();
								this.style.backgroundColor = "#222222";
							});

							dropzone.addEventListener("dragleave", function(e) {
								e.preventDefault();
								this.style.backgroundColor = "#000000";
							});

							dropzone.addEventListener("drop", function(e) {
								e.preventDefault();
								this.style.backgroundColor = "#000000";
								const files = e.dataTransfer.files;
								document.getElementById("fileInput").files = files;
								const fileNames = Array.from(files).map(file => file.name).join(", ");
								document.getElementById("fileNames").textContent = fileNames;
							});

							document.getElementById('pickExistingFiles').addEventListener('click', function() {
								const dropdown = document.getElementById('existingFilesDropdown');
								dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
							});

							document.getElementById('existingFilesDropdown').addEventListener('change', function() {
								const selectedOptions = Array.from(this.selectedOptions);
								const form = document.getElementById('audios');

								// Clear any previously appended inputs related to existing files
								document.querySelectorAll('.existing-file-input').forEach(input => {
									input.remove();
								});

								selectedOptions.forEach(option => {
									const input = document.createElement('input');
									input.type = 'hidden';
									input.name = 'existingFiles[]';
									input.value = option.value;
									input.classList.add('existing-file-input');
									form.appendChild(input);
								});

								const selectedFileNames = selectedOptions.map(opt => opt.textContent).join(', ');
								document.getElementById('fileNames').textContent = selectedFileNames;
							});
						</script>
					</div>
					<!-- <form action="/process" method="post" enctype="multipart/form-data">
						<h1>Upload Audio File</h1>
						<h2>Selected Voice Model: <?php echo $modelUrl; ?></h2>
						<label for="audioFile">Upload Audio File (max 6 minutes):</label>
						<input type="file" name="files[]" accept="audio/*" required><br><br>
						<input type="hidden" name="name" value="<?php echo htmlspecialchars($modelUrl); ?>">
						<button type="submit" name="submit">Convert Audio</button>
					</form> -->
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