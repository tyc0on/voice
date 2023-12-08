<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);
include 'config.php';
require 'vendor/autoload.php'; // Path to the SendGrid autoload.php

use SendGrid\Mail\Mail;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect POST data from form
    $fullName = $_POST['fullName'];
    $contactEmail = $_POST['contactEmail'];
    $affiliation = $_POST['affiliation'];
    $url = $_POST['url'];
    $description = $_POST['description'];
    $declarationName = $_POST['declarationName'];
    $date = $_POST['date'];

    // SendGrid details
    $sendgrid_api = $sendgrid_api;
    $from_email = 'hello@nocodelog.com';
    $to_email = 'easyaistudio@gmail.com';

    // Create a new SendGrid Mail object
    $email = new Mail();
    $email->setFrom($from_email, "Nocode Log");
    $email->setSubject("Takedown Request EAV - " . date("Y-m-d"));
    $email->addTo($to_email, "EasyAi Studio");
    $email->addContent(
        "text/plain",
        "Takedown request received from: " . $fullName . "\n" .
        "Contact Email: " . $contactEmail . "\n" .
        "Affiliation: " . $affiliation . "\n" .
        "URL of Infringing Content: " . $url . "\n" .
        "Description of Infringing Content: " . $description . "\n" .
        "Declaration Name: " . $declarationName . "\n" .
        "Date: " . $date
    );
    $email->addContent(
        "text/html",
        "<strong>Takedown request received from:</strong> " . htmlspecialchars($fullName) . "<br>" .
        "<strong>Contact Email:</strong> " . htmlspecialchars($contactEmail) . "<br>" .
        "<strong>Affiliation:</strong> " . htmlspecialchars($affiliation) . "<br>" .
        "<strong>URL of Infringing Content:</strong> " . htmlspecialchars($url) . "<br>" .
        "<strong>Description of Infringing Content:</strong> " . htmlspecialchars($description) . "<br>" .
        "<strong>Declaration Name:</strong> " . htmlspecialchars($declarationName) . "<br>" .
        "<strong>Date:</strong> " . htmlspecialchars($date)
    );

    // Send the email
    $sendgrid = new \SendGrid($sendgrid_api);
    try {
        $response = $sendgrid->send($email);
        // echo "Email sent successfully. Response code: " . $response->statusCode() . "\n";
        echo "Takedown request will be reviewed ASAP\n";
    } catch (Exception $e) {
        echo 'Caught exception: ' .  $e->getMessage() . "\n";
    }
} else {
    echo "No form data received.";
}
?>
