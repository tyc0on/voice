<?php
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 365);
session_start();
include('include.php');
include('variables.php');
// if (isset($_SESSION['loggedin'])) {
//     if (isset($_SESSION['return_url'])) {
//         unset($_SESSION['return_url']);
//         header('Location: ' . $_SESSION['return_url']);
//     } else {
//         header('Location: ' . $siteapp);
//     }

//     exit;
// } else {
//     $con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

//     if ($con->connect_errno) {
//         printf("connection failed: %s\n", $con->connect_error());
//         exit();
//     }

//     if (isset($_COOKIE['remember_token'])) {
//         $remember_token = $_COOKIE['remember_token'];
//         if ($stmt = $con->prepare('SELECT id, accounttype, username, email, fullname, picture FROM accounts WHERE remember_token = ?')) {
//             $stmt->bind_param('s', $remember_token);
//             $stmt->execute();
//             $stmt->store_result();

//             if ($stmt->num_rows > 0) {
//                 $stmt->bind_result($id, $accounttype, $setusername, $email, $name, $picture);
//                 $stmt->fetch();
//                 session_regenerate_id();
//                 $_SESSION['loggedin'] = TRUE;
//                 $_SESSION['name'] = $name;
//                 $_SESSION['email'] = $email;
//                 $_SESSION['id'] = $id;
//                 $_SESSION['accounttype'] = $accounttype;
//                 $_SESSION['picture'] = $picture;
//                 $loggedin = "true";

//                 if ($stmt2 = $con->prepare('SELECT camount FROM credits WHERE cuserid = ?')) {
//                     // Bind parameters (s = string, i = int, b = blob, etc), in our case the email is a string so we use "s"
//                     $stmt2->bind_param('i', $id);
//                     $stmt2->execute();
//                     // Store the result so we can check if the account exists in the database.
//                     $stmt2->store_result();

//                     $total = 0;
//                     if (!empty($stmt2->num_rows)) {
//                         for ($i = 0; $i < $stmt2->num_rows; $i++) {
//                             $stmt2->bind_result($camount);
//                             $stmt2->fetch();
//                             $total += $camount;
//                         }
//                     }
//                     $_SESSION['credits'] =  $total;
//                     $stmt2->close();
//                 }
//             }
//             $stmt->close();
//         }
//     }
//     $con->close();
// }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-style-mode" content="1"> <!-- 0 == light, 1 == dark -->

    <title>Easy AI Voice - Tailored AI Voices, Designed for Simplicity</title>

    <meta name="description" content="Easy AI Voice offers cutting-edge Tailored AI Voices, Designed for Simplicity, One Click Away" />
    <meta name="keywords" content="Easy Ai, Easy AI Voice, AI, Artificial Intelligence, Productivity, Automation, Efficiency, Business Solutions, Easy-to-use, Data Analysis, Innovation, Streamline, Operations, Success" />

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
    <!-- CSS ============================================ -->
    <link rel="stylesheet" href="assets2/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="assets2/css/plugins/animation.css">
    <link rel="stylesheet" href="assets2/css/plugins/feature.css">
    <link rel="stylesheet" href="assets2/css/plugins/magnify.min.css">
    <link rel="stylesheet" href="assets2/css/plugins/slick.css">
    <link rel="stylesheet" href="assets2/css/plugins/slick-theme.css">
    <link rel="stylesheet" href="assets2/css/plugins/lightbox.css">
    <link rel="stylesheet" href="assets2/css/style.css">
    <?php include '_head.php'; ?>
</head>

