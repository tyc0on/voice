<?php
// Set the URL of the endpoint where you want to make the GET request
$endpointUrl = "https://easyaivoice.com/apitest"; // Replace with the actual URL

// Data to be sent in the POST request
// $data = array(
//     'video' => 'https://www.youtube.com/watch?v=QRy4JJNTAiA'
// );

// convert JSON string to $data array
$json = '{
    "app_permissions": "278165808741953",
    "application_id": "1139880209093500968",
    "channel": {
      "flags": 0,
      "guild_id": "1093720384643276820",
      "id": "1139838342838632529",
      "last_message_id": "1139896924833579078",
      "name": "room-1",
      "nsfw": false,
      "parent_id": "1093720386102902784",
      "permissions": "281474976710655",
      "position": 5,
      "rate_limit_per_user": 0,
      "topic": null,
      "type": 0
    },
    "channel_id": "1139838342838632529",
    "data": {
      "id": "1139888330675327070",
      "name": "voice",
      "options": [
        {
          "name": "voice_model",
          "type": 3,
          "value": "test18"
        },
        {
          "name": "audio",
          "type": 11,
          "value": "1140204311633207336"
        }
      ],
      "resolved": {
        "attachments": {
          "1140204311633207336": {
            "content_type": "audio/mpeg",
            "ephemeral": true,
            "filename": "intro2.mp3",
            "id": "1140204311633207336",
            "proxy_url": "https://media.discordapp.net/ephemeral-attachments/1139888330675327070/1140204311633207336/intro2.mp3",
            "size": 68336,
            "url": "https://cdn.discordapp.com/ephemeral-attachments/1139888330675327070/1140204311633207336/intro2.mp3"
          }
        }
      },
      "type": 1
    },
    "entitlement_sku_ids": [
      
    ],
    "entitlements": [
      
    ],
    "guild": {
      "features": [
        
      ],
      "id": "1093720384643276820",
      "locale": "en-US"
    },
    "guild_id": "1093720384643276820",
    "guild_locale": "en-US",
    "id": "1140204311947788359",
    "locale": "en-US",
    "member": {
      "avatar": null,
      "communication_disabled_until": null,
      "deaf": false,
      "flags": 0,
      "joined_at": "2023-04-07T02:14:20.360000+00:00",
      "mute": false,
      "nick": null,
      "pending": false,
      "permissions": "281474976710655",
      "premium_since": null,
      "roles": [
        
      ],
      "unusual_dm_activity_until": null,
      "user": {
        "avatar": "3d2e26d8c15a387a5c6d4d14f08c5fd7",
        "avatar_decoration": null,
        "discriminator": "0",
        "global_name": "Tycoon",
        "id": "810919246744780840",
        "public_flags": 0,
        "username": "tycoon69"
      }
    },
    "token": "aW50ZXJhY3Rpb246MTE0MDIwNDMxMTk0Nzc4ODM1OTprWVkzSVRjdEdSMllpb3d5cGhwQ0xvVWl2OHA1M0VsTHEwTjE5OUozQ0FwdmRFaVFqREFDRzc0NVVlaEpVcXJEWWxNcWg1dEMxcWJTYTZqNTFldFBuUm5uOFFuY3ljeXZmaEVCV2hWUVVQaWJXZmJPWjdBb0RwMWcxSUc2MmlmMw",
    "type": 2,
    "version": 1
  }';
$data = json_decode($json, true);
print_r($data);
die;

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
