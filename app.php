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
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

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

?>
<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
	<base href="/" />
	<title>Easy AI Voice - Tailored AI Voices, Designed for Simplicity</title>
	<meta name="description" content="Easy AI Voice offers cutting-edge Tailored AI Voices, Designed for Simplicity, One Click Away" />
	<meta name="keywords" content="Easy Ai, Easy AI Voice, AI, Artificial Intelligence, Productivity, Automation, Efficiency, Business Solutions, Easy-to-use, Data Analysis, Innovation, Streamline, Operations, Success" />
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta property="og:locale" content="en_US" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="Easy AI Voice - Tailored AI Voices, Designed for Simplicity" />
	<meta property="og:url" content="https://easyaivoice.com" />
	<meta property="og:site_name" content="Easy AI Voice" />
	<link rel="canonical" href="https://easyaivoice.com" />
	<link rel="apple-touch-icon" sizes="180x180" href="assets/media/logos/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="assets/media/logos/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="assets/media/logos/favicon-16x16.png">
	<link rel="manifest" href="assets/media/logos/site.webmanifest">
	<link rel="mask-icon" href="assets/media/logos/safari-pinned-tab.svg" color="#5bbad5">
	<link rel="shortcut icon" href="assets/media/logos/favicon.ico">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="msapplication-config" content="assets/media/logos/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">
	<!--begin::Fonts(mandatory for all pages)-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
	<!--end::Fonts-->
	<!--begin::Vendor Stylesheets(used for this page only)-->
	<link href="assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
	<!--end::Vendor Stylesheets-->
	<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
	<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
	<!--end::Global Stylesheets Bundle-->
	<script>
		// Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }
	</script>
	<script>
		// function checkServerStatus(url) {
		// 	return new Promise((resolve, reject) => {
		// 		const controller = new AbortController();
		// 		const timeoutId = setTimeout(() => controller.abort(), 5000);

		// 		fetch(url, {
		// 				signal: controller.signal
		// 			})
		// 			.then(response => {
		// 				clearTimeout(timeoutId);
		// 				resolve(response.status === 200);
		// 			})
		// 			.catch(error => {
		// 				clearTimeout(timeoutId);
		// 				reject(error);
		// 			});
		// 	});
		// }

		// function updateUI(isConnected) {
		// 	const statusIcon = document.getElementById('statusIcon');
		// 	const statusText = document.getElementById('statusText');
		// 	const reconnectLink = document.getElementById('reconnectLink');

		// 	if (isConnected) {
		// 		statusIcon.innerHTML = '&#x2705;'; // Check Mark for connected
		// 		statusIcon.style.color = 'green';
		// 		statusText.innerText = 'Connected';
		// 		reconnectLink.style.display = 'none';
		// 	} else {
		// 		statusIcon.innerHTML = '&#x2715;'; // X Mark for disconnected
		// 		statusIcon.style.color = 'red';
		// 		statusText.innerText = 'Disconnected';
		// 		reconnectLink.style.display = 'inline';
		// 	}
		// }

		// function getURL() {
		// 	fetch('url.php')
		// 		.then(response => response.text())
		// 		.then(data => {
		// 			window.serverUrl = data;
		// 			checkServerStatus(window.serverUrl)
		// 				.then(isConnected => updateUI(isConnected))
		// 				.catch(error => updateUI(false));
		// 		})
		// 		.catch(error => console.error(error));
		// }

		// // Fetch the URL and check server status on page load
		// window.onload = getURL;

		// // Check server status at regular intervals
		// setInterval(() => {
		// 	checkServerStatus(window.serverUrl)
		// 		.then(isConnected => updateUI(isConnected))
		// 		.catch(error => updateUI(false));
		// }, 5000);
	</script>
	<?php include '_head.php'; ?>
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default dark-mode">
	<!--begin::Theme mode setup on page load-->
	<script>
		var defaultThemeMode = "dark";
		var themeMode;
		if (document.documentElement) {
			if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
				themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
			} else {
				if (localStorage.getItem("data-bs-theme") !== null) {
					themeMode = localStorage.getItem("data-bs-theme");
				} else {
					themeMode = defaultThemeMode;
				}
			}
			if (themeMode === "system") {
				themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
			}
			document.documentElement.setAttribute("data-bs-theme", themeMode);
		}
	</script>
	<!--end::Theme mode setup on page load-->
	<!--begin::App-->
	<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
		<!--begin::Page-->
		<div class="app-page flex-column flex-column-fluid" id="kt_app_page">
			<!--begin::Header-->
			<div id="kt_app_header" class="app-header" data-kt-sticky="true" data-kt-sticky-activate="{default: true, lg: true}" data-kt-sticky-name="app-header-minimize" data-kt-sticky-offset="{default: '200px', lg: '0'}" data-kt-sticky-animation="false">
				<!--begin::Header container-->
				<div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_container">
					<!--begin::Sidebar mobile toggle-->
					<div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
						<div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
							<i class="ki-duotone ki-abstract-14 fs-2 fs-md-1">
								<span class="path1"></span>
								<span class="path2"></span>
							</i>
						</div>
					</div>
					<!--end::Sidebar mobile toggle-->
					<!--begin::Mobile logo-->
					<div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
						<a href="/app.php" class="d-lg-none">
							<img alt="Logo" src="assets/media/logos/android-chrome-192x192.png" class="h-30px" />
						</a>
					</div>
					<!--end::Mobile logo-->
					<!--begin::Header wrapper-->
					<div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">
						<!--begin::Menu wrapper-->
						<div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="{default: 'append', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
							<!--begin::Menu-->
							<!-- <div class="mt-9">
								<span id="statusIcon" style="color:red;">&#x2715;</span>
								<span id="statusText">Disconnected</span>
								<a id="reconnectLink" style="display:none;" target="_blank" href="https://colab.research.google.com/github/Viral-Cuts/test/blob/main/app<?php echo $_SESSION['colab']; ?>.ipynb">
									<img src="https://colab.research.google.com/assets/colab-badge.svg" alt="Open In Colab" />
								</a>
							</div> -->
						</div>
						<!--end::Menu wrapper-->
						<!--begin::Navbar-->
						<div class="app-navbar flex-shrink-0">
							<!--begin::Search-->

							<!--end::My apps links-->
							<!--begin::User menu-->
							<div class="app-navbar-item ms-1 ms-md-4" id="kt_header_user_menu_toggle">
								<!--begin::Menu wrapper-->
								<div class="cursor-pointer symbol symbol-35px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
									<img src="https://lh3.googleusercontent.com/a/AAcHTteAneghS5HbLvTGHh8fijGv-JhOAwNSDneN-I_n7XmwHQ54=s96-c" class="rounded-3" alt="user" />
								</div>
								<!--begin::User account menu-->
								<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
									<!--begin::Menu item-->
									<div class="menu-item px-3">
										<div class="menu-content d-flex align-items-center px-3">
											<!--begin::Avatar-->
											<div class="symbol symbol-50px me-5">
												<img alt="Logo" src="https://lh3.googleusercontent.com/a/AAcHTteAneghS5HbLvTGHh8fijGv-JhOAwNSDneN-I_n7XmwHQ54=s96-c" />
											</div>
											<!--end::Avatar-->
											<!--begin::Username-->
											<div class="d-flex flex-column">
												<div class="fw-bold d-flex align-items-center fs-5">Robert Fox
													<span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">Pro</span>
												</div>
												<a href="#" class="fw-semibold text-muted text-hover-primary fs-7">robert@kt.com</a>
											</div>
											<!--end::Username-->
										</div>
									</div>
									<!--end::Menu item-->
									<!--begin::Menu separator-->
									<div class="separator my-2"></div>
									<!--end::Menu separator-->
									<!--begin::Menu item-->
									<div class="menu-item px-5">
										<a href="../../demo1/dist/account/overview.html" class="menu-link px-5">My Profile</a>
									</div>
									<!--end::Menu item-->
									<!--begin::Menu item-->
									<div class="menu-item px-5">
										<a href="../../demo1/dist/apps/projects/list.html" class="menu-link px-5">
											<span class="menu-text">My Projects</span>
											<span class="menu-badge">
												<span class="badge badge-light-danger badge-circle fw-bold fs-7">3</span>
											</span>
										</a>
									</div>
									<!--end::Menu item-->
									<!--begin::Menu item-->
									<div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
										<a href="#" class="menu-link px-5">
											<span class="menu-title">My Subscription</span>
											<span class="menu-arrow"></span>
										</a>
										<!--begin::Menu sub-->
										<div class="menu-sub menu-sub-dropdown w-175px py-4">
											<!--begin::Menu item-->
											<div class="menu-item px-3">
												<a href="../../demo1/dist/account/referrals.html" class="menu-link px-5">Referrals</a>
											</div>
											<!--end::Menu item-->
											<!--begin::Menu item-->
											<div class="menu-item px-3">
												<a href="../../demo1/dist/account/billing.html" class="menu-link px-5">Billing</a>
											</div>
											<!--end::Menu item-->
											<!--begin::Menu item-->
											<div class="menu-item px-3">
												<a href="../../demo1/dist/account/statements.html" class="menu-link px-5">Payments</a>
											</div>
											<!--end::Menu item-->
											<!--begin::Menu item-->
											<div class="menu-item px-3">
												<a href="../../demo1/dist/account/statements.html" class="menu-link d-flex flex-stack px-5">Statements
													<span class="ms-2 lh-0" data-bs-toggle="tooltip" title="View your statements">
														<i class="ki-duotone ki-information-5 fs-5">
															<span class="path1"></span>
															<span class="path2"></span>
															<span class="path3"></span>
														</i>
													</span></a>
											</div>
											<!--end::Menu item-->
											<!--begin::Menu separator-->
											<div class="separator my-2"></div>
											<!--end::Menu separator-->
											<!--begin::Menu item-->
											<div class="menu-item px-3">
												<div class="menu-content px-3">
													<label class="form-check form-switch form-check-custom form-check-solid">
														<input class="form-check-input w-30px h-20px" type="checkbox" value="1" checked="checked" name="notifications" />
														<span class="form-check-label text-muted fs-7">Notifications</span>
													</label>
												</div>
											</div>
											<!--end::Menu item-->
										</div>
										<!--end::Menu sub-->
									</div>
									<!--end::Menu item-->
									<!--begin::Menu item-->
									<div class="menu-item px-5">
										<a href="../../demo1/dist/account/statements.html" class="menu-link px-5">My Statements</a>
									</div>
									<!--end::Menu item-->
									<!--begin::Menu separator-->
									<div class="separator my-2"></div>
									<!--end::Menu separator-->
									<!--begin::Menu item-->
									<div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
										<a href="#" class="menu-link px-5">
											<span class="menu-title position-relative">Mode
												<span class="ms-5 position-absolute translate-middle-y top-50 end-0">
													<i class="ki-duotone ki-night-day theme-light-show fs-2">
														<span class="path1"></span>
														<span class="path2"></span>
														<span class="path3"></span>
														<span class="path4"></span>
														<span class="path5"></span>
														<span class="path6"></span>
														<span class="path7"></span>
														<span class="path8"></span>
														<span class="path9"></span>
														<span class="path10"></span>
													</i>
													<i class="ki-duotone ki-moon theme-dark-show fs-2">
														<span class="path1"></span>
														<span class="path2"></span>
													</i>
												</span></span>
										</a>
										<!--begin::Menu-->
										<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px" data-kt-menu="true" data-kt-element="theme-mode-menu">
											<!--begin::Menu item-->
											<div class="menu-item px-3 my-0">
												<a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
													<span class="menu-icon" data-kt-element="icon">
														<i class="ki-duotone ki-night-day fs-2">
															<span class="path1"></span>
															<span class="path2"></span>
															<span class="path3"></span>
															<span class="path4"></span>
															<span class="path5"></span>
															<span class="path6"></span>
															<span class="path7"></span>
															<span class="path8"></span>
															<span class="path9"></span>
															<span class="path10"></span>
														</i>
													</span>
													<span class="menu-title">Light</span>
												</a>
											</div>
											<!--end::Menu item-->
											<!--begin::Menu item-->
											<div class="menu-item px-3 my-0">
												<a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
													<span class="menu-icon" data-kt-element="icon">
														<i class="ki-duotone ki-moon fs-2">
															<span class="path1"></span>
															<span class="path2"></span>
														</i>
													</span>
													<span class="menu-title">Dark</span>
												</a>
											</div>
											<!--end::Menu item-->
											<!--begin::Menu item-->
											<div class="menu-item px-3 my-0">
												<a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
													<span class="menu-icon" data-kt-element="icon">
														<i class="ki-duotone ki-screen fs-2">
															<span class="path1"></span>
															<span class="path2"></span>
															<span class="path3"></span>
															<span class="path4"></span>
														</i>
													</span>
													<span class="menu-title">System</span>
												</a>
											</div>
											<!--end::Menu item-->
										</div>
										<!--end::Menu-->
									</div>
									<!--end::Menu item-->
									<!--begin::Menu item-->
									<div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
										<a href="#" class="menu-link px-5">
											<span class="menu-title position-relative">Language
												<span class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">English
													<img class="w-15px h-15px rounded-1 ms-2" src="assets/media/flags/united-states.svg" alt="" /></span></span>
										</a>
										<!--begin::Menu sub-->
										<div class="menu-sub menu-sub-dropdown w-175px py-4">
											<!--begin::Menu item-->
											<div class="menu-item px-3">
												<a href="../../demo1/dist/account/settings.html" class="menu-link d-flex px-5 active">
													<span class="symbol symbol-20px me-4">
														<img class="rounded-1" src="assets/media/flags/united-states.svg" alt="" />
													</span>English</a>
											</div>
											<!--end::Menu item-->
											<!--begin::Menu item-->
											<div class="menu-item px-3">
												<a href="../../demo1/dist/account/settings.html" class="menu-link d-flex px-5">
													<span class="symbol symbol-20px me-4">
														<img class="rounded-1" src="assets/media/flags/spain.svg" alt="" />
													</span>Spanish</a>
											</div>
											<!--end::Menu item-->
											<!--begin::Menu item-->
											<div class="menu-item px-3">
												<a href="../../demo1/dist/account/settings.html" class="menu-link d-flex px-5">
													<span class="symbol symbol-20px me-4">
														<img class="rounded-1" src="assets/media/flags/germany.svg" alt="" />
													</span>German</a>
											</div>
											<!--end::Menu item-->
											<!--begin::Menu item-->
											<div class="menu-item px-3">
												<a href="../../demo1/dist/account/settings.html" class="menu-link d-flex px-5">
													<span class="symbol symbol-20px me-4">
														<img class="rounded-1" src="assets/media/flags/japan.svg" alt="" />
													</span>Japanese</a>
											</div>
											<!--end::Menu item-->
											<!--begin::Menu item-->
											<div class="menu-item px-3">
												<a href="../../demo1/dist/account/settings.html" class="menu-link d-flex px-5">
													<span class="symbol symbol-20px me-4">
														<img class="rounded-1" src="assets/media/flags/france.svg" alt="" />
													</span>French</a>
											</div>
											<!--end::Menu item-->
										</div>
										<!--end::Menu sub-->
									</div>
									<!--end::Menu item-->
									<!--begin::Menu item-->
									<div class="menu-item px-5 my-1">
										<a href="../../demo1/dist/account/settings.html" class="menu-link px-5">Account Settings</a>
									</div>
									<!--end::Menu item-->
									<!--begin::Menu item-->
									<div class="menu-item px-5">
										<a href="/logout" class="menu-link px-5">Sign Out</a>
									</div>
									<!--end::Menu item-->
								</div>
								<!--end::User account menu-->
								<!--end::Menu wrapper-->
							</div>
							<!--end::User menu-->
							<!--begin::Header menu toggle-->
							<div class="app-navbar-item d-lg-none ms-2 me-n2" title="Show header menu">
								<div class="btn btn-flex btn-icon btn-active-color-primary w-30px h-30px" id="kt_app_header_menu_toggle">
									<i class="ki-duotone ki-element-4 fs-1">
										<span class="path1"></span>
										<span class="path2"></span>
									</i>
								</div>
							</div>
							<!--end::Header menu toggle-->
							<!--begin::Aside toggle-->
							<!--end::Header menu toggle-->
						</div>
						<!--end::Navbar-->
					</div>
					<!--end::Header wrapper-->
				</div>
				<!--end::Header container-->
			</div>
			<!--end::Header-->
			<!--begin::Wrapper-->
			<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
				<!--begin::Sidebar-->
				<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
					<!--begin::Logo-->
					<div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
						<!--begin::Logo image-->
						<a href="/app.php">
							<img alt="Logo" src="assets/media/logos/logo-dark.png" class="h-50px app-sidebar-logo-default" />
							<img alt="Logo" src="assets/media/logos/android-chrome-192x192.png" class="h-20px app-sidebar-logo-minimize" />
						</a>
						<!--end::Logo image-->
						<!--begin::Sidebar toggle-->
						<!--begin::Minimized sidebar setup:
            if (isset($_COOKIE["sidebar_minimize_state"]) && $_COOKIE["sidebar_minimize_state"] === "on") {
                1. "src/js/layout/sidebar.js" adds "sidebar_minimize_state" cookie value to save the sidebar minimize state.
                2. Set data-kt-app-sidebar-minimize="on" attribute for body tag.
                3. Set data-kt-toggle-state="active" attribute to the toggle element with "kt_app_sidebar_toggle" id.
                4. Add "active" class to to sidebar toggle element with "kt_app_sidebar_toggle" id.
            }
        -->
						<div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
							<i class="ki-duotone ki-black-left-line fs-3 rotate-180">
								<span class="path1"></span>
								<span class="path2"></span>
							</i>
						</div>
						<!--end::Sidebar toggle-->
					</div>
					<!--end::Logo-->
					<!--begin::sidebar menu-->
					<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
						<!--begin::Menu wrapper-->
						<div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
							<!--begin::Scroll wrapper-->
							<div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
								<!--begin::Menu-->
								<div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
									<!--begin:Menu item-->
									<div data-kt-menu-trigger="click" class="menu-item here show menu-accordion">
										<!--begin:Menu link-->
										<span class="menu-link">
											<span class="menu-icon">
												<i class="ki-duotone ki-element-11 fs-2">
													<span class="path1"></span>
													<span class="path2"></span>
													<span class="path3"></span>
													<span class="path4"></span>
												</i>
											</span>
											<span class="menu-title">History</span>
											<span class="menu-arrow"></span>
										</span>
										<!--end:Menu link-->
										<!--begin:Menu sub-->
										<div class="menu-sub menu-sub-accordion">
											<!--begin:Menu item-->
											<div class="menu-item">
												<!--begin:Menu link-->
												<a class="menu-link" href="/voice.php">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
													<span class="menu-title">yupin-test1.mp3</span>
												</a>
												<!--end:Menu link-->
											</div>
											<div class="menu-item">
												<!--begin:Menu link-->
												<a class="menu-link" href="/app.php">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
													<span class="menu-title">yupin-test2.mp3</span>
												</a>
												<!--end:Menu link-->
											</div>
											<div class="menu-item">
												<!--begin:Menu link-->
												<a class="menu-link" href="/app.php">
													<span class="menu-bullet">
														<span class="bullet bullet-dot"></span>
													</span>
													<span class="menu-title">ElevenLabs_2023-07-29T13_27_20.000Z_valentino deep-potential_6jUv0V9l5HUrvMWzDKQk.mp3</span>
												</a>
												<!--end:Menu link-->
											</div>

											<!--end:Menu item-->
											<!--begin:Menu item-->

											<!-- <div class="menu-item">
												<div class="menu-content">
													<a class="btn btn-flex btn-color-primary d-flex flex-stack fs-base p-0 ms-2 mb-2 toggle collapsible active" data-bs-toggle="collapse" href="#kt_app_sidebar_menu_dashboards_collapse" data-kt-toggle-text="Show 12 More">
														<span data-kt-toggle-text-target="true">Show Less</span>
														<i class="ki-duotone ki-minus-square toggle-on fs-2 me-0">
															<span class="path1"></span>
															<span class="path2"></span>
														</i>
														<i class="ki-duotone ki-plus-square toggle-off fs-2 me-0">
															<span class="path1"></span>
															<span class="path2"></span>
															<span class="path3"></span>
														</i>
													</a>
												</div>
											</div> -->
										</div>
										<!--end:Menu sub-->
									</div>
									<!--end:Menu item-->
									<!--begin:Menu item-->
									<!--end:Menu item-->
									<!--begin:Menu item-->

								</div>
								<!--end::Menu-->
							</div>
							<!--end::Scroll wrapper-->
						</div>
						<!--end::Menu wrapper-->
					</div>
					<!--end::sidebar menu-->
					<!--begin::Footer-->
					<!-- <div class="app-sidebar-footer flex-column-auto pt-2 pb-6 px-6" id="kt_app_sidebar_footer">
						<a href="https://preview.keenthemes.com/html/metronic/docs" class="btn btn-flex flex-center btn-custom btn-primary overflow-hidden text-nowrap px-0 h-40px w-100" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss-="click" title="200+ in-house components and 3rd-party plugins">
							<span class="btn-label">Docs & Components</span>
							<i class="ki-duotone ki-document btn-icon fs-2 m-0">
								<span class="path1"></span>
								<span class="path2"></span>
							</i>
						</a>
					</div> -->
					<!--end::Footer-->
				</div>
				<!--end::Sidebar-->
				<!--begin::Main-->
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
									<!-- <form class="form mt-5" action="reveal.php" method="post">
										<div class="fv-row">
											<div class="dropzone pt-10 pb-10" id="kt_dropzonejs_example_1" style="border: 1px dashed #9b00ff; background-color: #000000;">
												<div class="dz-message needsclick">
													<i class="ki-duotone ki-file-up fs-3x text-primary"><span class="path1"></span><span class="path2"></span></i>
													<div class="ms-4">
														<h3 class="fs-3 fw-bold text-gray-900 mb-1">Drop files here or click to upload.</h3>
														<span class="fs-7 fw-semibold text-gray-400">Upload your voice file here</span>
													</div>
												</div>
											</div>
											<button type="submit" class="btn btn-primary">Submit</button>
										</div>
									</form> -->
									<script>
										// var myDropzone = new Dropzone("#kt_dropzonejs_example_1", {
										// 	url: "upload.php",
										// 	paramName: "file",
										// 	maxFiles: 1,
										// 	maxFilesize: 10,
										// 	addRemoveLinks: true,
										// 	autoProcessQueue: false,
										// 	accept: function(file, done) {
										// 		if (file.name == "wow.jpg") {
										// 			done("Naha, you don't.");
										// 		} else {
										// 			done();
										// 		}
										// 	}
										// });

										// document.querySelector(".form").addEventListener("submit", function(e) {
										// 	e.preventDefault();

										// 	if (myDropzone.getQueuedFiles().length > 0) {
										// 		myDropzone.processQueue();
										// 	} else {
										// 		this.submit();
										// 	}
										// });

										// myDropzone.on("success", function() {
										// 	document.querySelector(".form").submit();
										// });

										// myDropzone.on("error", function(file, errorMessage) {
										// 	console.error("File upload error:", errorMessage);
										// });
									</script>


								</div>
								<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
									<form class="form mt-5" action="reveal.php" method="post" enctype="multipart/form-data" id="audios">
										<div class="fv-row">
											<!-- Hidden actual file input -->
											<input type="file" name="files[]" id="fileInput" multiple style="display: none;">

											<!-- Custom styled div/button for file selection -->
											<div class="custom-file-upload" style="border: 1px dashed #9b00ff; background-color: #000000; padding: 10px; text-align: center; cursor: pointer;">
												<i class="ki-duotone ki-file-up fs-3x text-primary"></i>
												<div class="ms-4 pb-5">
													<h3 class="fs-3 fw-bold text-gray-900 mb-1">Drop files here or click to upload.</h3>
													<span class="fs-7 fw-semibold text-gray-400" id="fileNames">Upload your voice files here</span>
												</div>
											</div>

											<button type="submit" class="btn btn-primary mt-3">Submit</button>
										</div>
									</form>

									<script>
										document.querySelector(".custom-file-upload").addEventListener("click", function() {
											document.getElementById("fileInput").click(); // Trigger the hidden file input click event
										});

										document.getElementById("fileInput").addEventListener("change", function() {
											const selectedFiles = Array.from(this.files);
											const fileNames = selectedFiles.map(file => file.name).join(", ");
											document.getElementById("fileNames").textContent = fileNames; // Display selected file names
										});

										const dropzone = document.querySelector(".custom-file-upload");

										// Highlight dropzone when file is dragged over it
										dropzone.addEventListener("dragover", function(e) {
											e.preventDefault();
											this.style.backgroundColor = "#222222"; // Change to any highlight color you prefer
										});

										// Reset dropzone styling when file is dragged out
										dropzone.addEventListener("dragleave", function(e) {
											e.preventDefault();
											this.style.backgroundColor = "#000000"; // Change to original color
										});

										// Handle the files once they're dropped
										dropzone.addEventListener("drop", function(e) {
											e.preventDefault();
											this.style.backgroundColor = "#000000"; // Change to original color

											// Set dropped files as input files
											const files = e.dataTransfer.files;
											document.getElementById("fileInput").files = files;

											// Update the text to show the selected file names
											const fileNames = Array.from(files).map(file => file.name).join(", ");
											document.getElementById("fileNames").textContent = fileNames;
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
														<select id="kt_filter_year" name="year" data-control="select2" data-hide-search="true" class="w-125px form-select form-select-solid form-select-sm">
															<option value="All" selected="selected">Gender</option>
															<option value="thisyear">Male</option>
															<option value="thismonth">Female</option>
															<option value="lastmonth">Other</option>
														</select>
													</div>
													<!--end::Select-->
													<!--begin::Select-->
													<div class="me-4 my-1">
														<select id="kt_filter_orders" name="orders" data-control="select2" data-hide-search="true" class="w-125px form-select form-select-solid form-select-sm">
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
														<input type="text" id="kt_filter_search" class="form-control form-control-solid form-select-sm w-150px ps-9" placeholder="Search Voices" />
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
															$sql = "SELECT * FROM files";
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




																	// get first letter from $row['name']
																	$firstLetter = mb_substr($row['original_name'], 0, 1);

																	$backgroundColor = getLetterStyle($firstLetter, $letterStyles);
																	$icon = "<div class='me-5 position-relative'><div class='symbol symbol-35px symbol-circle'>
															   <span class='symbol-label' style='background-color: $backgroundColor; color: white; font-weight: bold;'>$firstLetter</span>
															</div></div>";

																	echo '
<tr>
	<td>
		<div class="d-flex align-items-center">
			' . $icon . '
			<div class="d-flex flex-column justify-content-center">
				<a href="" class="fs-6 text-gray-800 text-hover-primary">' . $original_name . '</a>
				<div class="fw-semibold text-gray-400">' . $url . '</div>
			</div>
		</div>
	</td>
	<td>' . $created_at . '</td>
	<td><audio controls>
	<source src="samples/' . $row['name'] . '.mp3" type="audio/mpeg">
	Your browser does not support the audio tag.
</audio></td>
	<td>
		<span class="badge badge-light-success fw-bold px-4 py-3">Online</span>
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
														document.addEventListener('DOMContentLoaded', function() {
															// Get all the select buttons in the table
															const selectButtons = document.querySelectorAll('a.btn');

															selectButtons.forEach(button => {
																button.addEventListener('click', function(event) {
																	event.preventDefault(); // Prevent any default behavior

																	// Find the corresponding name value from the hidden input
																	const row = this.closest('tr');
																	const hiddenInput = row.querySelector('input[type="hidden"]');
																	const nameValue = hiddenInput ? hiddenInput.value : null;

																	// Get the pitch value from the span
																	const pitchElement = document.getElementById('kt_modal_create_campaign_budget_label');
																	const pitchValue = pitchElement ? pitchElement.textContent.trim() : null;

																	// If both values were found, add them to the form and submit it
																	if (nameValue && pitchValue) {
																		// Create hidden inputs for name and pitch
																		const nameInput = document.createElement('input');
																		nameInput.type = 'hidden';
																		nameInput.name = 'name';
																		nameInput.value = nameValue;

																		const pitchInput = document.createElement('input');
																		pitchInput.type = 'hidden';
																		pitchInput.name = 'pitch';
																		pitchInput.value = pitchValue;

																		// Append to the form and submit
																		const form = document.getElementById('audios');
																		form.appendChild(nameInput);
																		form.appendChild(pitchInput);
																		form.submit();
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
									<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
										<!--begin::Col-->
										<div class="col-xl-12">
											<!--begin::Player widget 1-->
											<div class="card card-flush h-xl-100">
												<!--begin::Header-->
												<div class="card-header pt-7">
													<!--begin::Title-->
													<h3 class="card-title align-items-start flex-column">
														<span class="card-label fw-bold text-dark">Pick a voice</span>
														<span class="text-gray-400 mt-1 fw-semibold fs-6">Updated 37 minutes ago</span>
													</h3>
													<!--end::Title-->
													<!--begin::Toolbar-->
													<div class="card-toolbar">
														<!--begin::Filters-->
														<div class="d-flex flex-stack flex-wrap gap-4">
															<!--begin::Destination-->
															<div class="d-flex align-items-center fw-bold">
																<!--begin::Label-->
																<div class="text-gray-400 fs-7 me-2">Gender</div>
																<!--end::Label-->

																<!--begin::Select-->
																<select class="form-select form-select-transparent text-graY-800 fs-base lh-1 fw-bold py-0 ps-3 w-auto select2-hidden-accessible" data-control="select2" data-hide-search="true" data-dropdown-css-class="w-150px" data-placeholder="Select an option" data-select2-id="select2-data-7-my9g" tabindex="-1" aria-hidden="true" data-kt-initialized="1">
																	<option></option>
																	<option value="Show All" selected="" data-select2-id="select2-data-9-wkzs">Show All</option>
																	<option value="a">Category A</option>
																	<option value="b">Category A</option>
																</select><span class="select2 select2-container select2-container--bootstrap5" dir="ltr" data-select2-id="select2-data-8-239u" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single form-select form-select-transparent text-graY-800 fs-base lh-1 fw-bold py-0 ps-3 w-auto" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-cftg-container" aria-controls="select2-cftg-container"><span class="select2-selection__rendered" id="select2-cftg-container" role="textbox" aria-readonly="true" title="Show All">Show All</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
																<!--end::Select-->
															</div>
															<!--end::Destination-->

															<!--begin::Status-->
															<div class="d-flex align-items-center fw-bold">
																<!--begin::Label-->
																<div class="text-gray-400 fs-7 me-2">Age</div>
																<!--end::Label-->

																<!--begin::Select-->
																<select class="form-select form-select-transparent text-dark fs-7 lh-1 fw-bold py-0 ps-3 w-auto select2-hidden-accessible" data-control="select2" data-hide-search="true" data-dropdown-css-class="w-150px" data-placeholder="Select an option" data-kt-table-widget-4="filter_status" data-select2-id="select2-data-10-4h2n" tabindex="-1" aria-hidden="true" data-kt-initialized="1">
																	<option></option>
																	<option value="Show All" selected="" data-select2-id="select2-data-12-5437">Show All</option>
																	<option value="Shipped">Shipped</option>
																	<option value="Confirmed">Confirmed</option>
																	<option value="Rejected">Rejected</option>
																	<option value="Pending">Pending</option>
																</select><span class="select2 select2-container select2-container--bootstrap5" dir="ltr" data-select2-id="select2-data-11-c38b" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single form-select form-select-transparent text-dark fs-7 lh-1 fw-bold py-0 ps-3 w-auto" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-7e7r-container" aria-controls="select2-7e7r-container"><span class="select2-selection__rendered" id="select2-7e7r-container" role="textbox" aria-readonly="true" title="Show All">Show All</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
																<!--end::Select-->
															</div>
															<!--end::Status-->


															<div class="d-flex align-items-center fw-bold">
																<!--begin::Label-->
																<div class="text-gray-400 fs-7 me-2">Accent</div>
																<!--end::Label-->

																<!--begin::Select-->
																<select class="form-select form-select-transparent text-dark fs-7 lh-1 fw-bold py-0 ps-3 w-auto select2-hidden-accessible" data-control="select2" data-hide-search="true" data-dropdown-css-class="w-150px" data-placeholder="Select an option" data-kt-table-widget-4="filter_status" data-select2-id="select2-data-10-4h2n" tabindex="-1" aria-hidden="true" data-kt-initialized="1">
																	<option></option>
																	<option value="Show All" selected="" data-select2-id="select2-data-12-5437">Show All</option>
																	<option value="Shipped">Shipped</option>
																	<option value="Confirmed">Confirmed</option>
																	<option value="Rejected">Rejected</option>
																	<option value="Pending">Pending</option>
																</select><span class="select2 select2-container select2-container--bootstrap5" dir="ltr" data-select2-id="select2-data-11-c38b" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single form-select form-select-transparent text-dark fs-7 lh-1 fw-bold py-0 ps-3 w-auto" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-7e7r-container" aria-controls="select2-7e7r-container"><span class="select2-selection__rendered" id="select2-7e7r-container" role="textbox" aria-readonly="true" title="Show All">Show All</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
																<!--end::Select-->
															</div>



															<!--begin::Search-->
															<div class="position-relative my-1">
																<i class="ki-duotone ki-magnifier fs-2 position-absolute top-50 translate-middle-y ms-4"><span class="path1"></span><span class="path2"></span></i> <input type="text" data-kt-table-widget-4="search" class="form-control w-150px fs-7 ps-12" placeholder="Search">
															</div>
															<!--end::Search-->
														</div>
														<!--begin::Filters-->
													</div>
													<!--end::Toolbar-->
												</div>
												<!--end::Header-->
												<!--begin::Card body-->
												<div class="card-body pt-7">
													<!--begin::Row-->
													<div class="row g-5 g-xl-9 mb-5 mb-xl-9">
														<!--begin::Col-->
														<div class="col-sm-3 mb-3 mb-sm-0">
															<!--begin::Player card-->
															<div class="m-0 cursor-pointer" onclick="window.location.href='/voice.php';">
																<!--begin::User pic-->
																<div class="card-rounded position-relative mb-5">
																	<!--begin::Img-->
																	<div class="bgi-position-center bgi-no-repeat bgi-size-cover h-200px card-rounded" style="background-image:url('assets/media/stock/600x600/yupin.png')"></div>
																	<!--end::Img-->
																	<!--begin::Play-->
																	<button class="btn btn-icon h-auto w-auto p-0 ms-4 mb-4 position-absolute bottom-0 right-0" data-kt-element="list-play-button">
																		<i class="bi bi-play-fill text-white fs-2x" data-kt-element="list-play-icon"></i>
																		<i class="bi bi-pause-fill text-white fs-2x d-none" data-kt-element="list-pause-icon"></i>
																	</button>
																	<!--end::Play-->
																</div>
																<!--end::User pic-->
																<!--begin::Info-->
																<div class="m-0 cursor-pointer" onclick="window.location.href='/voice.php';">
																	<div class="row">
																		<div class="col-8"><a href="#" class="text-gray-800 text-hover-primary fs-3 fw-bold d-block mb-2">Yupin</a>
																			<!--end::Title-->
																			<span class="fw-bold fs-6 text-gray-400 d-block lh-1">Private Voice Model</span>
																		</div>
																		<div class="col-4">
																			<a class="btn btn-primary">Select</a>

																		</div>
																	</div>
																	<!--begin::Title-->

																</div>
																<!--end::Info-->
															</div>
															<!--end::Player card-->
														</div>
														<!--end::Col-->
														<!--begin::Col-->
														<div class="col-sm-3 mb-3 mb-sm-0">
															<!--begin::Player card-->
															<div class="m-0 cursor-pointer" onclick="window.location.href='/voice.php';">
																<!--begin::User pic-->
																<div class="card-rounded position-relative mb-5">
																	<!--begin::Img-->
																	<div class="bgi-position-center bgi-no-repeat bgi-size-cover h-200px card-rounded" style="background-image:url('assets/media/stock/600x600/santa.jpeg')"></div>
																	<!--end::Img-->
																	<!--begin::Play-->
																	<button class="btn btn-icon h-auto w-auto p-0 ms-4 mb-4 position-absolute bottom-0 right-0" data-kt-element="list-play-button">
																		<i class="bi bi-play-fill text-white fs-2x" data-kt-element="list-play-icon"></i>
																		<i class="bi bi-pause-fill text-white fs-2x d-none" data-kt-element="list-pause-icon"></i>
																	</button>
																	<!--end::Play-->
																</div>
																<!--end::User pic-->
																<!--begin::Info-->
																<div class="m-0 cursor-pointer" onclick="window.location.href='/voice.php';">
																	<!--begin::Title-->
																	<a href="#" class="text-gray-800 text-hover-primary fs-3 fw-bold d-block mb-2">Santa</a>
																	<!--end::Title-->
																	<span class="fw-bold fs-6 text-gray-400 d-block lh-1">Ho Ho Ho</span>
																</div>
																<!--end::Info-->
															</div>
															<!--end::Player card-->
														</div>
														<!--end::Col-->
														<!--begin::Col-->
														<div class="col-sm-3 mb-3 mb-sm-0">
															<!--begin::Player card-->
															<div class="m-0 cursor-pointer" onclick="window.location.href='/voice.php';">
																<!--begin::User pic-->
																<div class="card-rounded position-relative mb-5">
																	<!--begin::Img-->
																	<div class="bgi-position-center bgi-no-repeat bgi-size-cover h-200px card-rounded" style="background-image:url('assets/media/stock/600x600/img-63.jpg')"></div>
																	<!--end::Img-->
																	<!--begin::Play-->
																	<button class="btn btn-icon h-auto w-auto p-0 ms-4 mb-4 position-absolute bottom-0 right-0" data-kt-element="list-play-button">
																		<i class="bi bi-play-fill text-white fs-2x" data-kt-element="list-play-icon"></i>
																		<i class="bi bi-pause-fill text-white fs-2x d-none" data-kt-element="list-pause-icon"></i>
																	</button>
																	<!--end::Play-->
																</div>
																<!--end::User pic-->
																<!--begin::Info-->
																<div class="m-0 cursor-pointer" onclick="window.location.href='/voice.php';">
																	<!--begin::Title-->
																	<a href="#" class="text-gray-800 text-hover-primary fs-3 fw-bold d-block mb-2">Robert Fox</a>
																	<!--end::Title-->
																	<span class="fw-bold fs-6 text-gray-400 d-block lh-1">Male Middle-aged American</span>
																</div>
																<!--end::Info-->
															</div>
															<!--end::Player card-->
														</div>
														<!--end::Col-->
														<!--begin::Col-->
														<div class="col-sm-3 mb-3 mb-sm-0">
															<!--begin::Player card-->
															<div class="m-0 cursor-pointer" onclick="window.location.href='/voice.php';">
																<!--begin::User pic-->
																<div class="card-rounded position-relative mb-5">
																	<!--begin::Img-->
																	<div class="bgi-position-center bgi-no-repeat bgi-size-cover h-200px card-rounded" style="background-image:url('assets/media/stock/600x600/img-61.jpg')"></div>
																	<!--end::Img-->
																	<!--begin::Play-->
																	<button class="btn btn-icon h-auto w-auto p-0 ms-4 mb-4 position-absolute bottom-0 right-0" data-kt-element="list-play-button">
																		<i class="bi bi-play-fill text-white fs-2x" data-kt-element="list-play-icon"></i>
																		<i class="bi bi-pause-fill text-white fs-2x d-none" data-kt-element="list-pause-icon"></i>
																	</button>
																	<!--end::Play-->
																</div>
																<!--end::User pic-->
																<!--begin::Info-->
																<div class="m-0 cursor-pointer" onclick="window.location.href='/voice.php';">
																	<!--begin::Title-->
																	<a href="#" class="text-gray-800 text-hover-primary fs-3 fw-bold d-block mb-2">Lisa Jones</a>
																	<!--end::Title-->
																	<span class="fw-bold fs-6 text-gray-400 d-block lh-1">Female Young British</span>
																</div>
																<!--end::Info-->
															</div>
															<!--end::Player card-->
														</div>
														<!--end::Col-->
													</div>
													<!--end::Row-->
													<!--begin::Row-->
													<div class="row g-5 g-xl-9 mb-xl-3">
														<!--begin::Col-->
														<div class="col-sm-3 mb-3 mb-sm-0">
															<!--begin::Player card-->
															<div class="m-0 cursor-pointer" onclick="window.location.href='/voice.php';">
																<!--begin::User pic-->
																<div class="card-rounded position-relative mb-5">
																	<!--begin::Img-->
																	<div class="bgi-position-center bgi-no-repeat bgi-size-cover h-200px card-rounded" style="background-image:url('assets/media/stock/600x600/img-57.jpg')"></div>
																	<!--end::Img-->
																	<!--begin::Play-->
																	<button class="btn btn-icon h-auto w-auto p-0 ms-4 mb-4 position-absolute bottom-0 right-0" data-kt-element="list-play-button">
																		<i class="bi bi-play-fill text-white fs-2x" data-kt-element="list-play-icon"></i>
																		<i class="bi bi-pause-fill text-white fs-2x d-none" data-kt-element="list-pause-icon"></i>
																	</button>
																	<!--end::Play-->
																</div>
																<!--end::User pic-->
																<!--begin::Info-->
																<div class="m-0 cursor-pointer" onclick="window.location.href='/voice.php';">
																	<!--begin::Title-->
																	<a href="#" class="text-gray-800 text-hover-primary fs-3 fw-bold d-block mb-2">It is what it is</a>
																	<!--end::Title-->
																	<span class="fw-bold fs-6 text-gray-400 d-block lh-1">Jane Cooper</span>
																</div>
																<!--end::Info-->
															</div>
															<!--end::Player card-->
														</div>
														<!--end::Col-->
														<!--begin::Col-->
														<div class="col-sm-3 mb-3 mb-sm-0">
															<!--begin::Player card-->
															<div class="m-0 cursor-pointer" onclick="window.location.href='/voice.php';">
																<!--begin::User pic-->
																<div class="card-rounded position-relative mb-5">
																	<!--begin::Img-->
																	<div class="bgi-position-center bgi-no-repeat bgi-size-cover h-200px card-rounded" style="background-image:url('assets/media/stock/600x600/img-58.jpg')"></div>
																	<!--end::Img-->
																	<!--begin::Play-->
																	<button class="btn btn-icon h-auto w-auto p-0 ms-4 mb-4 position-absolute bottom-0 right-0" data-kt-element="list-play-button">
																		<i class="bi bi-play-fill text-white fs-2x" data-kt-element="list-play-icon"></i>
																		<i class="bi bi-pause-fill text-white fs-2x d-none" data-kt-element="list-pause-icon"></i>
																	</button>
																	<!--end::Play-->
																</div>
																<!--end::User pic-->
																<!--begin::Info-->
																<div class="m-0 cursor-pointer" onclick="window.location.href='/voice.php';">
																	<!--begin::Title-->
																	<a href="#" class="text-gray-800 text-hover-primary fs-3 fw-bold d-block mb-2">Broken Mirros</a>
																	<!--end::Title-->
																	<span class="fw-bold fs-6 text-gray-400 d-block lh-1">Jenny Wilson</span>
																</div>
																<!--end::Info-->
															</div>
															<!--end::Player card-->
														</div>
														<!--end::Col-->
														<!--begin::Col-->
														<div class="col-sm-3 mb-3 mb-sm-0">
															<!--begin::Player card-->
															<div class="m-0 cursor-pointer" onclick="window.location.href='/voice.php';">
																<!--begin::User pic-->
																<div class="card-rounded position-relative mb-5">
																	<!--begin::Img-->
																	<div class="bgi-position-center bgi-no-repeat bgi-size-cover h-200px card-rounded" style="background-image:url('assets/media/stock/600x600/img-55.jpg')"></div>
																	<!--end::Img-->
																	<!--begin::Play-->
																	<button class="btn btn-icon h-auto w-auto p-0 ms-4 mb-4 position-absolute bottom-0 right-0" data-kt-element="list-play-button">
																		<i class="bi bi-play-fill text-white fs-2x" data-kt-element="list-play-icon"></i>
																		<i class="bi bi-pause-fill text-white fs-2x d-none" data-kt-element="list-pause-icon"></i>
																	</button>
																	<!--end::Play-->
																</div>
																<!--end::User pic-->
																<!--begin::Info-->
																<div class="m-0 cursor-pointer" onclick="window.location.href='/voice.php';">
																	<!--begin::Title-->
																	<a href="#" class="text-gray-800 text-hover-primary fs-3 fw-bold d-block mb-2">The Hood</a>
																	<!--end::Title-->
																	<span class="fw-bold fs-6 text-gray-400 d-block lh-1">Albert Flores</span>
																</div>
																<!--end::Info-->
															</div>
															<!--end::Player card-->
														</div>
														<!--end::Col-->
														<!--begin::Col-->
														<div class="col-sm-3">
															<!--begin::Player card-->
															<div class="m-0 cursor-pointer" onclick="window.location.href='/voice.php';">
																<!--begin::User pic-->
																<div class="card-rounded position-relative mb-5">
																	<!--begin::Img-->
																	<div class="bgi-position-center bgi-no-repeat bgi-size-cover h-200px card-rounded" style="background-image:url('assets/media/stock/600x600/img-64.jpg')"></div>
																	<!--end::Img-->
																	<!--begin::Play-->
																	<button class="btn btn-icon h-auto w-auto p-0 ms-4 mb-4 position-absolute bottom-0 right-0" data-kt-element="list-play-button">
																		<i class="bi bi-play-fill text-white fs-2x" data-kt-element="list-play-icon"></i>
																		<i class="bi bi-pause-fill text-white fs-2x d-none" data-kt-element="list-pause-icon"></i>
																	</button>
																	<!--end::Play-->
																</div>
																<!--end::User pic-->
																<!--begin::Info-->
																<div class="m-0 cursor-pointer" onclick="window.location.href='/voice.php';">
																	<!--begin::Title-->
																	<a href="#" class="text-gray-800 text-hover-primary fs-3 fw-bold d-block mb-2">Cirle Lights</a>
																	<!--end::Title-->
																	<span class="fw-bold fs-6 text-gray-400 d-block lh-1">Dianne Russell</span>
																</div>
																<!--end::Info-->
															</div>
															<!--end::Player card-->
														</div>
														<!--end::Col-->
													</div>
													<!--end::Row-->
												</div>
												<!--end::Card body-->
											</div>
											<!--end::Player widget 1-->
										</div>
										<!--end::Col-->
										<!--begin::Col-->

										<!--end::Col-->
									</div>
									<!--end::Row-->


















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
										<a href="https://devs.keenthemes.com" target="_blank" class="menu-link px-2">Support</a>
									</li>
									<!-- footerlink -->
								</ul>
								<!--end::Menu-->
							</div>
							<!--end::Footer container-->
						</div>
						<!--end::Footer-->
						<!-- <script src="assets/js/custom/documentation/forms/nouislider.js"></script> -->
						<?php include 'footer.php'; ?>