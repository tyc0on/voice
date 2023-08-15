<?php
require 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$app = new Slim\App();

// Endpoint to submit a job
$app->post('/submitJob', function ($request, $response) {
    $jobData = $request->getParsedBody();

    // Validation (You can expand on this)
    if (!isset($jobData['audio_url']) || !isset($jobData['settings']) || !isset($jobData['metadata'])) {
        return $response->withStatus(400)->write('Invalid job data.');
    }

    $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
    $channel = $connection->channel();

    $channel->queue_declare('job_queue', false, true, false, false);

    $msg = new AMQPMessage(json_encode($jobData));
    $channel->basic_publish($msg, '', 'job_queue');

    $channel->close();
    $connection->close();

    return $response->withStatus(200)->write('Job submitted successfully.');
});

$app->run();
