<?php

ini_set('session.cookie_lifetime', 60 * 60 * 24 * 365);

session_start();
if (@$_SESSION['id'] == 1) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

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
?>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
	<!--begin::Content wrapper-->
	<div class="d-flex flex-column flex-column-fluid" style="background: url('/assets/media/misc/dc5251e3-c26b-434b-a449-19e48d6874c1.webp') repeat-y center top; background-size: 100% auto;">
		<!--begin::Toolbar-->

		<!--end::Toolbar-->
		<!--begin::Content-->
		<div id="kt_app_content" class="app-content flex-column-fluid" style="background: rgba(27, 27, 27, 0.5);">
			<!--begin::Content container-->
			<div id="kt_app_content_container" class="app-container container-xxl pt-10">
				<!--begin::Row-->
				<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
					<div class="col-md-4 text-center mt-5">
						<div class="card border-primary">
							<div class="card-body p-0 pt-4">
								<h1 class="fw-bold text-primary">Step 1:</h1>
								<p class="fs-4">Pick a Voice</p>
							</div>
						</div>
					</div>
					<div class="col-md-4 text-center mt-5">
						<div class="card">
							<div class="card-body p-0 pt-4 text-muted">
								<h1 class="fw-bold text-muted">Step 2:</h1>
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







				<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
					<!--begin::Col-->
					<div class="col-xl-12">


						<!--begin::Table-->
						<div class="card card-flush">
							<!--begin::Card header-->
							<div class="card-header mt-5">
								<!--begin::Card title-->
								<div class="card-title flex-column">
									<h1 class="fw-bold mb-1">Pick a Voice: 🗣️👇</h1>
									<!-- <div class="fs-6 text-gray-400">Updated 37 minutes ago</div> -->
								</div>
								<!--begin::Card title-->
								<!--begin::Card toolbar-->
								<div class="card-toolbar my-1 w-75 d-flex justify-content-end">
									<div class="me-4 my-1" style="display:none;"><span class="fw-bold fs-4 mt-1 me-2">Set Pitch</span>
										<span class="fw-bold fs-3x" id="kt_modal_create_campaign_budget_label"></span>
									</div>
									<div class="me-4 my-1 ps-4 pe-4 w-25" style="display:none;">
										<div id="kt_modal_create_campaign_budget_slider" class="noUi-sm"></div>

									</div>
									<div class="me-6 my-1" style="display:none;">
										<select id="kt_filter_year" name="year" data-control="select2" data-hide-search="true" class="w-125px form-select form-select-sm">
											<option value="All" selected="selected">Gender</option>
											<option value="thisyear">Male</option>
											<option value="thismonth">Female</option>
											<option value="lastmonth">Other</option>
										</select>
									</div>
									<div class="me-4 my-1" style="display:none;">
										<select id="kt_filter_orders" name="orders" data-control="select2" data-hide-search="true" class="w-125px form-select form-select-sm">
											<option value="All" selected="selected">Accent</option>
											<option value="Approved">Approved</option>
											<option value="Declined">Declined</option>
											<option value="In Progress">In Progress</option>
											<option value="In Transit">In Transit</option>
										</select>
									</div>
									<!-- <div class="d-flex align-items-center position-relative my-1">
										<i class="ki-duotone ki-magnifier fs-3 position-absolute ms-3">
											<span class="path1"></span>
											<span class="path2"></span>
										</i>
										<input type="text" id="kt_filter_search" class="form-control w-150px fs-7 ps-9" placeholder="Search Voices" />
									</div> -->
								</div>
								<!--begin::Card toolbar-->
							</div>
							<!--end::Card header-->
							<!--begin::Card body-->
							<style>
								.table {
									table-layout: fixed;
								}

								.table th,
								.table td {
									word-break: break-word;
									padding: 0.25rem !important;
									/* padding-bottom: 0.75rem !important; */
								}

								.table th:nth-child(2),
								.table td:nth-child(2) {
									max-width: 50%;
								}

								.table th:nth-child(3),
								.table td:nth-child(3) {
									width: 450px;
								}

								.table th:nth-child(1),
								.table td:nth-child(1) {
									width: 300px;
								}

								@media (max-width: 768px) {

									.table th:nth-child(2),
									.table td:nth-child(2) {
										max-width: 40%;
									}
								}


								.search-container {
									display: flex;
									align-items: center;
								}

								#search {
									border-radius: 12px;
									background: linear-gradient(#131313, #131313) padding-box, linear-gradient(90deg, #ed6e61, #6359e1) border-box;
									border: 4px solid transparent;
									padding: 10px;
									color: white;
									margin-right: -4px;
									flex-grow: 1;
								}

								#searchButton {
									border-radius: 12px;
									background: linear-gradient(90deg, #ed6e61, #6359e1);
									color: white;
									border: none;
									padding: 10px 15px;
									margin-left: 10px;
									cursor: pointer;
									font-weight: bold;
									font-size: 18px;
									display: flex;
									align-items: center;
									justify-content: center;
									transition: background 0.3s ease, box-shadow 0.3s ease;
									box-shadow: 2px 2px 6px #00000050;
								}

								#searchButton:hover {
									background: linear-gradient(90deg, #f77f78, #7063ea);
								}

								#searchButton:active {
									box-shadow: inset 1px 1px 3px #00000050;
								}

								#searchButton:focus {
									outline: none;
									box-shadow: 0 0 0 2px #6359e1;
								}
							</style>

							<div class="card-body pt-0">
								<div class="table-responsive">
									<form action="" method="get" class="search-container">
										<input type="text" id="search" name="search" class="form-control" placeholder="Search..." <?php if (!empty($_GET['search'])) {
																																		echo 'value="' . htmlspecialchars($_GET['search']) . '" ';
																																	} ?>>
										<button type="submit" id="searchButton">
											Search
										</button>
									</form>
									<table class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold">
										<thead class="fs-7 text-gray-400 text-uppercase">
											<tr>
												<th class="min-w-100px">Voice</th>
												<th class="min-w-50px">Run</th>
												<th class="min-w-150px">Sample</th>
												<th class="text-end">Date</th>
											</tr>
										</thead>
										<tbody class="fs-6">
											<?php

											// $sql = "SELECT * FROM files WHERE active = 1 ORDER BY added_date DESC";
											// $limit = 25;
											// $sql = "SELECT * FROM files WHERE active = 1 ORDER BY added_date DESC LIMIT 50";
											$search = isset($_GET['search']) ? $con->real_escape_string($_GET['search']) : '';

											$query = "SELECT * FROM files WHERE active = 1";
											if (!empty($search)) {
												$query .= " AND (name LIKE '%$search%' OR original_name LIKE '%$search%')";
											}
											$query .= " ORDER BY added_date DESC LIMIT 50";

											$result = $con->query($query);
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
												$i = 0;
												while ($row = $result->fetch_assoc()) {
													$i++;

													$defaultFileName = "samples/" . $row['name'] . ".mp3";

													if (!file_exists($defaultFileName)) {
														continue;
													}

													$created_at = date('F j, Y', strtotime($row['added_date']));

													$original_name = strlen($row['original_name']) > 25 ? substr($row['original_name'], 0, 25) . '...' : $row['original_name'];

													$url = strlen($row['url']) > 25 ? substr($row['url'], 0, 25) . '...' : $row['url'];


													if ($_SESSION['id'] == 1) {
														$checkbox = '<input class="form-check-input me-2" type="checkbox" name="id[]" value="' . $row['id'] . '"> ';
														$copybutton = '';
														$query = "SELECT tag_name FROM tags INNER JOIN files_tags ON tags.tag_id = files_tags.tag_id WHERE files_tags.file_id = '" . $row['id'] . "'";
														$result3 = mysqli_query($con, $query);
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
																		<span href="" class="fs-6 text-gray-800 text-hover-primary">' . $original_name . '</span>
																		<div class="fw-semibold text-gray-400">' . $url . '</div>
																	</div>
																</div>
															</td>
															<td class="">
																<input type="hidden" id="name-' . $row['id'] . '" name="name-' . $row['id'] . '" value="' . $row['name'] . '">
																<a href="/run?url=' . urlencode($row['url']) . '&pitch=0" class="btn btn-primary btn-active-light-primary" id="selectButton-' . $row['id'] . '">Select -></a> 
															</td>';

													echo '<td class="fs-7" style="display: flex; align-items: center; gap: 10px;">';
													$pitches = [-16, -12, -8, -4, 0, 4, 8, 12, 16];

													$defaultFileName = "samples/" . $row['name'] . ".mp3";
													echo '<div style="float: left; margin-right: 10px;">';

													echo '<div id="playIcon-' . $row['id'] . '" onclick="playAudio(' . $row['id'] . ', \'' . $row['name'] . '\');" style="cursor: pointer; float: left; margin-right: 10px;">
													<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-play-fill" viewBox="0 0 16 16">
													<path d="m11.596 8.697-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 0 1 0 1.393z"/>
												  </svg>
</div>';
													echo '<audio id="audioPlayer-' . $row['id'] . '" data-row-name="' . $row['name'] . '" data-row-url="' . $row['url'] . '" style="height:40px; display:none;" controls>
<source type="audio/mpeg">
Your browser does not support the audio tag.
</audio>';
													echo '</div>';
													echo '</div>';
													echo '<div>Pitch:<br><select id="pitchSelector-' . $row['id'] . '" onchange="updateAudioSource(this.value, ' . $row['id'] . ', \'' . $row['name'] . '\');">';
													foreach ($pitches as $pitch) {
														if ($pitch == 0) {
															echo '<option value="' . $pitch . '" selected>' . $pitch . '</option>';
														} else {
															echo '<option value="' . $pitch . '">' . $pitch . '</option>';
														}
													}
													echo '</select>';
													echo '</div>';
													echo '<div>Gender:<br><button id="genderToggle-' . $row['id'] . '" onclick="toggleGender(' . $row['id'] . ');">Male</button></div>';


													echo '</td>
															<!--<td>
																' . $tags . '
															</td>-->
															
															<td class="text-end">' . $created_at . '</td>
														</tr>';
												}
											}
											?>


										</tbody>
									</table>
									<div id="loadingIndicator" style="display: none;">
										<div class="spinner-border text-primary" role="status">
											<span class="sr-only">Loading...</span>
										</div>
									</div>

									<script>
										var genderState = {};

										function updateHref(pitch, rowId, url) {
											var selectButtonId = "selectButton-" + rowId;
											var selectButton = document.getElementById(selectButtonId);

											selectButton.href = '/run?url=' + encodeURIComponent(url) + '&pitch=' + pitch;
										}

										function playAudio(rowId, rowName) {
											const audioPlayer = document.getElementById("audioPlayer-" + rowId);
											audioPlayer.style.display = 'block';

											const playIcon = document.getElementById("playIcon-" + rowId);
											playIcon.style.display = 'none';

											const pitch = document.getElementById('pitchSelector-' + rowId).value;
											updateAudioSource(pitch, rowId, rowName);
											audioPlayer.play();
										}

										function toggleGender(rowId) {
											genderState[rowId] = !genderState[rowId];

											const toggleButton = document.getElementById("genderToggle-" + rowId);

											if (genderState[rowId]) {
												toggleButton.textContent = "Female";
											} else {
												toggleButton.textContent = "Male";
											}

											const pitchElement = document.getElementById('pitchSelector-' + rowId);
											const pitch = pitchElement ? pitchElement.value : 0;
											const rowName = document.getElementById("audioPlayer-" + rowId).getAttribute("data-row-name");

											updateAudioSource(pitch, rowId, rowName);
										}

										function updateAudioSource(pitch, rowId, rowName) {
											var audioElementId = "audioPlayer-" + rowId;
											var audioPlayer = document.getElementById(audioElementId);

											var folder = genderState[rowId] ? "samples2" : "samples";
											var fileName = folder + "/" + rowName + (pitch == 0 ? ".mp3" : ".p" + pitch + ".mp3");

											audioPlayer.querySelector("source").src = fileName;
											audioPlayer.load();

											const url = document.getElementById("audioPlayer-" + rowId).getAttribute("data-row-url");
											updateHref(pitch, rowId, url);

										}

										// document.addEventListener('DOMContentLoaded', function() {
										// 	const selectButtons = document.querySelectorAll('a.btn');

										// 	selectButtons.forEach(button => {
										// 		button.addEventListener('click', function(event) {
										// 			event.preventDefault();

										// 			const files = document.getElementById('fileInput').files;
										// 			const existingFilesSelected = document.querySelectorAll('.existing-file-input').length > 0;
										// 			if (files.length === 0 && !existingFilesSelected) {
										// 				alert('Please select at least one file to upload or choose from existing files.');
										// 				return;
										// 			}

										// 			const row = this.closest('tr');
										// 			const hiddenInput = row.querySelector('input[type="hidden"]');
										// 			const nameValue = hiddenInput ? hiddenInput.value : null;

										// 			const pitchElement = document.getElementById('kt_modal_create_campaign_budget_label');
										// 			const pitchValue = pitchElement ? parseInt(pitchElement.textContent.trim()) : null;

										// 			if (nameValue !== null && pitchValue !== null) {
										// 				const nameInput = document.createElement('input');
										// 				nameInput.type = 'hidden';
										// 				nameInput.name = 'name';
										// 				nameInput.value = nameValue;

										// 				const pitchInput = document.createElement('input');
										// 				pitchInput.type = 'hidden';
										// 				pitchInput.name = 'pitch';
										// 				pitchInput.value = pitchValue;

										// 				const form = document.getElementById('audios');
										// 				form.appendChild(nameInput);
										// 				form.appendChild(pitchInput);

										// 				form.submit();

										// 				updateAudioSource(pitchValue, row.getAttribute('data-id'), nameValue);
										// 			}
										// 		});
										// 	});
										// });
									</script>
									<script>
										let loadedRows = 50; // Tracks how many rows have been loaded
										let loadingData = false; // Flag to prevent multiple loads
										let allDataLoaded = false;

										window.onscroll = function() {
											if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
												loadMoreData();
											}
										};

										function loadMoreData() {
											if (loadingData) {
												return;
											}
											loadingData = true;
											document.getElementById('loadingIndicator').style.display = 'block'; // Show the spinner

											const searchQuery = document.getElementById('search').value || '';
											fetch('fetchdata.php?offset=' + loadedRows + '&search=' + encodeURIComponent(searchQuery))
												.then(response => response.text())
												.then(data => {
													if (data.includes("No more records found")) {
														allDataLoaded = true; // Set the flag to true when no more data is available
														document.getElementById('loadingIndicator').style.display = 'none'; // Optionally, remove or hide the spinner permanently
														return; // Stop further execution
													}
													document.querySelector('tbody').innerHTML += data;
													loadedRows += 50;
													loadingData = false;
													document.getElementById('loadingIndicator').style.display = 'none'; // Hide the spinner after loading
												}).catch(() => {
													loadingData = false;
													document.getElementById('loadingIndicator').style.display = 'none'; // Hide the spinner if an error occurs
												});
										}
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
		<?php include 'layout/modals/_login.php'; ?>
		<?php include 'core/footer.php'; ?>