<?php
include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}

//! DONT FORGET TO UPDATE THE PUBLIC KEY
$payload = file_get_contents('php://input');
$result = endpointVerify($_SERVER, $payload, '9d2adf6234644a7bb7273edff0b41cea8468a751f80160d83401ae1f9285fc96');
http_response_code($result['code']);
header('Content-Type: application/json');
echo json_encode($result['payload']);


$arrpayload = json_decode($payload, true);
$url = "https://discord.com/api/v10/interactions/" . $arrpayload['id'] . "/" . $arrpayload['token'] . "/callback";

$sql = "INSERT INTO log (log) VALUES (?)";
$stmt = $con->prepare($sql);
$stmt->bind_param('s', json_encode($result['payload']));
if ($stmt->execute()) {
} else {
}

$sql = "INSERT INTO log (log) VALUES (?)";
$stmt = $con->prepare($sql);
$stmt->bind_param('s', $url);
if ($stmt->execute()) {
} else {
}
// echo $url;

$payload2 = array(
    "type" => 4,
    "data" => array(
        "content" => "Audio file received. You will be notified when it has processed"
    )
);

// Step 2: Make an HTTP POST Request
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload2));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
));

$response = curl_exec($ch);

curl_close($ch);


function endpointVerify(array $headers, string $payload, string $publicKey): array
{
    if (
        !isset($headers['HTTP_X_SIGNATURE_ED25519'])
        || !isset($headers['HTTP_X_SIGNATURE_TIMESTAMP'])
    )
        return ['code' => 401, 'payload' => null];

    $signature = $headers['HTTP_X_SIGNATURE_ED25519'];
    $timestamp = $headers['HTTP_X_SIGNATURE_TIMESTAMP'];

    if (!trim($signature, '0..9A..Fa..f') == '')
        return ['code' => 401, 'payload' => null];

    $message = $timestamp . $payload;
    $binarySignature = sodium_hex2bin($signature);
    $binaryKey = sodium_hex2bin($publicKey);

    if (!sodium_crypto_sign_verify_detached($binarySignature, $message, $binaryKey))
        return ['code' => 401, 'payload' => null];

    $payload = json_decode($payload, true);
    switch ($payload['type']) {
        case 1:
            return ['code' => 200, 'payload' => ['type' => 1]];
        case 2:
            $response = array(
                "type" => 2,
                "data" => array(
                    "tts" => false,
                    "content" => "Congrats on sending your command!2",
                    "embeds" => array(),
                    "allowed_mentions" => array("parse" => array())
                )
            );
            // return ['code' => 200, 'payload' => ['type' => 2]];
            return ['code' => 200, 'payload' => $response];
        case 4:
            $response = array(
                "type" => 4,
                "data" => array(
                    "tts" => false,
                    "content" => "Congrats on sending your command!",
                    "embeds" => array(),
                    "allowed_mentions" => array("parse" => array())
                )
            );
            return ['code' => 200, 'payload' => $response];
        default:
            return ['code' => 400, 'payload' => null];
    }
}

$postData = $payload;

// Convert $_POST data to JSON
$jsonData = json_encode($postData);
$sql = "INSERT INTO log (log) VALUES (?)";
$stmt = $con->prepare($sql);
$stmt->bind_param('s', $jsonData);
if ($stmt->execute()) {
} else {
}

$stmt->close();
$con->close();
