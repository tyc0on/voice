<?php
include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}

$folder = "/var/www/easyaivoice.com/public_html/samples2";
$file = "sample2.mp3";
if (!file_exists($folder)) {
    mkdir($folder, 0755, true);
}

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
    $url = "https://easyaivoice.com/" . $file;
    $name = $row['url'];

    // Retrieve the model name from the database
    $sql = "SELECT * FROM files WHERE url = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result2 = $stmt->get_result();
    $stmt->close();
    if ($result2->num_rows > 0) {
        $row2 = $result2->fetch_assoc();
        $model_name = $row2['name'];
    } 

    // Check for file existence for each pitch
    foreach ($pitches as $pitch) {
        $existing = $folder . "/" . $model_name . ".p" . $pitch . ".mp3";
        echo $existing . "<br>\n";
        if (!file_exists($existing)) {

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
}
$channel->close();
$connection->close();