<body>
    <main class="page-wrapper">
        <!-- Start Header Area  -->
        <header class="rainbow-header header-default header-not-transparent header-sticky">
            <div class="container position-relative">
                <div class="row align-items-center row--0">
                    <div class="col-lg-3 col-md-6 col-4">
                        <div class="logo">
                            <a href="/">
                                <img class="logo-light" src="assets2/images/logo/logo-dark2.png" alt="Corporate Logo">
                                <img class="logo-dark" src="assets2/images/logo/logo-dark2.png" alt="Corporate Logo">
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-6 col-8 position-static">
                        <div class="header-right">

                            <nav class="mainmenu-nav d-none d-lg-block">
                                <ul class="mainmenu">
                                    <!-- <li class="with-megamenu has-menu-child-item position-relative"><a href="#">Home</a>
                                        <div class="rainbow-megamenu with-mega-item-2">
                                            <div class="wrapper">
                                                <div class="row row--0">
                                                    <div class="col-lg-6 single-mega-item">
                                                        <ul class="mega-menu-item">
                                                            <li><a href="index-landing.html">Sass Landing <span class="rainbow-badge-card">New</span></a></li>
                                                            <li><a href="index-application.html">Application <span class="rainbow-badge-card">New</span></a></li>
                                                            <li><a href="index-collaborate.html">Collaborate <span class="rainbow-badge-card">New</span></a></li>
                                                            <li><a href="index-business-consulting.html">Business Consulting</a></li>
                                                            <li><a href="index-business-consulting-2.html">Business Consulting 02</a></li>
                                                            <li><a href="index-magazine.html">Magazine <span class="rainbow-badge-card">New</span></a></li>
                                                            <li><a href="index-corporate.html">Corporate</a></li>
                                                            <li><a href="index-business.html">Business</a></li>
                                                            <li><a href="index-digital-agency.html">Digital Agency</a></li>
                                                            <li><a href="index-finance.html">Finance</a></li>
                                                            <li><a href="index-company.html">Company</a></li>
                                                            <li><a href="index-marketing-agency.html">Marketing Agency</a></li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-lg-6 single-mega-item">
                                                        <ul class="mega-menu-item">
                                                            <li><a href="index-travel-agency.html">Travel Agency</a></li>
                                                            <li><a href="index-consulting.html">Consulting</a></li>
                                                            <li><a href="index-seo-agency.html">SEO Agency</a></li>
                                                            <li><a href="index-personal-portfolio.html">Personal Portfolio</a></li>
                                                            <li><a href="index-event-conference.html">Event Conference</a></li>
                                                            <li><a href="index-creative-portfolio.html">Creative Portfolio</a></li>
                                                            <li><a href="index-freelancer.html">Freelancer</a></li>
                                                            <li><a href="index-international-consulting.html">International Consulting</a></li>
                                                            <li><a href="index-startup.html">Startup</a></li>
                                                            <li><a href="index-web-agency.html">Web Agency</a></li>
                                                            <li><a href="index-corporate-one-page.html">Corporate One Page <span class="rainbow-badge-card">New</span></a></li>
                                                            <li><a href="index-photographer.html">Photographer <span class="rainbow-badge-card">New</span></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li> -->
                                    <!-- <li><a href="about.html">About</a></li>
                                    <li class="with-megamenu has-menu-child-item"><a href="#">Elements</a>
                                        <div class="rainbow-megamenu">
                                            <div class="wrapper">
                                                <div class="row row--0">
                                                    <div class="col-lg-3 single-mega-item">
                                                        <ul class="mega-menu-item">
                                                            <li><a href="button.html">Button</a></li>
                                                            <li><a href="service.html">Service</a></li>
                                                            <li><a href="counter.html">Counter</a></li>
                                                            <li><a href="progressbar.html">Progressbar</a></li>
                                                            <li><a href="accordion.html">Accordion</a></li>
                                                            <li><a href="social-share.html">Social Share</a></li>
                                                            <li><a href="blog-grid.html">Blog Grid</a></li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-lg-3 single-mega-item">
                                                        <ul class="mega-menu-item">
                                                            <li><a href="team.html">Team</a></li>
                                                            <li><a href="portfolio.html">Portfolio</a></li>
                                                            <li><a href="testimonial.html">Testimonial</a></li>
                                                            <li><a href="timeline.html">Timeline</a></li>
                                                            <li><a href="tab.html">Tab</a></li>
                                                            <li><a href="pricing.html">Pricing</a></li>
                                                            <li><a href="split.html">Split Section</a></li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-lg-3 single-mega-item">
                                                        <ul class="mega-menu-item">
                                                            <li><a href="call-to-action.html">Call To Action</a></li>
                                                            <li><a href="video.html">Video</a></li>
                                                            <li><a href="gallery.html">Gallery</a></li>
                                                            <li><a href="contact.html">Contact</a></li>
                                                            <li><a href="brand.html">Brand</a></li>
                                                            <li><a href="portfolio.html">Portfolio</a></li>
                                                            <li><a href="error.html">404</a></li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-lg-3 single-mega-item">
                                                        <ul class="mega-menu-item">
                                                            <li><a href="advance-tab.html">Advance Tab <span
                                        class="rainbow-badge-card">Hot</span></a></li>
                                                            <li><a href="brand-carouse.html">Brand Carousel <span
                                        class="rainbow-badge-card">New</span></a></li>
                                                            <li><a href="advance-pricing.html">Advance Pricing <span
                                        class="rainbow-badge-card">Hot</span></a></li>
                                                            <li><a href="portfolio-details.html">Portfolio Details</a></li>
                                                            <li><a href="blog-details.html">Blog Details</a></li>
                                                            <li><a href="privacy-policy.html">Privacy Policy <span
                                class="rainbow-badge-card">New</span></a></li>
                                                            <li><a href="login.html">Profile <span
                                class="rainbow-badge-card">New</span></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li> -->

                                    <!-- <li class="has-droupdown has-menu-child-item"><a href="#">Blog</a>
                                        <ul class="submenu">
                                            <li><a href="blog-grid.html">Blog Grid</a></li>
                                            <li><a href="blog-grid-sidebar.html">Blog Grid Sidebar</a></li>
                                            <li><a href="blog-list-view.html">Blog List View</a></li>
                                            <li><a href="blog-list-sidebar.html">Blog List View Sidebar</a></li>
                                            <li><a href="blog-details.html">Blog Details</a></li>
                                        </ul>
                                    </li> -->

                                    <!-- <li><a href="https://blog.easyaivoice.com/">Blog</a></li> -->
                                    <li><a href="#pricing">Pricing</a></li>
                                    <!-- <li><a href="#testimonial">Testimonial</a></li> -->
                                    <li><a href="https://blog.easyai.studio/contact/">Contact</a></li>

                                    <!-- <li class="has-droupdown has-menu-child-item"><a href="#">Portfolio</a>
                                        <ul class="submenu">
                                            <li><a href="portfolio.html">Portfolio Default</a></li>
                                            <li><a href="portfolio-three-column.html">Portfolio Three Column</a></li>
                                            <li><a href="portfolio-full-width.html">Portfolio Full Width</a></li>
                                            <li><a href="portfolio-grid-layout.html">Portfolio Grid Layout</a></li>
                                            <li><a href="portfolio-box-layout.html">Portfolio Box Layout</a></li>
                                            <li><a href="portfolio-details.html">Portfolio Details</a></li>
                                        </ul>
                                    </li> -->

                                </ul>







                            </nav>

                            <!-- Start Header Btn  -->
                            <div class="header-btn">
                                <a class="btn-default btn-small round" target="_blank" href="https://easyaivoice.com/sign-in">Sign In</a>
                            </div>
                            <!-- End Header Btn  -->

                            <!-- Start Mobile-Menu-Bar -->
                            <div class="mobile-menu-bar ml--5 d-block d-lg-none">
                                <div class="hamberger">
                                    <button class="hamberger-button">
                                        <i class="feather-menu"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- Start Mobile-Menu-Bar -->

                            <div id="my_switcher" class="my_switcher">
                                <ul>
                                    <li>
                                        <a href="javascript: void(0);" data-theme="light" class="setColor light">
                                            <img class="sun-image" src="assets2/images/icons/sun-01.svg" alt="Sun images">
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript: void(0);" data-theme="dark" class="setColor dark">
                                            <img class="Victor Image" src="assets2/images/icons/vector.svg" alt="Vector Images">
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- End Header Area  -->
        <div class="popup-mobile-menu">
            <div class="inner">
                <div class="header-top">
                    <div class="logo">
                        <a href="/">
                            <img class="logo-light" src="assets2/images/logo/logo-dark2.png" alt="Corporate Logo">
                            <img class="logo-dark" src="assets2/images/logo/logo-dark2.png" alt="Corporate Logo">
                        </a>
                    </div>
                    <div class="close-menu">
                        <button class="close-button">
                            <i class="feather-x"></i>
                        </button>
                    </div>
                </div>
                <ul class="mainmenu">
                    <!-- <li class="with-megamenu has-menu-child-item position-relative"><a href="#">Home</a>
                        <div class="rainbow-megamenu with-mega-item-2">
                            <div class="wrapper">
                                <div class="row row--0">
                                    <div class="col-lg-6 single-mega-item">
                                        <ul class="mega-menu-item">
                                            <li><a href="index-landing.html">Sass Landing <span class="rainbow-badge-card">New</span></a></li>
                                            <li><a href="index-application.html">Application <span class="rainbow-badge-card">New</span></a></li>
                                            <li><a href="index-collaborate.html">Collaborate <span class="rainbow-badge-card">New</span></a></li>
                                            <li><a href="index-business-consulting.html">Business Consulting</a></li>
                                            <li><a href="index-business-consulting-2.html">Business Consulting 02</a></li>
                                            <li><a href="index-magazine.html">Magazine <span class="rainbow-badge-card">New</span></a></li>
                                            <li><a href="index-corporate.html">Corporate</a></li>
                                            <li><a href="index-business.html">Business</a></li>
                                            <li><a href="index-digital-agency.html">Digital Agency</a></li>
                                            <li><a href="index-finance.html">Finance</a></li>
                                            <li><a href="index-company.html">Company</a></li>
                                            <li><a href="index-marketing-agency.html">Marketing Agency</a></li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-6 single-mega-item">
                                        <ul class="mega-menu-item">
                                            <li><a href="index-travel-agency.html">Travel Agency</a></li>
                                            <li><a href="index-consulting.html">Consulting</a></li>
                                            <li><a href="index-seo-agency.html">SEO Agency</a></li>
                                            <li><a href="index-personal-portfolio.html">Personal Portfolio</a></li>
                                            <li><a href="index-event-conference.html">Event Conference</a></li>
                                            <li><a href="index-creative-portfolio.html">Creative Portfolio</a></li>
                                            <li><a href="index-freelancer.html">Freelancer</a></li>
                                            <li><a href="index-international-consulting.html">International Consulting</a></li>
                                            <li><a href="index-startup.html">Startup</a></li>
                                            <li><a href="index-web-agency.html">Web Agency</a></li>
                                            <li><a href="index-corporate-one-page.html">Corporate One Page <span class="rainbow-badge-card">New</span></a></li>
                                            <li><a href="index-photographer.html">Photographer <span class="rainbow-badge-card">New</span></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li> -->
                    <!-- <li><a href="about.html">About</a></li> -->
                    <!-- <li class="with-megamenu has-menu-child-item"><a href="#">Elements</a>
                        <div class="rainbow-megamenu">
                            <div class="wrapper">
                                <div class="row row--0">
                                    <div class="col-lg-3 single-mega-item">
                                        <ul class="mega-menu-item">
                                            <li><a href="button.html">Button</a></li>
                                            <li><a href="service.html">Service</a></li>
                                            <li><a href="counter.html">Counter</a></li>
                                            <li><a href="progressbar.html">Progressbar</a></li>
                                            <li><a href="accordion.html">Accordion</a></li>
                                            <li><a href="social-share.html">Social Share</a></li>
                                            <li><a href="blog-grid.html">Blog Grid</a></li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-3 single-mega-item">
                                        <ul class="mega-menu-item">
                                            <li><a href="team.html">Team</a></li>
                                            <li><a href="portfolio.html">Portfolio</a></li>
                                            <li><a href="testimonial.html">Testimonial</a></li>
                                            <li><a href="timeline.html">Timeline</a></li>
                                            <li><a href="tab.html">Tab</a></li>
                                            <li><a href="pricing.html">Pricing</a></li>
                                            <li><a href="split.html">Split Section</a></li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-3 single-mega-item">
                                        <ul class="mega-menu-item">
                                            <li><a href="call-to-action.html">Call To Action</a></li>
                                            <li><a href="video.html">Video</a></li>
                                            <li><a href="gallery.html">Gallery</a></li>
                                            <li><a href="contact.html">Contact</a></li>
                                            <li><a href="brand.html">Brand</a></li>
                                            <li><a href="portfolio.html">Portfolio</a></li>
                                            <li><a href="error.html">404</a></li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-3 single-mega-item">
                                        <ul class="mega-menu-item">
                                            <li><a href="advance-tab.html">Advance Tab <span
                                        class="rainbow-badge-card">Hot</span></a></li>
                                            <li><a href="brand-carouse.html">Brand Carousel <span
                                        class="rainbow-badge-card">New</span></a></li>
                                            <li><a href="advance-pricing.html">Advance Pricing <span
                                        class="rainbow-badge-card">Hot</span></a></li>
                                            <li><a href="portfolio-details.html">Portfolio Details</a></li>
                                            <li><a href="blog-details.html">Blog Details</a></li>
                                            <li><a href="privacy-policy.html">Privacy Policy <span
                                class="rainbow-badge-card">New</span></a></li>
                                            <li><a href="login.html">Profile <span
                                class="rainbow-badge-card">New</span></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li> -->

                    <!-- <li class="has-droupdown has-menu-child-item"><a href="#">Blog</a>
                        <ul class="submenu">
                            <li><a href="blog-grid.html">Blog Grid</a></li>
                            <li><a href="blog-grid-sidebar.html">Blog Grid Sidebar</a></li>
                            <li><a href="blog-list-view.html">Blog List View</a></li>
                            <li><a href="blog-list-sidebar.html">Blog List View Sidebar</a></li>
                            <li><a href="blog-details.html">Blog Details</a></li>
                        </ul>
                    </li> -->

                    <!-- <li class="has-droupdown has-menu-child-item"><a href="#">Portfolio</a>
                        <ul class="submenu">
                            <li><a href="portfolio.html">Portfolio Default</a></li>
                            <li><a href="portfolio-three-column.html">Portfolio Three Column</a></li>
                            <li><a href="portfolio-full-width.html">Portfolio Full Width</a></li>
                            <li><a href="portfolio-grid-layout.html">Portfolio Grid Layout</a></li>
                            <li><a href="portfolio-box-layout.html">Portfolio Box Layout</a></li>
                            <li><a href="portfolio-details.html">Portfolio Details</a></li>
                        </ul>
                    </li> -->
                    <li><a href="https://blog.easyaivoice.com/">Blog</a></li>
                    <li><a href="#pricing">Pricing</a></li>
                    <!-- <li><a href="#testimonial">Testimonial</a></li> -->
                    <li><a href="https://blog.easyai.studio/contact/">Contact</a></li>
                </ul>







            </div>
        </div>
        <!-- Start Theme Style  -->
        <div>
            <div class="rainbow-gradient-circle"></div>
            <div class="rainbow-gradient-circle theme-pink"></div>
        </div>
        <!-- End Theme Style  -->



        <!-- Start Slider Area  -->
        <div class="slider-area slider-style-8 height-650" style="height: 450px;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="inner text-center">
                            <!-- <span class="subtitle theme-gradient">Hello! This is WebAgency</span> -->
                            <!-- <h1 class="title">Unlock <span class="theme-gradient">10x Productivity</span><br> with Easy AI Solutions.</h1> -->
                            <h1 class="title">Create <span class="theme-gradient">AI Voice</span> Magic<br>for
                                <span class="header-caption">
                                    <span class="cd-headline clip is-full-width">
                                        <span class="cd-words-wrapper">
                                            <b class="is-visible theme-gradient">Character Voices</b>
                                            <b class="is-hidden theme-gradient">Customer Service</b>
                                            <b class="is-hidden theme-gradient">Voiceovers</b>
                                            <b class="is-hidden theme-gradient">Voice Acting</b>
                                        </span>
                                    </span>
                                    <!-- <span class="theme-gradient">
                                Your
                            </span> 
                            Fingertips -->

                            </h1>
                            <p class="description">Tailored AI Voices, Designed for Simplicity, One Click Away</p>

                            <!-- <form class="contact-form-1 rainbow-dynamic-form" id="contact-form" method="POST" action="mail.php">
                                <div class="form-group">
                                    <input type="text" name="contact-name" id="contact-name" placeholder="Your Name">
                                </div>
                                <div class="form-group">
                                    <button name="submit" type="submit" id="submit" class="btn-default btn-large rainbow-btn">
                                        <span>Submit Now</span>
                                    </button>
                                </div>
                            </form> -->

                            <!-- <form class="rainbow-newsletter mt_md--20 mt_sm--20" action="#">
                                <div class="form-group"><input type="email" placeholder="Email Address">
                                </div>
                                <div class="form-group"><button class="btn-default">Subscribe</button></div>
                            </form> -->
                            <style>
                                #loadingThrobber {
                                    display: none;
                                }
                            </style>
                            <div class="row">
                                <div class="col-lg-2">
                                </div>
                                <div class="col-lg-8">
                                    <div id="loadingThrobber"><span class="spinner-border text-primary" role="status"></span></div>
                                    <form id="videoForm" class="rainbow-newsletter mt_md--20 mt_sm--20" action="/sign-up">
                                        <div class="form-group sal-animate" style="display: flex; align-items: center; justify-content: center;">
                                            <button type="submit" class="btn-default" style="width:250px;">Login to Get Started <i class="feather-arrow-right"></i></button>
                                        </div>
                                    </form>
                                    <!-- <a class="btn-default btn-small btn-border" href="#" id="sample">Load Demo YouTube Link</a> -->
                                </div>
                                <script>
                                    // Select the Sample YouTube button
                                    var sampleYouTubeButton = document.querySelector('#sample');

                                    // Add event listener for click
                                    sampleYouTubeButton.addEventListener('click', function(e) {
                                        // Prevent default click action
                                        e.preventDefault();

                                        // Set the video input field value to the YouTube link
                                        document.querySelector('#video').value = 'https://www.youtube.com/watch?v=QRy4JJNTAiA';

                                        // Dispatch a submit event to the form to trigger the existing event listener
                                        document.querySelector('#videoForm').dispatchEvent(new Event('submit'));
                                    });
                                </script>
                                <div class="col-lg-2">
                                </div>
                            </div>



                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Slider Area  -->

        <!-- Start Main Counter up-5 Area  -->
        <div class="rainbow-counterup-area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="thumbnail" onclick="location.href='/sign-up'"><img class="radius w-100" src="assets2/images/screenshot2.jpg" alt="Images"></div>
                    </div>
                </div>


                <!-- <div class="row ptb--60">
                                    <div class="col-lg-3 col-md-6 col-sm-6 col-12" data-sal="slide-up" data-sal-duration="700">
                                        <div class="count-box counter-style-4 text-center">
                                            <div>
                                                <div class="count-number"><span class="counter">199</span></div>
                                            </div>
                                            <h5 class="counter-title">Happy Clients.</h5>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-6 col-sm-6 col-12" data-sal="slide-up" data-sal-duration="700" data-sal-delay="100">
                                        <div class="count-box counter-style-4 text-center">
                                            <div>
                                                <div class="count-number"><span class="counter">575</span></div>
                                            </div>
                                            <h5 class="counter-title">Employees</h5>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-6 col-sm-6 col-12" data-sal="slide-up" data-sal-duration="700" data-sal-delay="200">
                                        <div class="count-box counter-style-4 text-center">
                                            <div>
                                                <div class="count-number"><span class="counter">69</span></div>
                                            </div>
                                            <h5 class="counter-title">Useful Programs</h5>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-6 col-sm-6 col-12" data-sal="slide-up" data-sal-duration="700" data-sal-delay="300">
                                        <div class="count-box counter-style-4 text-center">
                                            <div>
                                                <div class="count-number"><span class="counter">500</span></div>
                                            </div>
                                            <h5 class="counter-title">Useful Programs</h5>
                                        </div>
                                    </div>
                                </div> -->


            </div>
        </div>
        <!-- End Main Counter up-5 Area  -->


        <!-- Start Seperator Area  -->
        <!-- <div class="rbt-separator-mid">
            <div class="container">
                <hr class="rbt-separator m-0">
            </div>
        </div> -->
        <!-- End Seperator Area  -->

        <!-- Start Service-2 Area  -->
        <!-- <div class="service-area rainbow-section-gap">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="content">
                            <h3 class="title">We are creative digital agency working for our company brands.</h3>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <p class="mb--10">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quod, quis. Ullam
                            accusantium dignissimos repellendus nemo fugiat numquam, nisi odio adipisci. Veniam neque
                            itaque expedita officiis rem ipsa! Ratione, rem reiciendis?</p>
                        <div class="readmore-btn"><a class="btn-read-more" href="#"><span>View
                                    More</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- End Service-2 Area  -->


        <!-- Start Service-5 Area  -->
        <div class="rainbow-service-area rainbow-section-gapBottom">
            <!-- <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title text-center" data-sal="slide-up" data-sal-duration="700" data-sal-delay="100">
                            <h4 class="subtitle ">
                                <span class="theme-gradient">What we can do for you</span>
                            </h4>
                            <h2 class="title w-600 mb--20">Services provide for you.</h2>
                        </div>
                    </div>
                </div>
                <div class="row row--15 service-wrapper">
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12" data-sal="slide-up" data-sal-duration="700">
                        <div class="service service__style--1 icon-circle-style text-center">
                            <div class="icon">
                                <i class="feather-activity"></i>
                            </div>
                            <div class="content">
                                <h4 class="title w-600"><a href="#">Awarded Design</a></h4>
                                <p class="description b1 color-gray mb--0">There are many variations variations
                                    of passages of Lorem Ipsum available, but the majority have suffered.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-6 col-12" data-sal="slide-up" data-sal-duration="700" data-sal-delay="100">
                        <div class="service service__style--1 icon-circle-style text-center">
                            <div class="icon">
                                <i class="feather-map"></i>
                            </div>
                            <div class="content">
                                <h4 class="title w-600"><a href="#">Awarded Design</a></h4>
                                <p class="description b1 color-gray mb--0">Passages there are many variations variations
                                    of of Lorem Ipsum available, but the majority have suffered.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-6 col-12" data-sal="slide-up" data-sal-duration="700" data-sal-delay="200">
                        <div class="service service__style--1 icon-circle-style text-center">
                            <div class="icon">
                                <i class="feather-cast"></i>
                            </div>
                            <div class="content">
                                <h4 class="title w-600"><a href="#">Awarded Design</a></h4>
                                <p class="description b1 color-gray mb--0">Variations There are many variations of
                                    passages of Lorem Ipsum available, but the majority have suffered.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
        <!-- Start Service-5 Area  -->


        <!-- Srart About Area  -->
        <div class="about-area about-style-4 rainbow-section-gapBottom ">
            <div class="container">
                <div class="row row--40 align-items-center">
                    <div class="col-lg-6">
                        <div class="video-btn">
                            <div class="video-popup icon-center">
                                <div class="overlay-content">
                                    <div class="thumbnail"><img class="radius-small" src="assets2/images/voiceLoGo.webp" alt="Corporate Image"></div>
                                    <div class="video-icon">
                                        <a class="btn-default rounded-player popup-video" href="https://www.youtube.com/embed/tJzjFSKlYs4">
                                            <span><i class="feather-play"></i></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="video-lightbox-wrapper"></div>
                        </div>
                    </div>
                    <div class="col-lg-6 mt_md--40 mt_sm--40">
                        <div class="content">
                            <div class="inner">
                                <h3 class="title">Your Voice, <span class="theme-gradient"><strong> Your Way.</strong></span>
                                </h3>
                                <ul class="feature-list">
                                    <li>
                                        <div class="icon">
                                            <i class="feather-check"></i>
                                        </div>
                                        <div class="title-wrapper">
                                            <h4 class="title">Your Voice, Amplified</h4>
                                            <p class="text">🎙️Seamless Creation and Use of Custom AI Voice Models.</p>
                                            <p class="text">👥 Our platform prioritizes your needs, allowing you to create voice models that align with your personal or business requirements.</p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="icon">
                                            <i class="feather-check"></i>
                                        </div>
                                        <div class="title-wrapper">
                                            <h4 class="title">Voice Power, Simplified</h4>
                                            <p class="text">⚡User-Generated Voice Models in a Snap.</p>
                                            <p class="text">💡We've eliminated the complexities usually associated with voice model training, ensuring you can create models quickly and without hassle.</p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="icon">
                                            <i class="feather-check"></i>
                                        </div>
                                        <div class="title-wrapper">
                                            <h4 class="title">AI Voice Modeling, Demystified.</h4>
                                            <p class="text">🎓Easily Train and Use Your Custom Voice Models.</p>
                                            <p class="text">👶Even if you're a beginner, our intuitive platform makes it easy for anyone to train and use voice models.</p>
                                        </div>
                                    </li>
                                </ul>
                                <div class="about-btn mt--20"><a class="btn-default" href="https://easyaivoice.com/sign-up" style="
                                    margin-left: 30px;" id="pricing">Explore Voice Now <i class="feather-arrow-right"></i></a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End About Area  -->

        <!-- Start Pricing Style-2  -->
        <div class="rainbow-pricing-area " style="
        padding-top: 40px;
        padding-bottom: 57px;
    ">
            <div class="container">
                <div class="row mb--20 mb_sm--0">
                    <div class="col-lg-12">
                        <div class="section-title text-center" data-sal="slide-up" data-sal-duration="400" data-sal-delay="150">

                            <h3 class="title" style="margin-bottom: 20px;">
                                Choose your <br>
                            </h3>

                            <a class="btn-default" style="
                                    " id="monthly-option">Monthly</a><a class="btn-default btn-border" style="
                                    " id="yearly-option">Yearly <span style="
                            background: linear-gradient(95deg, var(--color-primary) 15%, var(--color-tertiary) 45%, var(--color-pink) 75%, var(--color-secondary) 100%) 98%/200% 100%; border-radius: 100px; padding: 1px 10px;
                        ">20% off</span></a>
                        </div>

                    </div>

                </div>

                <!-- <div class="col-lg-12">
                        <div class="section-title text-center sal-animate" data-sal="slide-up" data-sal-duration="700" data-sal-delay="100">
                            <h4 class="subtitle " style><span class="theme-gradient">Companies About.</span></h4>
                        
                        </div>
                    </div> -->






                <div class="row ">

                    <div class="col-lg-9 offset-lg-1">
                        <div class="row row--0">

                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="rainbow-pricing style-2">
                                    <div class="pricing-table-inner" id="starter">
                                        <div class="pricing-header">
                                            <h4 class="title">Starter</h4>
                                            <div class="pricing">
                                                <div class="price-wrapper"><span class="currency">$</span><span class="price" id="starter-price">10</span></div><span class="subtitle" id="starter-text">US Per
                                                    Month</span>
                                            </div>
                                        </div>
                                        <div class="pricing-body" style="padding-left: 30px;">
                                            <ul class="list-style--1">
                                                <li><i class="feather-check"></i> Private projects</li>
                                                <li><i class="feather-check"></i> 15GB storage</li>
                                                <li><i class="feather-check"></i> Mid-range instances</li>
                                                <li><i class="feather-check"></i> Faster free GPUs 🚀</li>
                                            </ul>
                                        </div>
                                        <div class="pricing-footer"><a class="btn-default btn-border" id="starter-link" href="https://buy.stripe.com/4gw5mb7My6DN2ju6op">Subscribe</a>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-12">
                                <span>
                                    <div class="rainbow-pricing style-2 active" style="
                                background: linear-gradient(95deg, var(--color-primary) 15%, var(--color-tertiary) 45%, var(--color-pink) 75%, var(--color-secondary) 100%) 98%/200% 100%;
                                margin-top: 17px;
                                padding-top: 1px;
                                padding-right: 1px;
                                padding-bottom: 1px;
                                padding-left: 1px;
                            ">
                                        <div class="pricing-table-inner" id="advanced" style="padding-top: 10px;">
                                            <div style="text-align:center;"><span class="rainbow-badge-card" style=" background: linear-gradient(95deg, var(--color-primary) 15%, var(--color-tertiary) 45%, var(--color-pink) 75%, var(--color-secondary) 100%) 95%/200% 100%;">Most Popular</span></div>
                                            <div class="pricing-header">

                                                <h4 class="title" id="advanced-label">Advanced</h4>
                                                <div class="pricing">
                                                    <div class="price-wrapper"><span class="currency">$</span><span class="price" id="advanced-price">39</span></div><span class="subtitle" id="advanced-text">US Per
                                                        Month</span>
                                                </div>
                                            </div>
                                            <div class="pricing-body" style="padding-left: 30px;">
                                                <ul class="list-style--1">
                                                    <li><i class="feather-check"></i> Private projects</li>
                                                    <li><i class="feather-check"></i> 50GB storage</li>
                                                    <li><i class="feather-check"></i> High-end instances</li>
                                                    <li><i class="feather-check"></i> Priority Support</li>
                                                    <li><i class="feather-check"></i> Expert Support</li>
                                                </ul>
                                            </div>
                                            <div class="pricing-footer"><a class="btn-default" id="advanced-link" href="https://buy.stripe.com/9AQ29Z2seaU33nycMO">Subscribe</a>
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            <!-- <div class="col-lg-4 col-md-6 col-12">
                            <div class="rainbow-pricing style-2">
                                <div class="pricing-table-inner" id="enterprise">
                                    <div class="pricing-header">
                                        <h4 class="title" id="enterprise-label">Enterprise</h4>
                                        <div class="pricing">
                                            <div class="price-wrapper">
                                                <span class="currency">$</span>
                                                <span class="price" id="enterprise-price">599</span>
                                            </div>
                                            <span class="subtitle" id="enterprise-text">USD Per Month</span>
                                        </div>
                                    </div>
                                    <div class="pricing-body" style="padding-left: 30px;">
                                        <ul class="list-style--1">
                                            <li><i class="feather-check"></i> API Access</li>
                                            <li><i class="feather-check"></i> 20,000 credits / month</li>
                                            <li><i class="feather-check"></i> Access All Tools</li>
                                        </ul>
                                    </div>
                                    <div class="pricing-footer"><a class="btn-default btn-border" id="enterprise-link" href="https://buy.stripe.com/9AQ3e3aYKgen7DOfZ4">Subscribe</a>
                                    </div>

                                </div>
                            </div>
                        </div> -->

                        </div>
                    </div>
                </div>

            </div>



            <div id="testimonial"></div>
        </div>

        <!-- Start Testimonial Style One  -->
        <!-- <div class="rainbow-testimonial-area rainbow-testimonial ">
            <div class="wrapper plr--150 plr_lg--30 plr_md--30 plr_sm--30">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title text-center" data-sal="slide-up" data-sal-duration="700" data-sal-delay="100">
                            <h4 class="subtitle ">
                                <span class="theme-gradient">OUR FANTASTIC ENVATO CUSTOMERS REVIEWS</span>
                            </h4>
                            <h2 class="title w-600 mb--5">Customer feedback</h2>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-lg-3 col-md-6 col-12 mt--10" data-sal="slide-up" data-sal-duration="700">
                        <div class="rainbow-box-card card-style-default testimonial-style-one style-two border-gradient">
                            <div class="inner">
                                <div class="content">
                                    <div class="rating">
                                        <img src="assets2/images/icons/rating.png" alt="">
                                    </div>
                                    <p class="description">I was able to quickly and easily build advanced AI models thanks to Easy AI Voice's intuitive design.</p>
                                    <h2 class="title">mindcycle001</h2>
                                    <h6 class="subtitle theme-gradient">Customer Support</h6>
                                     <div class="author-envato-image pb--10">
                                        <img class="envato-white" src="assets2/images/icons/envato-white.svg" alt="Envato">
                                        <img class="envato-black" src="assets2/images/icons/envato.svg" alt="Envato">
                                    </div> -->
        <!-- </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12 mt--10" data-sal="slide-up" data-sal-duration="700">
                        <div class="rainbow-box-card card-style-default testimonial-style-one style-two border-gradient">
                            <div class="inner">
                                <div class="content">
                                    <div class="rating">
                                        <img src="assets2/images/icons/rating.png" alt="">
                                    </div>
                                    <p class="description">The user-friendly interface of Easy AI Voice made it easy for me to understand and utilize AI technology.</p>
                                    <h2 class="title">The4</h2>
                                    <h6 class="subtitle theme-gradient">Design Quality</h6>
                                    <div class="author-envato-image pb--10">
                                        <img class="envato-white" src="assets2/images/icons/envato-white.svg" alt="Envato">
                                        <img class="envato-black" src="assets2/images/icons/envato.svg" alt="Envato">
                                    </div> -->
        <!-- </div>
                            </div> -->
        <!-- </div> -->
        <!-- </div>

                    <div class="col-lg-3 col-md-6 col-12 mt--10" data-sal="slide-up" data-sal-duration="700">
                        <div class="rainbow-box-card card-style-default testimonial-style-one style-two border-gradient">
                            <div class="inner">
                                <div class="content">
                                    <div class="rating">
                                        <img src="assets2/images/icons/rating.png" alt="">
                                    </div>
                                    <p class="description">Easy AI Voice helped me to create and implement AI solutions for my business, saving me time and money.</p>
                                    <h2 class="title">wimm-x</h2>
                                    <h6 class="subtitle theme-gradient">Design Quality</h6>
                                    <div class="author-envato-image pb--10">
                                        <img class="envato-white" src="assets2/images/icons/envato-white.svg" alt="Envato">
                                        <img class="envato-black" src="assets2/images/icons/envato.svg" alt="Envato">
                                    </div> -->
        <!-- </div>
                            </div>
                        </div> -->
        <!-- </div> -->

        <!-- <div class="col-lg-3 col-md-6 col-12 mt--10" data-sal="slide-up" data-sal-duration="700">
                        <div class="rainbow-box-card card-style-default testimonial-style-one style-two border-gradient">
                            <div class="inner">
                                <div class="content">
                                    <div class="rating">
                                        <img src="assets2/images/icons/rating.png" alt="">
                                    </div>
                                    <p class="description">Easy AI Voice made it simple for me to create and deploy my own AI models. Highly recommended!</p>
                                    <h2 class="title">amarbv2002</h2>
                                    <h6 class="subtitle theme-gradient">Design Quality</h6>
                                     <div class="author-envato-image pb--10">
                                        <img class="envato-white" src="assets2/images/icons/envato-white.svg" alt="Envato">
                                        <img class="envato-black" src="assets2/images/icons/envato.svg" alt="Envato">
                                    </div> -->
        <!-- </div> -->
        <!-- </div> -->
        <!-- </div> -->
        <!-- </div>   -->

        <!-- </div> -->
        <!-- </div> -->
        <!-- </div>  -->
        <!-- End Testimonial Style One  -->

        <!-- Start Brand Style-1  -->
        <!-- <div class="rainbow-brand-area rainbow-section-gapBottom">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title text-center" data-sal="slide-up" data-sal-duration="700" data-sal-delay="100">
                            <h4 class="subtitle "><span class="theme-gradient">Our Awesome Client.</span></h4>
                            <h2 class="title w-600 mb--20">Our Awesome Client.</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 mt--40">
                        <ul class="brand-list brand-style-1">
                            <li><a href="#"><img src="assets2/images/brand/brand-01.png" alt="Brand Image"></a></li>
                            <li><a href="#"><img src="assets2/images/brand/brand-02.png" alt="Brand Image"></a></li>
                            <li><a href="#"><img src="assets2/images/brand/brand-03.png" alt="Brand Image"></a></li>
                            <li><a href="#"><img src="assets2/images/brand/brand-04.png" alt="Brand Image"></a></li>
                            <li><a href="#"><img src="assets2/images/brand/brand-05.png" alt="Brand Image"></a></li>
                            <li><a href="#"><img src="assets2/images/brand/brand-06.png" alt="Brand Image"></a></li>
                            <li><a href="#"><img src="assets2/images/brand/brand-07.png" alt="Brand Image"></a></li>
                            <li><a href="#"><img src="assets2/images/brand/brand-08.png" alt="Brand Image"></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- End Brand Style-1  -->


        <!-- Start Footer Area  -->
        <footer class="rainbow-footer footer-style-default variation-two">
            <div class="rainbow-callto-action clltoaction-style-default style-7">
                <div class="container">
                    <div class="row row--0 align-items-center content-wrapper">
                        <div class="col-lg-8 col-md-8">
                            <div class="inner">
                                <div class="content text-left">
                                    <div class="logo">
                                        <a href="/">
                                            <img class="logo-light" src="assets2/images/logo/logo-dark2.png" alt="Corporate Logo">
                                            <img class="logo-dark" src="assets2/images/logo/logo-dark2.png" alt="Corporate Logo">
                                        </a>
                                    </div>
                                    <h4 class="main-title">Start With Easy AI Voice Today, Speed Up Development!</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4" data-sal="slide-up" data-sal-duration="400" data-sal-delay="150">
                            <div class="call-to-btn text-left mt_sm--20 text-lg-right">
                                <a class="btn-default" href="https://easyaivoice.com/sign-up">Get Started Now
                                    <i class="feather-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-top" style="
            padding-top: 50px;
            padding-bottom: 50px;
        ">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-2 col-md-6 col-sm-6 col-12">
                            <div class="rainbow-footer-widget">
                                <h4 class="title">Services</h4>
                                <div class="inner">
                                    <ul class="footer-link link-hover">
                                        <!-- <li><a href="about.html">About</a></li>
                                        <li><a href="portfolio.html">Portfolio</a></li> -->
                                        <li><a href="https://blog.easyai.studio/contact/">Contact</a></li>
                                        <!-- <li><a href="service.html">Service</a></li> -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-lg-2 col-md-6 col-sm-6 col-12">
                            <div class="rainbow-footer-widget">
                                <div class="widget-menu-top">
                                    <h4 class="title">Solutions</h4>
                                    <div class="inner">
                                        <ul class="footer-link link-hover">
                                            <li><a href="brand.html">Brand</a></li>
                                            <li><a href="call-to-action.html">call To Action</a></li>
                                            <li><a href="counter.html">Counter</a></li>
                                            <li><a href="service.html">Service</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="col-lg-2 col-md-6 col-sm-6 col-12">
                            <div class="rainbow-footer-widget">
                                <h4 class="title">Company</h4>
                                <div class="inner">
                                    <ul class="footer-link link-hover">
                                        <li><a href="#pricing">Pricing</a></li>
                                        <!-- <li><a href="tab.html">Tab Styles</a></li>
                                        <li><a href="service.html">Service</a></li>
                                        <li><a href="social-share.html">Social</a></li> -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-lg-2 col-md-6 col-sm-6 col-12"> -->
                        <!-- <div class="rainbow-footer-widget"> -->
                        <!-- <h4 class="title">Resources</h4> -->
                        <!-- <div class="inner"> -->
                        <!-- <ul class="footer-link link-hover"> -->
                        <!-- <li><a href="team.html">Team</a></li> -->
                        <!-- <li><a href="#testimonial">Testimonial</a></li> -->
                        <!-- <li><a href="service.html">Service</a></li>
                                        <li><a href="timeline.html">Timeline</a></li> -->
                        <!-- </ul> -->
                        <!-- </div> -->
                        <!-- </div> -->
                        <!-- </div> -->
                        <div class="col-lg-5 col-md-6 col-sm-6 col-12">
                            <div class="rainbow-footer-widget">
                                <h4 class="title">Stay Connected</h4>
                                <div class="inner">
                                    <!-- <h6 class="subtitle">2000+ Our clients are subscribe Around the World</h6> -->
                                    <ul class="social-icon social-default justify-content-start">
                                        <li><a href="https://www.facebook.com/EasyaiStudio">
                                                <i class="feather-facebook"></i>
                                            </a>
                                        </li>
                                        <li><a href="https://www.youtube.com/@easyaistudio">
                                                <i class="feather-youtube"></i>
                                            </a>
                                        </li>
                                        <li><a href="https://twitter.com/EasyaiStudio">
                                                <i class="feather-twitter"></i>
                                            </a>
                                        </li>
                                        <li><a href="https://www.instagram.com/easyai.studio/">
                                                <i class="feather-instagram"></i>
                                            </a>
                                        </li>
                                        <li><a href="https://www.linkedin.com/in/yupin-nu-4b053a22a/">
                                                <i class="feather-linkedin"></i>
                                            </a>
                                        </li>
                                        <li><a href="https://github.com/EasyAiStudio">
                                                <i class="feather-github"></i>
                                            </a>
                                        </li>
                                        <li><a href="https://www.reddit.com/user/EasyAiStudio">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-reddit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M12 8c2.648 0 5.028 .826 6.675 2.14a2.5 2.5 0 0 1 2.326 4.36c0 3.59 -4.03 6.5 -9 6.5c-4.875 0 -8.845 -2.8 -9 -6.294l-1 -.206a2.5 2.5 0 0 1 2.326 -4.36c1.646 -1.313 4.026 -2.14 6.674 -2.14zl1 -5l6 1m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0m-9 9m-.5 0a0.5 .5 0 1 0 1 0a0.5 .5 0 1 0 -1 0m6.5 0m-.5 0a0.5 .5 0 1 0 1 0a0.5 .5 0 1 0 -1 0m-4.5 4c.667 .333 1.333 .5 2 .5s1.333 -.167 2 -.5"></path>
                                                </svg>
                                            </a>
                                        </li>
                                        <li><a href="https://www.tiktok.com/@easyai.studio?lang=en">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-tiktok" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M9 12a4 4 0 1 0 4 4v-12a5 5 0 0 0 5 5"></path>
                                                </svg>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- End Footer Area  -->
        <!-- Start Copy Right Area  -->
        <div class="copyright-area copyright-style-one">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-8 col-sm-12 col-12">
                        <div class="copyright-left">
                            <ul class="ft-menu link-hover">
                                <li>
                                    <a href="/privacy">Privacy Policy</a>
                                </li>
                                <li>
                                    <a href="/terms">Terms And Condition</a>
                                </li>
                                <li>
                                    <a href="https://blog.easyai.studio/contact/">Contact Us</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-4 col-sm-12 col-12">
                        <div class="copyright-right text-center text-lg-end">
                            <p class="copyright-text">© Easy AI Voice 2023</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://accounts.google.com/gsi/client" async defer></script>
        <div id="g_id_onload" data-client_id="<?php echo $google_oauth_client_id; ?>" data-auto_select="true" data-context="signin" data-callback="googleLoginEndpoint" data-close_on_tap_outside="false">
        </div>
        <!-- End Copy Right Area  -->
    </main>

    <!-- All Scripts  -->
    <!-- Start Top To Bottom Area  -->
    <div class="rainbow-back-top">
        <i class="feather-arrow-up"></i>
    </div>
    <!-- End Top To Bottom Area  -->
    <!-- JS
