<?php
// calculate script execution time
$time_start = microtime(true);

include 'include.php';
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);
if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error);
    exit();
}

/* ---------- Helpers ---------- */
function s3_client(string $key, string $secret): S3Client {
    return new S3Client([
        'version'                 => 'latest',
        'region'                  => 'sfo3',
        'endpoint'                => 'https://sfo3.digitaloceanspaces.com',
        'use_path_style_endpoint' => true,
        'credentials'             => ['key' => $key, 'secret' => $secret],
    ]);
}
function clamp_sample_dir(?string $dir): string {
    if (!$dir) return 'samples';
    
    // Handle S3 URLs like "s3://voe/samples2" - extract just the directory name
    if (preg_match('#s3://[^/]+/(.+)#', $dir, $matches)) {
        $dir = $matches[1];
    }
    
    return in_array($dir, ['samples','samples2'], true) ? $dir : 'samples';
}

/* ===================== Discord branch ===================== */
if (isset($_POST['type']) && $_POST['type'] === 'discord') {

    switch ($_POST['channel_id'] ?? '') {
        case "1142759953103335464":
            $url = "https://discord.com/api/webhooks/1142760352178769941/AO1_OUEq8d9iREiBLJwzpuNoW0raQPkYRBotq9sxH4FRtg2-mGcrHtgtDZWwa6zhDxeC";
            break;
        case "1143077531902296074":
            $url = "https://discord.com/api/webhooks/1143077993015672852/CE9LAKVOfv_H2MtmELlSPJWh5Zx820DKF_kGznfeX_EbdGm5hCguwUg6J_Tw-llgFV9-";
            break;
        case "1143077533735198761":
            $url = "https://discord.com/api/webhooks/1143078096333975624/1vwuKb_95rmpmtSvo4XXMp9PWwmDL3tGqBuhdILM0y3Tqbr_utBEadYhiTUxGXIOcL27";
            break;
        default:
            $url = "https://discord.com/api/webhooks/1142760352178769941/AO1_OUEq8d9iREiBLJwzpuNoW0raQPkYRBotq9sxH4FRtg2-mGcrHtgtDZWwa6zhDxeC";
    }

    if (isset($_POST['error_message'])) {
        $error_payload = [
            "username" => "VoicezeBot Worker ⛏️",
            "content"  => "<@" . ($_POST['user_id'] ?? '0') . "> Error: " . $_POST['error_message'],
        ];
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($error_payload),
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        echo "error posted";
        die;
    }

    if (!isset($_FILES['audioFile'])) { die("No audio file uploaded."); }
    if ($_FILES['audioFile']['error'] !== UPLOAD_ERR_OK) { die("Error uploading audio file."); }

    $tempFile  = $_FILES['audioFile']['tmp_name'];
    if (!file_exists($tempFile)) { die("Uploaded file not found."); }

    $timestamp = date("ymd-His");
    $pitch = isset($_POST['pitch']) && $_POST['pitch'] !== "0" ? "_p" . $_POST['pitch'] : "";
    $extension = pathinfo($_FILES['audioFile']['name'], PATHINFO_EXTENSION) ?: 'mp3';

    $postFields = [
        "username" => "VoicezeBot Worker ⛏️",
        "content"  => "<@" . ($_POST['user_id'] ?? '0') . "> Here is your audio file:",
        "file0"    => curl_file_create(
            $tempFile,
            "audio/mpeg",
            $timestamp . "_" . ($_POST['original_name'] ?? 'audio') . $pitch . "_" . ($_POST['username'] ?? 'user') . "." . $extension
        )
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => ['Content-Type: multipart/form-data'],
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    @unlink($tempFile);

    // store webhook response (optional)
    $stmt = $con->prepare("INSERT INTO log (log) VALUES (?)");
    $stmt->bind_param('s', $response);
    $stmt->execute(); $webhook = $con->insert_id; $stmt->close();

    // billing/credits, simplified (kept from your code)
    $stmt = $con->prepare("SELECT id FROM users WHERE discord_id = ?");
    $stmt->bind_param("i", $_POST['user_id']); $stmt->execute();
    $res = $stmt->get_result(); $userid = ($res->fetch_assoc()['id'] ?? 0); $stmt->close();

    $resp = json_decode($response, true);
    $audio_url   = $resp['attachments'][0]['url'] ?? '';
    $audio_len_n = -(float)round((float)($_POST['audio_length'] ?? 0), 2);
    $msg = "spending";
    $stmt = $con->prepare("INSERT INTO credits (user_id, amount, source, notes, interaction, webhook) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idssii", $userid, $audio_len_n, $msg, $audio_url, $_POST['interaction'], $webhook);
    $stmt->execute(); $stmt->close();

/* ===================== EAV branch (jobs & samples) ===================== */
} elseif (isset($_POST['type']) && $_POST['type'] === 'eav') {

    if (!isset($_FILES['audioFile'])) {
        if (!empty($_POST['job'])) {
            $status = "failed";
            $stmt = $con->prepare("UPDATE jobs SET status = ?, error = ? WHERE id = ?");
            $stmt->bind_param('ssi', $status, $_POST['error_message'], $_POST['job']);
            $stmt->execute(); $stmt->close();

            $stmt = $con->prepare("SELECT batch_id FROM jobs WHERE id = ?");
            $stmt->bind_param('i', $_POST['job']); $stmt->execute();
            $stmt->bind_result($batch_id); $stmt->fetch(); $stmt->close();

            $stmt = $con->prepare("UPDATE batch SET status = ?, error = ? WHERE id = ?");
            $stmt->bind_param('ssi', $status, $_POST['error_message'], $batch_id);
            $stmt->execute(); $stmt->close();
        }
        die("No audio file uploaded...");
    }
    if ($_FILES['audioFile']['error'] !== UPLOAD_ERR_OK) { die("Error uploading audio file."); }

    $tempFile = $_FILES['audioFile']['tmp_name'];
    if (!file_exists($tempFile)) { die("Uploaded file not found."); }

    // common pitch suffix for both sub-branches
    $pitchSuffix = (isset($_POST['pitch']) && $_POST['pitch'] != 0) ? ('.p' . (int)$_POST['pitch']) : '';

    /* ----- A) Regular job (has job id): store under paudios/ in Spaces ----- */
    if (!empty($_POST['job'])) {
        if (!is_dir('paudios')) { mkdir('paudios', 0755, true); }
        $timestamp = date("ymd-His");
        $jobId = (int)$_POST['job'];
        $extension = 'mp3';                     // normalize to mp3
        $localName = "paudios/{$timestamp}.j{$jobId}{$pitchSuffix}.{$extension}";

        // move to a stable name first, then upload
        move_uploaded_file($tempFile, $localName);

        try {
            $s3 = s3_client($spaces_key, $spaces_secret);
            $result = $s3->putObject([
                'Bucket'       => 'voe',
                'Key'          => $localName,     // keeps paudios/ prefix remotely
                'SourceFile'   => $localName,
                'ACL'          => 'public-read',
                'ContentType'  => 'audio/mpeg',
                'CacheControl' => 'public, max-age=31536000, immutable',
            ]);
            $file_url = $result->get('ObjectURL');
        } catch (Throwable $e) {
            echo $e->getMessage() . "\n";
        }

        @unlink($localName);

        // DB updates (kept from your code)
        $stmt = $con->prepare("INSERT INTO paudio_files (user_id, file_path, file_format, job_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('isss', $_POST['user_id'], $localName, $extension, $jobId);
        $stmt->execute(); $paudio_id = $con->insert_id; $stmt->close();

        $status = "complete";
        $stmt = $con->prepare("UPDATE jobs SET paudio_id = ?, status = ? WHERE id = ?");
        $stmt->bind_param('isi', $paudio_id, $status, $jobId); $stmt->execute(); $stmt->close();

        $stmt = $con->prepare("SELECT batch_id FROM jobs WHERE id = ?");
        $stmt->bind_param('i', $jobId); $stmt->execute();
        $stmt->bind_result($batch_id); $stmt->fetch(); $stmt->close();

        $status = "complete";
        $stmt = $con->prepare("SELECT 1 FROM jobs WHERE batch_id = ? AND status != ?");
        $stmt->bind_param('is', $batch_id, $status); $stmt->execute();
        $r = $stmt->get_result(); $stmt->close();
        if ($r->num_rows === 0) {
            $stmt = $con->prepare("UPDATE batch SET status = ? WHERE id = ?");
            $stmt->bind_param('si', $status, $batch_id); $stmt->execute(); $stmt->close();
        }

        $audio_length = round((float)($_POST['audio_length'] ?? 0), 2);
        $stmt = $con->prepare("UPDATE jobs SET audio_length = ? WHERE id = ?");
        $stmt->bind_param('di', $audio_length, $jobId); $stmt->execute(); $stmt->close();

        $stmt = $con->prepare("UPDATE paudio_files SET audio_length = ? WHERE id = ?");
        $stmt->bind_param('di', $audio_length, $paudio_id); $stmt->execute(); $stmt->close();

        if (isset($_POST['timestamp'])) {
            $time_taken = time() - (int)$_POST['timestamp'];
            $stmt = $con->prepare("UPDATE jobs SET time_taken = ? WHERE id = ?");
            $stmt->bind_param('ii', $time_taken, $jobId); $stmt->execute(); $stmt->close();
        }

    /* ----- B) SAMPLE return (no job id): put under voe/samples or voe/samples2 ----- */
    } else {
        $sampleDir = clamp_sample_dir($_POST['sample_dir'] ?? null);
        $modelName = $_POST['model_name'] ?? ($_POST['name'] ?? 'unknown');

        // samples must be .mp3 to match samplesmaker probes
        $extension = 'mp3';
        $key      = "{$sampleDir}/{$modelName}{$pitchSuffix}.{$extension}";
        $lockKey  = "{$sampleDir}/{$modelName}{$pitchSuffix}.lock";

        try {
            $s3 = s3_client($spaces_key, $spaces_secret);

            $result = $s3->putObject([
                'Bucket'       => 'voe',
                'Key'          => $key,
                'SourceFile'   => $tempFile,
                'ACL'          => 'public-read',
                'ContentType'  => 'audio/mpeg',
                'CacheControl' => 'public, max-age=31536000, immutable',
            ]);

            // best-effort lock removal
            try {
                $s3->deleteObject(['Bucket' => 'voe', 'Key' => $lockKey]);
            } catch (AwsException $e) { /* non-fatal */ }

        } catch (Throwable $e) {
            echo $e->getMessage() . "\n";
        }

        @unlink($tempFile);
    }
}

/* ---------- Final JSON ---------- */
$time_end = microtime(true);
$script_time = round($time_end - $time_start, 4);
if (!isset($time_taken)) { $time_taken = 0; }

$con->close();
header('Content-Type: application/json');
echo json_encode([
    "success" => true,
    "execution_time" => $time_taken,
    "script" => $script_time
]);
