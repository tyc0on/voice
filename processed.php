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
				<!-- <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
			
					<div style="text-align:center; margin-top:100px;">
						<h1 style="font-size:60px;">Upload your voice file</h1>
						<h2>MP3 or WAV</h2>
					</div>
					<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 mt-0">
					
						<h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
							Step 1:
						</h1>

					</div>



				</div> -->


				<!--end::Row-->
				<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 ">
					<!--begin::Title-->
					<!-- <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0 mb-5">
						Batch:
					</h1> -->
					<!--end::Title-->

				</div>





				<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
					<!--begin::Col-->
					<div class="col-xl-12">


						<!--begin::Table-->
						<div class="card card-flush mt-6 mt-xl-9">
							<!--begin::Card header-->
							<!-- <div class="card-header mt-5">
								
								<div class="card-title flex-column">
									<h3 class="fw-bold mb-1">Processed Audio Files</h3>
									
								</div>
								
								<div class="card-toolbar my-1 w-75 d-flex justify-content-end">
									
								</div>
							</div> -->
							<!--end::Card header-->
							<!--begin::Card body-->
							<div class="card-body pt-0">
								<!--begin::Table container-->
								<div class="table-responsive">
									<!--begin::Table-->
									<table id="kt_profile_overview_table" class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold">
										<thead class="fs-7 text-gray-400 text-uppercase">
											<tr>
												<th class="min-w-250px">File</th>
												<!-- <th class="min-w-150px">Date</th>
												<th class="min-w-90px text-end">Download</th> -->
											</tr>
										</thead>
										<tbody class="fs-6">
											<?php

											// select * from jobs where batch_id = POST batch left join paudio_files ON paudio_files.job_id = jobs.id
											$sql = "SELECT * FROM jobs LEFT JOIN audio_files ON audio_files.id = jobs.audio_id LEFT JOIN paudio_files ON paudio_files.job_id = jobs.id WHERE jobs.user_id = ? AND jobs.batch_id = ? ORDER BY jobs.id DESC";
											$stmt = $con->prepare($sql);
											$stmt->bind_param('ii', $_SESSION['id'], $_GET['batch']);
											$stmt->execute();
											$result = $stmt->get_result();



											//foreach SELECT * FROM markdown WHERE user_id = 1
											// $sql = "SELECT * FROM files";
											// $result = $con->query($sql);
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
													// $i++;
													// // if ($i == 5) $i = 1;
													// // make $row['created_ad'] look nice
													// if row upload_date is empty then created at = red text "pending"
													if ($row['upload_date'] == "") {
														$created_at = "<span class='badge badge-light-danger fw-bold px-4 py-3'>Pending</span>";
													} else {
														$created_at = date('F j, Y', strtotime($row['upload_date']));
													}
													// $created_at = date('F j, Y', strtotime($row['upload_date']));

													// // trim row original_name if too long
													// $original_name = strlen($row['original_name']) > 18 ? substr($row['original_name'], 0, 18) . '...' : $row['original_name'];

													// // trim row url if too long
													// $url = strlen($row['url']) > 18 ? substr($row['url'], 0, 18) . '...' : $row['url'];




													// // get first letter from $row['name']
													// $firstLetter = mb_substr($row['original_name'], 0, 1);

													// $backgroundColor = getLetterStyle($firstLetter, $letterStyles);
													// $icon = "<div class='me-5 position-relative'><div class='symbol symbol-35px symbol-circle'>
													// 		   <span class='symbol-label' style='background-color: $backgroundColor; color: white; font-weight: bold;'>$firstLetter</span>
													// 		</div></div>";
													$url = "";
													// $created_at = "";

													$original_name = $row['original_name'];
													echo '<tr>
															<td class="p-1">
																<!--<div class="d-flex align-items-center">
																	
																	<div class="d-flex flex-column justify-content-center">
																		
																		<div class="fw-semibold text-gray-400">' . $url . '</div>
																	</div>
																</div><br>-->
															<!--</td>
															<td>--><!--</td>
<td class="fs-7 text-end">-->';
													$pitches = [-16, -12, -8, -4, 0, 4, 8, 12, 16];

													// Single audio player
													// $defaultFileName = "paudios/" . $row['name'] . ".mp3";
													echo '<div style="float: left; margin-right: 10px;">'; // Float the audio player to the left
													echo '<audio id="audioPlayer-' . $row['id'] . '" style="height:40px; margin-right: 10px;" controls>
    <source src="' . $row['file_path'] . '" type="audio/mpeg">
    Your browser does not support the audio tag.
</audio>';
													echo ' <span class="fs-6 text-gray-800 text-hover-primary">' . $original_name . '</span></div><!--<br>' . $created_at . '--></td>'; // Close audio player div

													// Display Sample text
													// echo '<div>Sample</div>';

													// Display Pitch text and dropdown
													// echo '<div>Pitch:</div>';
													// echo '<div><select id="pitchSelector-' . $row['id'] . '" onchange="updateAudioSource(this.value, ' . $row['id'] . ', \'' . $row['name'] . '\');">';
													// foreach ($pitches as $pitch) {
													// 	// if pitch 0 set as default
													// 	if ($pitch == 0) {
													// 		echo '<option value="' . $pitch . '" selected>' . $pitch . '</option>';
													// 	} else {
													// 		echo '<option value="' . $pitch . '">' . $pitch . '</option>';
													// 	}
													// }
													// echo '</select>';
													// echo '</div>'; // Close Pitch div


													// echo '</td>
													// 		<td>
													// 			<span class="badge badge-light-success fw-bold px-4 py-3">Online</span>
													// 		</td>
													// 		<td>

													// 		</td>
													// 	</tr>';
												}
											}
											?>


										</tbody>
									</table>

									<!--end::Table-->
								</div>
								<!--end::Table container-->
							</div>
							<!--end::Card body-->
						</div>



						<!--end::Col-->
					</div>




				</div>
				<!--end::Content container-->



				<!-- <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
					<div class="col-xl-12">
						<div class="card card-flush mt-6 mt-xl-9">
						
							<div class="card-body pt-0">
								<h1><a href="">Use voice model again</a></h1>

							</div>
						</div>
					</div>
				</div> -->


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