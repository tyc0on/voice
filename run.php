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


if ($con->connect_errno) {
	printf("connection failed: %s\n", $con->connect_error());
	exit();
}


$_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
include 'auth.php';
include 'config.php';
include 'core/header.php';

$modelUrl = isset($_GET['url']) ? $_GET['url'] : 'https://huggingface.co/CxronaBxndit/Morgan-Freeman/resolve/main/Morgan-Freeman.zip';

// if no url then show message "No voice model selected, please visit voice-models.com and select a voice model"
if ($modelUrl == "") {
	echo '<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
	<div class="d-flex flex-column flex-column-fluid" style="background: url(\'/assets/media/misc/dc5251e3-c26b-434b-a449-19e48d6874c1.webp\') repeat-y center top; background-size: 100% auto;">
		<div id="kt_app_content" class="app-content flex-column-fluid" style="background: rgba(27, 27, 27, 0.5);">
			<div id="kt_app_content_container" class="app-container container-fluid">
				<h1>No voice model selected, please visit <a href="https://voice-models.com" target="_blank">voice-models.com</a> and select a voice model</h1>
			</div>
		</div>
	</div>
</div>';
} else {
?>
	<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
		<div class="d-flex flex-column flex-column-fluid" style="background: url('/assets/media/misc/dc5251e3-c26b-434b-a449-19e48d6874c1.webp') repeat-y center top; background-size: 100% auto;">
			<div id="kt_app_content" class="app-content flex-column-fluid" style="background: rgba(27, 27, 27, 0.5);">
				<div id="kt_app_content_container" class="app-container container-xxl pt-10">

					<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
						<div class="col-md-4 text-center mt-5">
							<div class="card" id="step1-card" style="cursor: pointer;">
								<div class="card-body p-0 pt-4">
									<h1 class="fw-bold">Step 1:</h1>
									<p class="fs-4">Pick a Voice</p>
								</div>
							</div>
						</div>
						<div class="col-md-4 text-center mt-5">
							<div class="card border-primary">
								<div class="card-body p-0 pt-4">
									<h1 class="fw-bold text-primary">Step 2:</h1>
									<p class="fs-4">Upload Audio Files</p>
								</div>
							</div>
						</div>
						<div class="col-md-4 text-center mt-5">
							<div class="card">
								<div class="card-body p-0 pt-4 text-muted">
									<h1 class="fw-bold text-muted">Step 3:</h1>
									<p class="fs-4">Download</p>
								</div>
							</div>
						</div>
					</div>
					<script>
						document.addEventListener("DOMContentLoaded", function() {
							var step1Card = document.getElementById('step1-card');

							step1Card.addEventListener('click', function() {
								window.location.href = '/app';
							});
						});
					</script>
					<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
						<form class="form mt-5" action="/process" method="post" enctype="multipart/form-data" id="audios">
							<h1 style="font-weight:bold;">Upload Audio File</h1>

							<?php

							// check in files table that url has not been set to 0
							$sql = "SELECT * FROM files WHERE url = ? AND active = 0 LIMIT 1";
							$stmt = $con->prepare($sql);
							$stmt->bind_param('s', $modelUrl);
							$stmt->execute();
							$result = $stmt->get_result();
							$stmt->close();

							if ($result->num_rows === 0) {

							?>
								<h2 style="font-weight:normal;">Selected Voice Model: <?php echo $modelUrl; ?></h2>
								<div class="fv-row">
									<input type="file" name="files[]" id="fileInput" multiple style="display: none;">
									<div class="custom-file-upload rounded-3" style="border: 1px dashed #9b00ff; background-color: #000000; padding: 10px; text-align: center; cursor: pointer;">
										<i class="ki-duotone ki-file-up fs-3x text-primary"></i>
										<div class="ms-4 pb-5">
											<h3 class="fs-1 fw-bold text-gray-900 mb-1">Drop files here or click to upload.</h3>
											<span class="fs-2 fw-semibold text-gray-600" id="fileNames">Upload your voice files here</span>
										</div>
									</div>
									<button type="button" id="pickExistingFiles" class="btn btn-success mt-3">Pick from existing audio files</button>
									<div id="existingFilesDropdown2" style="display:none;" class="pt-3">
										<select id="existingFilesDropdown" class="form-select form-select-lg form-select-solid border rounded-3 border-primary" data-control="select2" data-close-on-select="true" data-placeholder="Click to select your audio files" data-allow-clear="true" multiple="multiple"><?php
																																																																													if (is_numeric($_SESSION['id'])) {
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
																																																																													}
																																																																													?></select>
									</div>
								</div>
								<input type="hidden" name="name" value="<?php echo htmlspecialchars($modelUrl); ?>">
								<button class="btn btn-primary mt-3" type="submit" name="submit">Submit -></button>
								<div class="fv-row">
									<button type="button" id="toggleAdvancedSettings" class="btn btn-secondary mt-3">Advanced Settings</button>
									<div id="advancedSettings" class="pt-3">

										<div class="fv-row mb-3" style="width:100px">
											<label for="pitch" class="form-label">Pitch:</label>
											<?php
											$pitch = 0;

											if (isset($_GET['pitch'])) {
												$pitchValue = filter_var($_GET['pitch'], FILTER_VALIDATE_INT, [
													"options" => ["min_range" => -100, "max_range" => 100]
												]);

												if ($pitchValue !== false) {
													$pitch = $pitchValue;
												}
											}
											?>
											<input type="number" id="pitch" name="pitch" value="<?php echo $pitch; ?>" min="-100" max="100" class="form-control">
										</div>
										<!-- <div class="fv-row">
										<label for="removeAccent" class="form-check-label text-dark">Remove Model's Accent:</label>
										<input type="checkbox" id="removeAccent" name="removeAccent" class="form-check-input">
									</div> -->
									</div>
								</div>
							<?php
							} else {
								echo '<h2 class="text-warning">Selected Voice Model has been removed</h2>';
							} ?>
						</form>

						<script>
							// $(document).ready(function() {
							// 	$('#existingFilesDropdown').select2();
							// });

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
							function isValidFileType(fileName) {
								const validExtensions = ['.wav', '.mp3', '.ogg', '.flac', '.m4a', '.aac', '.wma', '.mov'];
								return validExtensions.some(ext => fileName.endsWith(ext));
							}

							document.querySelector(".custom-file-upload").addEventListener("click", function() {
								document.getElementById("fileInput").click();
							});

							document.getElementById("fileInput").addEventListener("change", function() {
								const selectedFiles = Array.from(this.files);
								const validFiles = selectedFiles.filter(file => isValidFileType(file.name));

								if (validFiles.length !== selectedFiles.length) {
									alert("Some files have been removed because they are not of the allowed types.");
								}

								const fileNames = validFiles.map(file => file.name).join(", ");
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
								const validFiles = Array.from(files).filter(file => isValidFileType(file.name));

								if (validFiles.length !== files.length) {
									alert("Some files have been removed because they are not of the allowed types.");
								}

								let dataTransfer = new DataTransfer();
								for (let file of validFiles) {
									dataTransfer.items.add(file);
								}

								document.getElementById("fileInput").files = dataTransfer.files;
								document.getElementById("fileInput").dispatchEvent(new Event('change'));
							});

							document.getElementById('pickExistingFiles').addEventListener('click', function() {
								const dropdown = document.getElementById('existingFilesDropdown2');
								dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
							});

							document.getElementById('audios').addEventListener('submit', function(e) {
								const fileInput = document.getElementById('fileInput');
								const existingFilesDropdown = $('#existingFilesDropdown').select2('data');

								if (fileInput.files.length === 0 && existingFilesDropdown.length === 0) {
									e.preventDefault();
									alert('Please select at least one file to upload.');
								} else {
									existingFilesDropdown.forEach(function(item) {
										const input = document.createElement('input');
										input.type = 'hidden';
										input.name = 'existingFiles[]';
										input.value = item.id;
										document.getElementById('audios').appendChild(input);
									});
								}

								if (fileInput.files.length === 0 && existingFilesDropdown.selectedOptions.length === 0) {
									e.preventDefault();
									alert('Please select at least one file to upload.');
								}
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
<?php include 'layout/modals/_login.php'; ?>
<!--end::Footer-->
<!-- <script src="assets/js/custom/documentation/forms/nouislider.js"></script> -->
<?php include 'core/footer.php'; ?>