============================================ -->
    <script src="assets2/js/vendor/modernizr.min.js"></script>
    <script src="assets2/js/vendor/jquery.min.js"></script>
    <script src="assets2/js/vendor/bootstrap.min.js"></script>
    <script src="assets2/js/vendor/popper.min.js"></script>
    <script src="assets2/js/vendor/waypoint.min.js"></script>
    <script src="assets2/js/vendor/wow.min.js"></script>
    <script src="assets2/js/vendor/counterup.min.js"></script>
    <script src="assets2/js/vendor/feather.min.js"></script>
    <script src="assets2/js/vendor/sal.min.js"></script>
    <script src="assets2/js/vendor/masonry.js"></script>
    <script src="assets2/js/vendor/imageloaded.js"></script>
    <script src="assets2/js/vendor/magnify.min.js"></script>
    <script src="assets2/js/vendor/lightbox.js"></script>
    <script src="assets2/js/vendor/slick.min.js"></script>
    <script src="assets2/js/vendor/easypie.js"></script>
    <script src="assets2/js/vendor/text-type.js"></script>
    <script src="assets2/js/vendor/jquery.style.swicher.js"></script>
    <script src="assets2/js/vendor/js.cookie.js"></script>
    <script src="assets2/js/vendor/jquery-one-page-nav.js"></script>

    <!-- Main JS -->
    <script src="assets2/js/main.js"></script>
    <script>
        // callback function that will be called when the user is successfully logged-in with Google
        function googleLoginEndpoint(googleUser) {
            // get user information from Google
            console.log(googleUser);

            // send an AJAX request to register the user in your website
            var ajax = new XMLHttpRequest();

            // path of server file
            ajax.open("POST", "google-sign-in.php", true);

            // callback when the status of AJAX is changed
            ajax.onreadystatechange = function() {

                // when the request is completed
                if (this.readyState == 4) {

                    // when the response is okay
                    if (this.status == 200) {
                        console.log(this.responseText);

                        window.location.replace('<?php
                                                    if (isset($_SESSION['return_url'])) {
                                                        echo $_SESSION['return_url'];
                                                        unset($_SESSION['return_url']);
                                                    } else {
                                                        echo ($_SERVER['HTTP_HOST'] === 'localhost') ? '/app.php' : '/app';
                                                    }
                                                    ?>');
                    }

                    // if there is any server error
                    if (this.status == 500) {
                        console.log(this.responseText);
                    }
                }
            };

            // send google credentials in the AJAX request
            var formData = new FormData();
            formData.append("id_token", googleUser.credential);
            ajax.send(formData);
        }
    </script>

    <script>
        $(document).ready(function() {
            var monthlyPrices = {
                'starter': 10,
                'advanced': 39,
                // 'enterprise': 599
            }; // Set your monthly prices
            var yearlyPrices = {
                'starter': 100,
                'advanced': 399,
                // 'enterprise': "5,999"
            };

            $('#monthly-option').on('click', function() {
                // Update pricing, button link and label when monthly option is clicked
                $('#starter-price').text(monthlyPrices['starter']);
                $('#starter-link').attr('href', 'https://buy.stripe.com/4gw5mb7My6DN2ju6op');
                $('#advanced-price').text(monthlyPrices['advanced']);
                $('#advanced-link').attr('href', 'https://buy.stripe.com/9AQ29Z2seaU33nycMO');
                // $('#enterprise-price').text(monthlyPrices['enterprise']);
                // $('#enterprise-link').attr('href', 'https://buy.stripe.com/9AQ3e3aYKgen7DOfZ4');
                $('#starter-text').text('USD Per Month');
                $('#advanced-text').text('USD Per Month');
                // $('#enterprise-text').text('USD Per Month');
                document.getElementById("monthly-option").classList.remove("btn-border");
                document.getElementById("yearly-option").classList.add("btn-border");
            });

            $('#yearly-option').on('click', function() {
                // Update pricing, button link and label when yearly option is clicked
                $('#starter-price').text(yearlyPrices['starter']);
                $('#starter-link').attr('href', 'https://buy.stripe.com/14k15VeaW2nx8HSfYY');
                $('#advanced-price').text(yearlyPrices['advanced']);
                $('#advanced-link').attr('href', 'https://buy.stripe.com/28o29Zd6S7HRf6gcMP');
                // $('#enterprise-price').text(yearlyPrices['enterprise']);
                // $('#enterprise-link').attr('href', 'https://buy.stripe.com/aEUeWLc2O5zJbU4cMT');
                $('#starter-text').text('USD Per Year | 2 MONTHS FREE');
                $('#advanced-text').text('USD Per Year | 2 MONTHS FREE');
                // $('#enterprise-text').text('USD Per Year | 2 MONTHS FREE');

                document.getElementById("monthly-option").classList.add("btn-border");
                document.getElementById("yearly-option").classList.remove("btn-border");
            });
        });
    </script>
    <style>
        .btn-group-toggle .btn.active {
            display: none;
        }

        .btn-group-toggle .btn:not(.active) {
            display: inline;
        }
    </style>
</body>

</html>