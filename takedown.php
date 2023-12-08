<?php
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 365);

// errors on
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


session_start();
include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);
include 'config.php';

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}

function decodeBase62($encoded)
{
    $base62 = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $num = '0';

    for ($i = 0; $i < strlen($encoded); $i++) {
        $pos = strpos($base62, $encoded[$i]);
        $num = bcadd(bcmul($num, '62'), (string)$pos);
    }

    return $num;
}



$title = "Takedown Request";
$pagescripts = '<script src="assets/plugins/global/plugins.bundle.js"></script>
<script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>';
include 'core/header.php';

// List of AI Voice Models | <a href="/colab" target="_blank">Colab List</a> | <a href="https://easyaivoice.com" target="_blank">Use via Web App</a> | <a href="https://discord.gg/3WJ8r6Bf5A" target="_blank">Use via Discord Bot</a> 👈 Use /voice to change your voice just like Midjourney!
?>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid" style="background: url('1f1e371d-cb89-4735-b106-2f9c30de9be5.jpeg') repeat-y center top; background-size: 100% auto;">
        <!--begin::Toolbar-->

        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">

                <!-- Display Colab links -->
                <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                    <div class="col-xl-12">
                        <div class="card card-flush">
                            <div class="card-header" style="min-height:55px;">
                            <div class="card-title flex-column">
                        <h3 class="fw-bold mb-1">Takedown Request Form</h3>
                        <small class="text-muted">Please complete the form to submit your request.</small>
                    </div>
                            </div>
                            <div class="card-body pt-0">
                    <form action="/takedownemail" method="post">
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full Name:</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" required>
                        </div>
                        <div class="mb-3">
                            <label for="contactEmail" class="form-label">Contact Email:</label>
                            <input type="email" class="form-control" id="contactEmail" name="contactEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="affiliation" class="form-label">Affiliation:</label>
                            <select class="form-control" id="affiliation" name="affiliation" required>
                                <option value="">-- Select One --</option>
                                <option value="voiceActor">Voice Actor</option>
                                <option value="legalRepresentative">Legal Representative</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="url" class="form-label">URL of the Infringing Voice Model:</label>
                            <input type="url" class="form-control" id="url" name="url" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description of the Infringing Content:</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rightsOwnership" name="rightsOwnership" required>
                            <label class="form-check-label" for="rightsOwnership">I am the owner of the exclusive rights to the voice recording or am authorized to act on behalf of the owner.</label>
                        </div>
                        <div class="mb-3">
                            <label for="declarationName" class="form-label">Your Full Legal Name for Declaration:</label>
                            <input type="text" class="form-control" id="declarationName" name="declarationName" required>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Date:</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </form>
                </div>
                        </div>
                    </div>
                </div>


                <?php include 'core/footer.php'; ?>