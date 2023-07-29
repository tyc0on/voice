<?php
// Set the URL of the endpoint where you want to make the GET request
$endpointUrl = "https://datawb.com/ngrok.php"; // Replace with the actual URL

// Data to be sent in the GET request
$data = array(
    'u' => 'a918023890123.ngrok.io',
    'inst' => '69'
);

// Append the data to the URL
$endpointUrl .= '?' . http_build_query($data);

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $endpointUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL session and fetch the result
$result = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
}

// Close cURL session
curl_close($ch);

// Echo the result
echo $result;
