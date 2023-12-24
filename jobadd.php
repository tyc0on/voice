<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

include 'include.php';
require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobData = $_POST;

    // Validation
    if (!isset($jobData['audio_url']) || !isset($jobData['settings']) || !isset($jobData['metadata'])) {
        header("HTTP/1.1 400 Bad Request");
        echo 'Invalid job data.';
        exit();
    }

    $connection = new AMQPStreamConnection($sqlh, 5672, 'admintycoon', $rabbitp, 'voice');
    $channel = $connection->channel();

    $channel->queue_declare('job_queue', false, true, false, false, false, new \PhpAmqpLib\Wire\AMQPTable(['x-max-priority' => 10]));

    $msg = new AMQPMessage(json_encode($jobData));
    $channel->basic_publish($msg, '', 'job_queue');

    $channel->close();
    $connection->close();

    header("HTTP/1.1 200 OK");
    echo 'Job submitted successfully.';
    print_r($jobData);
    exit();
}
