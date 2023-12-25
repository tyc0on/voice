<?php if (!isset($_SESSION['loggedin'])) { ?>
    <div class="modal fade " data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" id="kt_modal_login">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <!-- <div class="modal-header">
                <h5 class="modal-title">Unlock AI-Generated Content and Share it with Friends</h5>

                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <span class="svg-icon svg-icon-2x"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                transform="rotate(-45 6 17.3137)" fill="currentColor"></rect>
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                fill="currentColor"></rect>
                        </svg>

                    </span>
                </div>
            </div> -->

                <div class="modal-body">
                    <div class="text-start mb-10">
                        <!--begin::Title-->
                        <h1 class="text-dark mb-3 fs-3x" data-kt-translate="sign-in-title">
                            Sign In
                        </h1>
                        <!--end::Title-->
                        <!--begin::Text-->
                        <div class="text-gray-400 fw-semibold fs-6" data-kt-translate="general-desc">
                            Access the power of AI
                        </div>
                        <!--end::Link-->
                    </div>

                    <div class="fv-row mb-8">
                        <script src="https://accounts.google.com/gsi/client" async defer></script>
                        <div id="g_id_onload" data-client_id="<?php echo $google_oauth_client_id; ?>" data-auto_select="true" data-context="signin" data-callback="googleLoginEndpoint" data-close_on_tap_outside="false"></div>

                        <div class="g_id_signin" data-type="standard" data-size="large" data-theme="outline" data-text="sign_in_with" data-shape="rectangular" data-logo_alignment="left">
                        </div>
                    </div>

                    <div class="separator separator-content my-14">
                        <span class="w-125px text-gray-500 fw-semibold fs-7">Or with email</span>
                    </div>


                    <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" data-kt-redirect-url="/dash.php" action="authenticate.php" method="post">
                        <!--begin::Heading-->
                        <!--begin::Input group=-->
                        <div class="fv-row mb-8">
                            <!--begin::Email-->
                            <input type="text" placeholder="Email" name="email" autocomplete="off" data-kt-translate="sign-in-input-email" class="form-control form-control-solid" />
                            <!--end::Email-->
                        </div>
                        <!--end::Input group=-->
                        <div class="fv-row mb-7">
                            <!--begin::Password-->
                            <input type="password" placeholder="Password" name="password" autocomplete="off" data-kt-translate="sign-in-input-password" class="form-control form-control-solid" />
                            <!--end::Password-->
                        </div>
                        <!--end::Input group=-->
                        <!--begin::Wrapper-->
                        <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-10">
                            <div></div>
                            <!--begin::Link-->
                            <a href="/reset-password" class="link-primary" data-kt-translate="sign-in-forgot-password">Forgot
                                Password ?</a>
                            <!--end::Link-->
                        </div>
                        <!--end::Wrapper-->
                        <!--begin::Actions-->
                        <div class="d-flex flex-stack">
                            <!--begin::Submit-->
                            <button id="kt_sign_in_submit" class="btn btn-primary me-2 flex-shrink-0">
                                <!--begin::Indicator label-->
                                <span class="indicator-label" data-kt-translate="sign-in-submit">Sign In</span>
                                <!--end::Indicator label-->
                                <!--begin::Indicator progress-->
                                <span class="indicator-progress">
                                    <span data-kt-translate="general-progress">Please wait...</span>
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                                <!--end::Indicator progress-->
                            </button>

                            <div class="d-flex align-items-center">
                                <!-- <div class="text-gray-400 fw-semibold fs-6 me-6">Or</div> -->
                                <div class="m-0">
                                    <span class="text-gray-400 fw-bold fs-5 me-2" data-kt-translate="sign-in-head-desc">Not
                                        a
                                        Member yet?</span>
                                    <a href="/sign-up" class="link-primary fw-bold fs-5" data-kt-translate="sign-in-head-link">Sign Up</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
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
                                                            echo ($_SERVER['HTTP_HOST'] === 'localhost') ? '/dash.php' : '/dash';
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

            window.onload = () => {
                $('#kt_modal_login').modal('show');
            }
        </script>
    <?php } ?>