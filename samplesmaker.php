<?php
declare(strict_types=1);

require 'vendor/autoload.php';
include 'include.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

/* ---------- Tunables ---------- */
$DAYS_BACK   = (int)($_ENV['SAMPLES_DAYS_BACK'] ?? 1);   // only models added in last N days
$MAX_MODELS  = (int)($_ENV['SAMPLES_MAX_MODELS'] ?? 100); // guardrail per run

/* ---------- DB ---------- */
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);
if ($con->connect_errno) { printf("connection failed: %s\n", $con->connect_error); exit(1); }

/* ---------- Spaces ---------- */
$bucket = 'voe';
$sets = [
  'samples'  => 'sample.mp3',
  'samples2' => 'sample2.mp3',
];

$s3 = new S3Client([
  'version'                 => 'latest',
  'region'                  => 'sfo3',
  'endpoint'                => 'https://sfo3.digitaloceanspaces.com',
  'use_path_style_endpoint' => true,
  'credentials'             => ['key'=>$spaces_key,'secret'=>$spaces_secret],
]);

/** List all keys under a prefix into an associative set for O(1) membership checks. */
function s3_list_set(S3Client $s3, string $bucket, string $prefix): array {
  $keys = [];
  $params = ['Bucket'=>$bucket, 'Prefix'=>$prefix . '/'];
  do {
    $res = $s3->listObjectsV2($params);
    if (!empty($res['Contents'])) {
      foreach ($res['Contents'] as $o) $keys[$o['Key']] = true;
    }
    $params['ContinuationToken'] = $res['NextContinuationToken'] ?? null;
  } while (!empty($params['ContinuationToken']));
  return $keys;
}

/* Preload existing remote keys once per prefix */
$existing = [];
foreach (array_keys($sets) as $prefix) {
  $existing[$prefix] = s3_list_set($s3, $bucket, $prefix);  // e.g., 'samples' => ['samples/foo.mp3'=>true, ...]
}

/* ---------- RabbitMQ ---------- */
$connection = new AMQPStreamConnection($sqlh, 5672, 'admintycoon', $rabbitp, 'voice');
$channel = $connection->channel();
$channel->queue_declare('job_queue', false, true, false, false, false,
  new \PhpAmqpLib\Wire\AMQPTable(['x-max-priority' => 10]));

/* ---------- Work ---------- */
$pitches = [-16,-12,-8,-4,0,4,8,12,16];

/* Only recent, valid, active models; pull name directly (no second query per row) */
$sql = "
SELECT id, url, name
FROM files
WHERE md5 <> 'T'
  AND index_name <> ''
  AND active = 1
  AND added_date >= NOW() - INTERVAL ? DAY
ORDER BY id DESC
LIMIT ?
";
$stmt = $con->prepare($sql);
$stmt->bind_param('ii', $DAYS_BACK, $MAX_MODELS);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
  $voiceUrl  = $row['url'];
  $modelName = $row['name'];
  if ($modelName === null || $modelName === '') continue;

  foreach ($sets as $basePath => $sourceFile) {
    $audioUrl = "https://easyaivoice.com/{$sourceFile}";

    foreach ($pitches as $pitch) {
      $suffix  = ($pitch === 0) ? '' : '.p'.$pitch;
      $key     = "{$basePath}/{$modelName}{$suffix}.mp3";
      $lockKey = "{$basePath}/{$modelName}{$suffix}.lock";

      // Skip if already present remotely (from our preloaded set)
      if (isset($existing[$basePath][$key])) continue;

      // Optimistic lock: create lock only if it doesn't exist (no prior HEAD)
      try {
        $s3->putObject([
          'Bucket'       => $bucket,
          'Key'          => $lockKey,
          'Body'         => '1',
          'ACL'          => 'private',
          'IfNoneMatch'  => '*',   // fail if lock already exists
        ]);
      } catch (AwsException $e) {
        // If the object already exists (412 Precondition Failed), skip
        continue;
      }

      echo "{$bucket}/{$key}\n";

      $job = [
        'audio_url'    => $audioUrl,
        'voice_model'  => $voiceUrl,
        'type'         => 'eav',
        'pitch'        => $pitch,
        'credits'      => 99999999,
        'settings'     => 'none',
        'sample_dir'   => $basePath,
        'model_name'   => $modelName,
        'guild_id'=>1,'channel_id'=>1,'message_id'=>1,'interaction_id'=>1,
        'interaction_token'=>'None','interaction'=>1,'application_id'=>1,
        'metadata'=>['member'=>['user'=>[
          'id'=>1,'username'=>"s3://{$bucket}/{$basePath}",'global_name'=>'None'
        ]]],
      ];

      $msg = new AMQPMessage(json_encode($job, JSON_UNESCAPED_SLASHES),
               ['delivery_mode'=>2,'priority'=>1]);
      $channel->basic_publish($msg, '', 'job_queue');
    }
  }
}

$stmt->close();
$res->free();
$channel->close();
$connection->close();
