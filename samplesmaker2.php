<?php
include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error);
    exit();
}

// Store and check samples on S3 (DO Spaces) instead of local disk
$bucket = 'voe';
$basePath = 'samples2';
$file = 'sample2.mp3';

$sql = "SELECT * FROM files WHERE md5 <> 'T' AND index_name != '' ORDER BY `id` DESC";
$stmt = $con->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

require 'vendor/autoload.php';

// Initialize S3 client (DigitalOcean Spaces)
$s3Client = new S3Client([
    'version' => 'latest',
    'region'  => 'sfo3',
    'endpoint' => 'https://sfo3.digitaloceanspaces.com',
    'credentials' => [
        'key'    => $spaces_key,
        'secret' => $spaces_secret,
    ],
]);

$connection = new AMQPStreamConnection($sqlh, 5672, 'admintycoon', $rabbitp, 'voice');
$channel = $connection->channel();

$channel->queue_declare('job_queue', false, true, false, false, false, new \PhpAmqpLib\Wire\AMQPTable(['x-max-priority' => 10]));

$pitches = array(-16, -12, -8, -4, 0, 4, 8, 12, 16);

while ($row = $result->fetch_assoc()) {
    $url = "https://easyaivoice.com/" . $file;
    $name = $row['url'];

    // Retrieve the model name from the database
    $sql = "SELECT * FROM files WHERE url = ? AND active = 1";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result2 = $stmt->get_result();
    $stmt->close();
    if ($result2->num_rows > 0) {
        $row2 = $result2->fetch_assoc();
        $model_name = $row2['name'];
    }

    // Check for file existence for each pitch using S3
    foreach ($pitches as $pitch) {
        if ($pitch == 0) {
            $key = $basePath . "/" . $model_name . ".mp3";
            $lockKey = $basePath . "/" . $model_name . ".lock";
        } else {
            $key = $basePath . "/" . $model_name . ".p" . $pitch . ".mp3";
            $lockKey = $basePath . "/" . $model_name . ".p" . $pitch . ".lock";
        }

        // Skip if file already exists on S3
        try {
            $exists = $s3Client->doesObjectExistV2($bucket, $key);
        } catch (Exception $e) {
            $exists = false;
        }

        if (!$exists) {
            // Distributed lock via S3
            $lockExists = false;
            try {
                $lockExists = $s3Client->doesObjectExistV2($bucket, $lockKey);
            } catch (Exception $e) {
                $lockExists = false;
            }
            if ($lockExists) {
                continue;
            }

            try {
                $s3Client->putObject([
                    'Bucket' => $bucket,
                    'Key'    => $lockKey,
                    'Body'   => '1',
                    'ACL'    => 'private',
                ]);
            } catch (AwsException $e) {
                continue;
            }

            echo $bucket . "/" . $key . "<br>\n";

            $jobData = array(
                'audio_url' => $url,
                'voice_model' => $name,
                'type' => 'eav',
                'pitch' => $pitch,
                'credits' => 99999999,
                'settings' => 'none',
                'sample_dir' => $basePath,
                'model_name' => $model_name,
                'guild_id' => 1,
                'channel_id' => 1,
                'message_id' => 1,
                'interaction_id' => 1,
                'interaction_token' => 'None',
                'interaction' => 1,
                'application_id' => 1,
                'metadata' => array(
                    'member' => array(
                        'user' => array(
                            'id' => 1,
                            'username' => 's3://'.$bucket.'/'.$basePath,
                            'global_name' => 'None',
                        )
                    )
                )
            );


            // $msg = new AMQPMessage(json_encode($jobData));
            $msg = new AMQPMessage(
                json_encode($jobData),
                array('delivery_mode' => 2, 'priority' => 1) // Setting the message priority
            );
            $channel->basic_publish($msg, '', 'job_queue');
        }
    }
}
$channel->close();
$connection->close();
