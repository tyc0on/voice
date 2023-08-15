<?php
// 1. Error Reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Define the URL and JSON Payload
$url = "https://easyaivoice.com/apitest";
$jsonPayload = "{\"app_permissions\":\"278165808741953\",\"application_id\":\"1139880209093500968\",\"channel\":{\"flags\":0,\"guild_id\":\"1093720384643276820\",\"id\":\"1139838342838632529\",\"last_message_id\":\"1140897101585322065\",\"name\":\"room-1\",\"nsfw\":false,\"parent_id\":\"1093720386102902784\",\"permissions\":\"281474976710655\",\"position\":5,\"rate_limit_per_user\":0,\"topic\":null,\"type\":0},\"channel_id\":\"1139838342838632529\",\"data\":{\"id\":\"1139888330675327070\",\"name\":\"voice\",\"options\":[{\"name\":\"voice_model\",\"type\":3,\"value\":\"penis2\"},{\"name\":\"audio\",\"type\":11,\"value\":\"1140903356362141726\"}],\"resolved\":{\"attachments\":{\"1140903356362141726\":{\"content_type\":\"audio\/mpeg\",\"ephemeral\":true,\"filename\":\"01-the-screaming-sheep_1.mp3\",\"id\":\"1140903356362141726\",\"proxy_url\":\"https:\/\/media.discordapp.net\/ephemeral-attachments\/1139888330675327070\/1140903356362141726\/01-the-screaming-sheep_1.mp3\",\"size\":838965,\"url\":\"https:\/\/cdn.discordapp.com\/ephemeral-attachments\/1139888330675327070\/1140903356362141726\/01-the-screaming-sheep_1.mp3\"}}},\"type\":1},\"entitlement_sku_ids\":[],\"entitlements\":[],\"guild\":{\"features\":[],\"id\":\"1093720384643276820\",\"locale\":\"en-US\"},\"guild_id\":\"1093720384643276820\",\"guild_locale\":\"en-US\",\"id\":\"1140903357033238650\",\"locale\":\"en-US\",\"member\":{\"avatar\":null,\"communication_disabled_until\":null,\"deaf\":false,\"flags\":0,\"joined_at\":\"2023-04-07T02:14:20.360000+00:00\",\"mute\":false,\"nick\":null,\"pending\":false,\"permissions\":\"281474976710655\",\"premium_since\":null,\"roles\":[],\"unusual_dm_activity_until\":null,\"user\":{\"avatar\":\"3d2e26d8c15a387a5c6d4d14f08c5fd7\",\"avatar_decoration\":null,\"discriminator\":\"0\",\"global_name\":\"Tycoon\",\"id\":\"810919246744780840\",\"public_flags\":0,\"username\":\"tycoon69\"}},\"token\":\"aW50ZXJhY3Rpb246MTE0MDkwMzM1NzAzMzIzODY1MDplNllmM296eEthaExKMUhmcVBUdEpPQ1ZqaGhaUVRINnFYRHBkUEVmZEE4TFJkdFpia09uZUV6bmtsZ3BZR1lQY25NVG15SmZmWmh6OEZPVUpCQWdCbXVweDQ3WDJjdllHV0o3d3VtcWhkTkFmOWxHdUZORkFob244c2VFQzFYUw\",\"type\":2,\"version\":1}"; // truncated for brevity

// turn $jsonPayload into an array
$arrPayload = json_decode($jsonPayload, true);
//print
$jsonPayload = json_encode($arrPayload);

// 3. Setup cURL for POST request
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonPayload)
    )
);

$result = curl_exec($ch);

// 4. Display Results
echo $result;

// 5. Close cURL Session
curl_close($ch);
