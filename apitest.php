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
    // get last id
    $interaction = $con->insert_id;
} else {
}
$stmt->close();
// $sql = "INSERT INTO log (log) VALUES (?)";
// $stmt = $con->prepare($sql);
// $stmt->bind_param('s', $url);
// if ($stmt->execute()) {
// } else {
// }
// echo $url;

$payload2 = array(
    "type" => 4,
    "data" => array(
        "content" => "Voice received. Processing..."
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

$attachments = $arrpayload['data']['resolved']['attachments'];
$firstAttachment = reset($attachments);  // Get the first attachment regardless of its key

$vm = $arrpayload['data']['options'];
$firstVm = reset($vm);  // Get the first attachment regardless of its key

$jobData = array(
    'audio_url' => $firstAttachment['url'],
    'voice_model' => $firstVm['value'],
    'settings' => 'none',
    'guild_id' => $arrpayload['guild_id'],
    'channel_id' => $arrpayload['channel_id'],
    'message_id' => $arrpayload['message_id'],
    'interaction_id' => $arrpayload['id'],
    'interaction_token' => $arrpayload['token'],
    'interaction' => $interaction,
    'type' => 'discord',
    'application_id' => $arrpayload['application_id'],
    'metadata' => array(
        'member' => array(
            'user' => array(
                'id' => $arrpayload['member']['user']['id'],
                'username' => $arrpayload['member']['user']['username'],
                'global_name' => $arrpayload['member']['user']['global_name'],
            )
        )
    )
);

// lookup users.id with where discord_id = $arrpayload['member']['user']['id']
$sql = "SELECT id FROM users WHERE discord_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('s', $arrpayload['member']['user']['id']);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();
if ($user_id) {
} else {
    $sql = "INSERT INTO users (discord_id) VALUES (?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $arrpayload['member']['user']['id']);
    $stmt->execute();
    $user_id = $stmt->insert_id;
    $stmt->close();

    $sql = "INSERT INTO credits (user_id, amount, source) VALUES (?, 3600, 'free_trial')";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $stmt->close();
}

$sql = "SELECT SUM(amount) AS total_credits FROM credits WHERE user_id = ? AND (expiration_date IS NULL OR expiration_date > CURRENT_TIMESTAMP)";
$stmt = $con->prepare($sql);
$stmt->bind_param('s', $user_id);
$stmt->execute();
$stmt->bind_result($total_credits);
$stmt->fetch();
$stmt->close();

$jobData['credits'] = $total_credits;

// $jobData = array("audio_url" => $arrpayload['data']['resolved']['attachments'][0]['url'], "settings" => "none", "metadata" => "");
// $jobData = $_POST;

// Validation
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require 'vendor/autoload.php';

$connection = new AMQPStreamConnection($sqlh, 5672, 'admintycoon', $rabbitp, 'voice');
$channel = $connection->channel();

$channel->queue_declare('job_queue', false, true, false, false);

$msg = new AMQPMessage(json_encode($jobData));
$channel->basic_publish($msg, '', 'job_queue');

$channel->close();
$connection->close();

// header("HTTP/1.1 200 OK");
// echo 'Job submitted successfully.';
// print_r($jobData);




// close $con
$con->close();

exit();
