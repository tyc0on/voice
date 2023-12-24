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

	if (isset($_COOKIE['remember_token'])) {
		$remember_token = $_COOKIE['remember_token'];

		// First, check the old remember_token column in the accounts table
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

		// If not found in the old method, check the new remember_tokens table
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
		} else {
			// user not logged in, redirect so sign-in
			header('Location: /sign-in');
			exit;
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
?>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
	<!--begin::Content wrapper-->
	<div class="d-flex flex-column flex-column-fluid" style="background: url('/assets/media/misc/dc5251e3-c26b-434b-a449-19e48d6874c1.webp') repeat-y center top; background-size: 100% auto;">
		<!--begin::Toolbar-->

		<!--end::Toolbar-->
		<!--begin::Content-->
		<div id="kt_app_content" class="app-content flex-column-fluid">
			<!--begin::Content container-->
			<div id="kt_app_content_container" class="app-container container-xxl">
				<!--begin::Row-->
				<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
					<!--begin::Form-->
					<div style="text-align:center; margin-top:100px;">
						<h1 style="font-size:60px;">Upload your voice file</h1>
						<h2>MP3 or WAV</h2>
					</div>
					<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 mt-0">
						<!--begin::Title-->
						<h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
							Step 1:
						</h1>
						<!--end::Title-->

					</div>



				</div>
				<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
					<form class="form mt-5" action="processing.php" method="post" enctype="multipart/form-data" id="audios">
						<div class="fv-row">
							<input type="file" name="files[]" id="fileInput" multiple style="display: none;">
							<div class="custom-file-upload" style="border: 1px dashed #9b00ff; background-color: #000000; padding: 10px; text-align: center; cursor: pointer;">
								<i class="ki-duotone ki-file-up fs-3x text-primary"></i>
								<div class="ms-4 pb-5">
									<h3 class="fs-3 fw-bold text-gray-900 mb-1">Drop files here or click to upload.</h3>
									<span class="fs-7 fw-semibold text-gray-400" id="fileNames">Upload your voice files here</span>
								</div>
							</div>
							<button type="button" id="pickExistingFiles" class="btn btn-primary mt-3">Pick from existing audio files</button>
							<select multiple id="existingFilesDropdown" style="display:none;"><?php

																								$sql = "SELECT * FROM audio_files WHERE user_id = ? ORDER BY id DESC";
																								$stmt = $con->prepare($sql);
																								$stmt->bind_param('i', $_SESSION['id']);
																								$stmt->execute();
																								$result = $stmt->get_result();

																								// Assuming there's a field 'name' in the 'batch' table to display as the menu title.
																								// If the field name is different, replace 'name' with the appropriate field name.
																								while ($row = $result->fetch_assoc()) {
																									$filepath = str_replace("audios/", "", $row['file_path']);
																									$audioFiles[] = ['id' => $row['id'], 'file_name' => $row['original_name']];
																								}

																								$stmt->close();
																								// $audioFiles = [
																								// 	['id' => 1, 'file_name' => 'audio1.mp3'],
																								// 	['id' => 2, 'file_name' => 'audio2.mp3'],
																								// 	['id' => 3, 'file_name' => 'audio3.mp3'],
																								// 	['id' => 4, 'file_name' => 'audio4.mp3'],
																								// 	['id' => 5, 'file_name' => 'audio5.mp3'],
																								// 	// ... more files
																								// ];

																								// echo "<option value=''>Select files</option>";
																								foreach ($audioFiles as $audioFile) {
																									echo "<option value='{$audioFile['id']}'>{$audioFile['file_name']}</option>";
																								}
																								?></select>
						</div>
					</form>
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


				<!--end::Row-->
				<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 ">
					<!--begin::Title-->
					<h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0 mb-5">
						Step 2:
					</h1>
					<!--end::Title-->

				</div>





				<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
					<!--begin::Col-->
					<div class="col-xl-12">


						<!--begin::Table-->
						<div class="card card-flush mt-6 mt-xl-9">
							<!--begin::Card header-->
							<div class="card-header mt-5">
								<!--begin::Card title-->
								<div class="card-title flex-column">
									<h3 class="fw-bold mb-1">Pick a voice</h3>
									<div class="fs-6 text-gray-400">Updated 37 minutes ago</div>
								</div>
								<!--begin::Card title-->
								<!--begin::Card toolbar-->
								<div class="card-toolbar my-1 w-75 d-flex justify-content-end">
									<div class="me-4 my-1"><span class="fw-bold fs-4 mt-1 me-2">Set Pitch</span>
										<span class="fw-bold fs-3x" id="kt_modal_create_campaign_budget_label"></span>
									</div>
									<div class="me-4 my-1 ps-4 pe-4 w-25">
										<div id="kt_modal_create_campaign_budget_slider" class="noUi-sm"></div>

									</div>
									<!--begin::Select-->
									<div class="me-6 my-1">
										<select id="kt_filter_year" name="year" data-control="select2" data-hide-search="true" class="w-125px form-select form-select-sm">
											<option value="All" selected="selected">Gender</option>
											<option value="thisyear">Male</option>
											<option value="thismonth">Female</option>
											<option value="lastmonth">Other</option>
										</select>
									</div>
									<!--end::Select-->
									<!--begin::Select-->
									<div class="me-4 my-1">
										<select id="kt_filter_orders" name="orders" data-control="select2" data-hide-search="true" class="w-125px form-select form-select-sm">
											<option value="All" selected="selected">Accent</option>
											<option value="Approved">Approved</option>
											<option value="Declined">Declined</option>
											<option value="In Progress">In Progress</option>
											<option value="In Transit">In Transit</option>
										</select>
									</div>
									<!--end::Select-->
									<!--begin::Search-->
									<div class="d-flex align-items-center position-relative my-1">
										<i class="ki-duotone ki-magnifier fs-3 position-absolute ms-3">
											<span class="path1"></span>
											<span class="path2"></span>
										</i>
										<input type="text" id="kt_filter_search" class="form-control w-150px fs-7 ps-9" placeholder="Search Voices" />
									</div>
									<!--end::Search-->
								</div>
								<!--begin::Card toolbar-->
							</div>
							<!--end::Card header-->
							<!--begin::Card body-->
							<div class="card-body pt-0">
								<!--begin::Table container-->
								<div class="table-responsive">
									<!--begin::Table-->
									<table id="kt_profile_overview_table" class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold">
										<thead class="fs-7 text-gray-400 text-uppercase">
											<tr>
												<th class="min-w-250px">Manager</th>
												<th class="min-w-150px">Date</th>
												<th class="min-w-90px">Sample</th>
												<th class="min-w-90px">Rating</th>
												<th class="min-w-50px text-end">Details</th>
											</tr>
										</thead>
										<tbody class="fs-6">
											<?php

											//foreach SELECT * FROM markdown WHERE user_id = 1
											$sql = "SELECT * FROM files ORDER BY added_date DESC";
											$result = $con->query($sql);
											function getColorFromLetter($letter)
											{
												$ascii = ord(strtoupper($letter));

												$red = ($ascii * 23) % 256;
												$green = ($ascii * 47) % 256;
												$blue = ($ascii * 67) % 256;

												return "rgb($red, $green, $blue)";
											}

											$letterStyles = [];
											foreach (range('A', 'Z') as $letter) {
												$letterStyles[$letter] = getColorFromLetter($letter);
											}

											function getLetterStyle($letter, $letterStyles)
											{
												return $letterStyles[strtoupper($letter[0])] ?? null;
											}
											if ($result->num_rows > 0) {
												// output data of each row
												$i = 0;
												while ($row = $result->fetch_assoc()) {
													$i++;
													// if ($i == 5) $i = 1;
													// make $row['created_ad'] look nice
													$created_at = date('F j, Y', strtotime($row['added_date']));

													// trim row original_name if too long
													$original_name = strlen($row['original_name']) > 18 ? substr($row['original_name'], 0, 18) . '...' : $row['original_name'];

													// trim row url if too long
													$url = strlen($row['url']) > 18 ? substr($row['url'], 0, 18) . '...' : $row['url'];


													if ($_SESSION['id'] == 1) {
														$checkbox = '<input class="form-check-input me-2" type="checkbox" name="id[]" value="' . $row['id'] . '"> ';
														$copybutton = '';
														// find tags for each file
														$query = "SELECT tag_name FROM tags INNER JOIN files_tags ON tags.tag_id = files_tags.tag_id WHERE files_tags.file_id = '" . $row['id'] . "'";
														$result3 = mysqli_query($con, $query);
														// set $tags as badge html
														$tags = '';
														while ($row3 = mysqli_fetch_assoc($result3)) {
															$tags .= '<span class="badge badge-light">' . $row3['tag_name'] . '</span> ';
														}
													} else {
														$checkbox = '';
														$copybutton = "<button class='btn btn-sm fw-bold btn-light copy-button ms-4 p-1 ps-2 pe-2' onclick='copyToClipboard(\"" . htmlspecialchars($row['url']) . "\", this)'>
														Copy to Clipboard
														</button>";
														$tags = '<span class="badge badge-light-success fw-bold px-4 py-3">Online</span>';
													}

													// get first letter from $row['name']
													$firstLetter = mb_substr($row['original_name'], 0, 1);

													$backgroundColor = getLetterStyle($firstLetter, $letterStyles);
													$icon = "<div class='me-5 position-relative'><div class='symbol symbol-35px symbol-circle'>
															   <span class='symbol-label' style='background-color: $backgroundColor; color: white; font-weight: bold;'>$firstLetter</span>
															</div></div>";

													echo '<tr>
															<td>' . $checkbox . '
																<div class="d-flex align-items-center">
																	' . $icon . '
																	<div class="d-flex flex-column justify-content-center">
																		<a href="" class="fs-6 text-gray-800 text-hover-primary">' . $original_name . '</a>
																		<div class="fw-semibold text-gray-400">' . $url . '</div>
																	</div>
																</div>
															</td>
															<td>' . $created_at . '</td>
<td class="fs-7">';
													$pitches = [-16, -12, -8, -4, 0, 4, 8, 12, 16];

													// Single audio player
													$defaultFileName = "samples/" . $row['name'] . ".mp3";
													echo '<div style="float: left; margin-right: 10px;">'; // Float the audio player to the left
													echo '<audio id="audioPlayer-' . $row['id'] . '" style="height:40px;" controls>
    <source src="' . $defaultFileName . '" type="audio/mpeg">
    Your browser does not support the audio tag.
</audio>';
													echo '</div>'; // Close audio player div

													// Display Sample text
													// echo '<div>Sample</div>';

													// Display Pitch text and dropdown
													echo '<div>Pitch:</div>';
													echo '<div><select id="pitchSelector-' . $row['id'] . '" onchange="updateAudioSource(this.value, ' . $row['id'] . ', \'' . $row['name'] . '\');">';
													foreach ($pitches as $pitch) {
														// if pitch 0 set as default
														if ($pitch == 0) {
															echo '<option value="' . $pitch . '" selected>' . $pitch . '</option>';
														} else {
															echo '<option value="' . $pitch . '">' . $pitch . '</option>';
														}
													}
													echo '</select>';
													echo '</div>'; // Close Pitch div


													echo '</td>
															<td>
																' . $tags . '
															</td>
															<td class="text-end">
																<input type="hidden" id="name-' . $row['id'] . '" name="name-' . $row['id'] . '" value="' . $row['name'] . '">
																<a href="#" class="btn btn-lg btn-primary btn-active-light-primary">Select -></a> 
															</td>
														</tr>';
												}
											}
											?>


										</tbody>
									</table>
									<script>
										function updateAudioSource(pitch, rowId, rowName) {
											var audioElementId = "audioPlayer-" + rowId;
											var fileName = "samples/" + rowName + ".p" + pitch + ".mp3";

											if (pitch == 0) {
												fileName = "samples/" + rowName + ".mp3";
											}

											document.getElementById(audioElementId).querySelector("source").src = fileName;
											document.getElementById(audioElementId).load();

											const pitchElement = document.getElementById('kt_modal_create_campaign_budget_label');
											if (pitchElement) {
												pitchElement.textContent = pitch;
											}

											const slider = document.getElementById('kt_modal_create_campaign_budget_slider');
											if (slider) {
												const percentage = (pitch / 100) * 100; // Assuming max pitch of 100
												slider.style.width = percentage + "%";
											}
										}

										document.addEventListener('DOMContentLoaded', function() {
											const selectButtons = document.querySelectorAll('a.btn');

											selectButtons.forEach(button => {
												button.addEventListener('click', function(event) {
													event.preventDefault();

													const row = this.closest('tr');
													const hiddenInput = row.querySelector('input[type="hidden"]');
													const nameValue = hiddenInput ? hiddenInput.value : null;

													const pitchElement = document.getElementById('kt_modal_create_campaign_budget_label');
													const pitchValue = pitchElement ? parseInt(pitchElement.textContent.trim()) : null;

													if (nameValue !== null && pitchValue !== null) {
														const nameInput = document.createElement('input');
														nameInput.type = 'hidden';
														nameInput.name = 'name';
														nameInput.value = nameValue;

														const pitchInput = document.createElement('input');
														pitchInput.type = 'hidden';
														pitchInput.name = 'pitch';
														pitchInput.value = pitchValue;

														const form = document.getElementById('audios');
														form.appendChild(nameInput);
														form.appendChild(pitchInput);
														form.submit();

														updateAudioSource(pitchValue, row.getAttribute('data-id'), nameValue);
													}
												});
											});
										});
									</script>

									<!--end::Table-->
								</div>
								<!--end::Table container-->
							</div>
							<!--end::Card body-->
						</div>



						<!--end::Col-->
					</div>



















					<!--begin::Row-->

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