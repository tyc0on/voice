<?php
// Use the $pageConfig array to set metadata like title
$title = $sitename . " - " . $siteslogan ?? $sitename;
?>
<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <base href="/" />
    <title><?php echo $title; ?></title>
    <meta name="description" content="<?php echo $sitename; ?> offers cutting-edge <?php echo $siteslogan; ?>, One Click Away" />
    <meta name="keywords" content="Easy Ai, <?php echo $sitename; ?>, AI, Artificial Intelligence, Productivity, Automation, Efficiency, Business Solutions, Easy-to-use, Data Analysis, Innovation, Streamline, Operations, Success" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="<?php echo $sitename; ?> - <?php echo $siteslogan; ?>" />
    <meta property="og:url" content="https://<?php echo $siteurl; ?>" />
    <meta property="og:site_name" content="<?php echo $sitename; ?>" />
    <link rel="canonical" href="https://<?php echo $siteurl; ?>" />
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
    <?php echo $head; ?>
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_app_body" <?php echo $bodysettings; ?> class="app-default dark-mode">
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
                        <?php echo $menu1; ?>
                        <!--end::Menu wrapper-->
                        <!--begin::Navbar-->
                        <div class="app-navbar flex-shrink-0">
                            <!--begin::Search-->

                            <!--end::My apps links-->
                            <!--begin::User menu-->
                            <div class="app-navbar-item ms-1 ms-md-4" id="kt_header_user_menu_toggle">
                                <!--begin::Menu wrapper-->
                                <div class="cursor-pointer symbol symbol-35px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                    <img src="<?php if (@$_SESSION['picture'] != "") {
                                                    echo @$_SESSION['picture'];
                                                } else {
                                                    echo 'https://www.gravatar.com/avatar/' . md5(@strtolower($_SESSION['email']));
                                                } ?>" class="rounded-3" alt="user" />
                                </div>
                                <!--begin::User account menu-->
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <div class="menu-content d-flex align-items-center px-3">
                                            <!--begin::Avatar-->
                                            <div class="symbol symbol-50px me-5">
                                                <img alt="Logo" src="<?php if (@$_SESSION['picture'] != "") {
                                                                            echo @$_SESSION['picture'];
                                                                        } else {
                                                                            echo 'https://www.gravatar.com/avatar/' . md5(@strtolower($_SESSION['email']));
                                                                        } ?>" />
                                            </div>
                                            <!--end::Avatar-->
                                            <!--begin::Username-->
                                            <div class="d-flex flex-column">
                                                <div class="fw-bold d-flex align-items-center fs-5"><?php echo $_SESSION['name']; ?>
                                                    <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2"><?php echo ucfirst($_SESSION['accounttype']); ?></span>
                                                </div>
                                                <a href="#" class="fw-semibold text-muted text-hover-primary fs-7"><?php echo $_SESSION['email']; ?></a>
                                            </div>
                                            <!--end::Username-->
                                        </div>
                                    </div>

                                    <div class="separator my-2"></div>
                                    <!-- <div class="menu-item px-5">
                                        <a href="../../demo1/dist/account/overview.html" class="menu-link px-5">My Profile</a>
                                    </div>
                                    <div class="menu-item px-5">
                                        <a href="../../demo1/dist/apps/projects/list.html" class="menu-link px-5">
                                            <span class="menu-text">My Projects</span>
                                            <span class="menu-badge">
                                                <span class="badge badge-light-danger badge-circle fw-bold fs-7">3</span>
                                            </span>
                                        </a>
                                    </div>
                                    <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                                        <a href="#" class="menu-link px-5">
                                            <span class="menu-title">My Subscription</span>
                                            <span class="menu-arrow"></span>
                                        </a>
                                        <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                            <div class="menu-item px-3">
                                                <a href="../../demo1/dist/account/referrals.html" class="menu-link px-5">Referrals</a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="../../demo1/dist/account/billing.html" class="menu-link px-5">Billing</a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="../../demo1/dist/account/statements.html" class="menu-link px-5">Payments</a>
                                            </div>
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
                                            <div class="separator my-2"></div>
                                            <div class="menu-item px-3">
                                                <div class="menu-content px-3">
                                                    <label class="form-check form-switch form-check-custom form-check-solid">
                                                        <input class="form-check-input w-30px h-20px" type="checkbox" value="1" checked="checked" name="notifications" />
                                                        <span class="form-check-label text-muted fs-7">Notifications</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="menu-item px-5">
                                        <a href="../../demo1/dist/account/statements.html" class="menu-link px-5">My Statements</a>
                                    </div>
                                    <div class="separator my-2"></div> -->
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
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px" data-kt-menu="true" data-kt-element="theme-mode-menu">
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
                                        </div>
                                    </div>
                                    <!-- <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                                        <a href="#" class="menu-link px-5">
                                            <span class="menu-title position-relative">Language
                                                <span class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">English
                                                    <img class="w-15px h-15px rounded-1 ms-2" src="assets/media/flags/united-states.svg" alt="" /></span></span>
                                        </a>
                                        <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                            <div class="menu-item px-3">
                                                <a href="../../demo1/dist/account/settings.html" class="menu-link d-flex px-5 active">
                                                    <span class="symbol symbol-20px me-4">
                                                        <img class="rounded-1" src="assets/media/flags/united-states.svg" alt="" />
                                                    </span>English</a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="../../demo1/dist/account/settings.html" class="menu-link d-flex px-5">
                                                    <span class="symbol symbol-20px me-4">
                                                        <img class="rounded-1" src="assets/media/flags/spain.svg" alt="" />
                                                    </span>Spanish</a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="../../demo1/dist/account/settings.html" class="menu-link d-flex px-5">
                                                    <span class="symbol symbol-20px me-4">
                                                        <img class="rounded-1" src="assets/media/flags/germany.svg" alt="" />
                                                    </span>German</a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="../../demo1/dist/account/settings.html" class="menu-link d-flex px-5">
                                                    <span class="symbol symbol-20px me-4">
                                                        <img class="rounded-1" src="assets/media/flags/japan.svg" alt="" />
                                                    </span>Japanese</a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="../../demo1/dist/account/settings.html" class="menu-link d-flex px-5">
                                                    <span class="symbol symbol-20px me-4">
                                                        <img class="rounded-1" src="assets/media/flags/france.svg" alt="" />
                                                    </span>French</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="menu-item px-5 my-1">
                                        <a href="../../demo1/dist/account/settings.html" class="menu-link px-5">Account Settings</a>
                                    </div> -->
                                    <div class="menu-item px-5">
                                        <a href="#" class="menu-link px-5 text-muted position-relative" style="cursor: not-allowed; pointer-events: none; opacity: 0.6;" title="Storing files and running voice models is expensive. To get priority in the queue and keep all your files, we will soon be offering an upgraded plan with enhanced features and storage.">
                                            <span class="menu-text">Upgrade Plan</span>
                                            <span class="badge badge-light-warning fw-bold fs-8 px-2 py-1 ms-2">Coming Soon</span>
                                        </a>
                                    </div>
                                    <div class="separator my-2"></div>
                                    <div class="menu-item px-5">
                                        <a href="/logout" class="menu-link px-5">Sign Out</a>
                                    </div>

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
                        <a href="/app">
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
                    <?php echo $menu2; ?>
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