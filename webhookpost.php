<?php
$url = "https://discord.com/api/webhooks/1139786475911794738/KcbCIMYym1NicZ9tn-a8bdwmOnj7ZUuhijc4TxhYMp5vtfEICC-ZUyZANUo76PXZyPvC";
// $url = "https://discord.com/api/webhooks/1139786475911794738/KcbCIMYym1NicZ9tn-a8bdwmOnj7ZUuhijc4TxhYMp5vtfEICC-ZUyZANUo76PXZyPvC/messages/1140239155041939496";

// Step 1: Download the MP3 file
$tempFile = tempnam(sys_get_temp_dir(), 'mp3');

$mp3Ch = curl_init("https://easyaivoice.com/i.mp3");
$file = fopen($tempFile, 'wb');
curl_setopt($mp3Ch, CURLOPT_FILE, $file);
curl_setopt($mp3Ch, CURLOPT_HEADER, 0);
curl_exec($mp3Ch);
curl_close($mp3Ch);
fclose($file);

// Step 2: Send the MP3 file to Discord
$ch = curl_init($url);

$postFields = [
    "username" => "GeneratorBot Worker ⛏️",
    "content" => "<@810919246744780840> Here is your audio file:",
    "file0" => curl_file_create($tempFile, "audio/mpeg", time() . ".mp3")
];

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));

$response = curl_exec($ch);

unlink($tempFile); // Cleaning up the temporary file

echo $response;
curl_close($ch);
