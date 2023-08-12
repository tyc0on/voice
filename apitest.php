<?php
include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}

$payload = file_get_contents('php://input');
$result = endpointVerify($_SERVER, $payload, '9d2adf6234644a7bb7273edff0b41cea8468a751f80160d83401ae1f9285fc96');
http_response_code($result['code']);
echo json_encode($result['payload']);

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
            return ['code' => 200, 'payload' => ['type' => 2]];
        default:
            return ['code' => 400, 'payload' => null];
    }
}

$postData = $payload;

// Convert $_POST data to JSON
$jsonData = json_encode($postData);

// Prepare the SQL statement
$sql = "INSERT INTO log (log) VALUES (?)";

// Prepare the statement
$stmt = $con->prepare($sql);

// Bind the parameter and execute the statement
$stmt->bind_param('s', $jsonData);

if ($stmt->execute()) {
    // Data inserted successfully
    // echo 'Data inserted into the log table.';
} else {
    // Error inserting data
    // echo 'Error: ' . $stmt->error;
}

$stmt->close();
$con->close();
