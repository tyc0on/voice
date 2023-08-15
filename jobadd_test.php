<?php
// max exe time 5 seconds
set_time_limit(5);

// $endpointUrl = "https://easyaivoice.com/apitest"; // Replace with the actual URL
$endpointUrl = "http://localhost:5011/jobadd.php"; // Replace with the actual URL

// Data to be sent in the POST request
$data = array(
    'audio_url' => 'https://easyaivoice.com/i.mp3',
    'settings' => 'none',
    'metadata' => '{"member": {"user": {"id": "123"}}}'
);

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $endpointUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
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
