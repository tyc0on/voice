
<?php
include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}

$url = "https://discord.com/api/v10/interactions/1140216617280749658/aW50ZXJhY3Rpb246MTE0MDIxNjYxNzI4MDc0OTY1ODpNbzNKVlEwNjlPajRwZEZlTTJLRHNjNDRzNzNaWFB5cGJIcWpLRHlrdFFBaFZEVUlHbDFjeTdjRll4MkFRWFh4bmJCcnlaSFpySHJFWmRkT2NteVdxZnBSODB1clFLSEhtRzRJQzF6Mnp6YWNEaE1GOHgySXB0aGFucVc3cWUwYQ/callback";

$webhook = "https://discord.com/api/webhooks/1139786475911794738/KcbCIMYym1NicZ9tn-a8bdwmOnj7ZUuhijc4TxhYMp5vtfEICC-ZUyZANUo76PXZyPvC";
$webhook2 = "https://discord.com/api/webhooks/1140216617280749658/aW50ZXJhY3Rpb246MTE0MDIxNjYxNzI4MDc0OTY1ODpNbzNKVlEwNjlPajRwZEZlTTJLRHNjNDRzNzNaWFB5cGJIcWpLRHlrdFFBaFZEVUlHbDFjeTdjRll4MkFRWFh4bmJCcnlaSFpySHJFWmRkT2NteVdxZnBSODB1clFLSEhtRzRJQzF6Mnp6YWNEaE1GOHgySXB0aGFucVc3cWUwYQ";
$wh3 = "https://discord.com/api/webhooks/1140219768050298960/Q_V2ffzczuoyP2OWwYtcRRpnrtwVydPHgsWJdpk0k-cdWoB7CF2IdVyu3mDsqyN2ZYBZ";

$payload = array(
    "type" => 4,
    "data" => array(
        "content" => "Congrats on sending your reply!"
    )
);

// Step 2: Make an HTTP POST Request
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
));

$response = curl_exec($ch);

curl_close($ch);
echo $response;
