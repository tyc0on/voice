<?php
include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}

$folder = "/var/www/easyaivoice.com/public_html/samples";
$file = "sample.mp3";
// create samples folder if not exists
if (!file_exists($folder)) {
    mkdir($folder, 0755, true);
}

// loop through all `files` and create job for each with curl
$sql = "SELECT * FROM files WHERE md5 <> 'T' AND index_name != '' ORDER BY `id` DESC";
$stmt = $con->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require 'vendor/autoload.php';

$connection = new AMQPStreamConnection($sqlh, 5672, 'admintycoon', $rabbitp, 'voice');
$channel = $connection->channel();

$channel->queue_declare('job_queue', false, true, false, false);

$pitches = array(-16, -12, -8, -4, 0, 4, 8, 12, 16);

while ($row = $result->fetch_assoc()) {
    // url is the url of the audio file to process
    $url = "https://easyaivoice.com/" . $file;
    // $name = $row['name'];
    //model url
    $name = $row['url'];

    // lookup files.name where files.url = $name
    $sql = "SELECT * FROM files WHERE url = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result2 = $stmt->get_result();
    $stmt->close();
    // set model_name to files.name
    if ($result2->num_rows > 0) {
        $row2 = $result2->fetch_assoc();
        $model_name = $row2['name'];
    } else {
        //die("Model not found.");
    }



    //if file samples/$name.p4.mp3 exists skip
    $existing = $folder . "/" . $model_name . ".p4.mp3"; // __DIR__ . 
    echo $existing . "<br>\n";
    if (file_exists($existing)) {
        // continue;
    } else {



        foreach ($pitches as $pitch) {

            $jobData = array(
                'audio_url' => $url,
                'voice_model' => $name,
                'type' => 'eav',
                'pitch' => $pitch,
                'credits' => 99999999,
                'settings' => 'none',
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
                            'username' => $folder,
                            'global_name' => 'None',
                        )
                    )
                )
            );




            $msg = new AMQPMessage(json_encode($jobData));
            $channel->basic_publish($msg, '', 'job_queue');
        }
    }
    // die;
}
$channel->close();
$connection->close();
