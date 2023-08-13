<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data["type"]) && $data["type"] == 1) {
        $response = array("type" => 1);
    } else {
        $response = array(
            "type" => 4,
            "data" => array(
                "tts" => false,
                "content" => "Congrats on sending your command!",
                "embeds" => array(),
                "allowed_mentions" => array("parse" => array())
            )
        );
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
}
