<?php
// errors on
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}


$ch = curl_init();
$cursor = "null";
// $cursor = 500;

while (true) {


    // curl_setopt($ch, CURLOPT_URL, "https://www.weights.gg/api/trpc/models.getAll?input=%7B%22json%22%3A%7B%22limit%22%3A100%2C%22tagFilters%22%3A%5B%5D%2C%22search%22%3A%22%22%2C%22sortFilter%22%3A%22createdAt%22%2C%22source%22%3A%22all%22%2C%22cursor%22%3A" . $cursor . "%7D%7D");
    curl_setopt($ch, CURLOPT_URL, "https://www.weights.gg/api/trpc/models.getAll?input=%7B%22json%22%3A%7B%22limit%22%3A100%2C%22tagFilters%22%3A%5B%5D%2C%22search%22%3A%22%22%2C%22sortFilter%22%3A%22createdAt%22%2C%22source%22%3A%22all%22%2C%22cursor%22%3A" . $cursor . "%7D%7D");

    // if cursor is null then make cursor = 0
    if ($cursor == "null") {
        $cursor = 0;
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $output = curl_exec($ch);

    $dataArray = json_decode($output, true);

    // if (!isset($dataArray['result']['data']['json']) || count($dataArray['result']['data']['json']) == 0) {
    //     break;
    // }

    echo "count: " . count($dataArray['result']['data']['json']) . "\n";

    $insertcount = 0;
    foreach ($dataArray['result']['data']['json'] as $item) {
        $messageId = $item['discordThreadId'];
        $createdAt = $item['createdAt'];
        $title = $item['title'];
        $url = $item['url'];
        $rawJson = json_encode($item);

        $escapedMessageId = $con->real_escape_string($messageId);
        $escapedCreatedAt = $con->real_escape_string($createdAt);
        $escapedRawJson = $con->real_escape_string($rawJson);
        $escapedTitle = $con->real_escape_string($title);
        $escapedUrl = $con->real_escape_string($url);

        $sql = "SELECT * FROM weights WHERE message_id = '{$escapedMessageId}'";
        $result = $con->query($sql);

        if ($result->num_rows == 0) {
            $sql = "INSERT INTO weights (message_id, created_at, raw_json, title, url) VALUES ('{$escapedMessageId}', '{$escapedCreatedAt}', '{$escapedRawJson}', '{$escapedTitle}', '{$escapedUrl}')";
            $con->query($sql);
            echo "inserted: {$sql}\n";
            $insertcount++;

            if ($con->errno) {
                echo "\nerror: " . $con->error;
            }
        } else {
            echo "message_id: {$escapedMessageId} already exists\n";
        }
    }
    // if insertcount == 0 then break
    if ($insertcount == 0) {
        break;
    }

    $cursor += 100;
}

curl_close($ch);
$con->close();
