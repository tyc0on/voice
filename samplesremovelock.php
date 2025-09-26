<?php
require 'vendor/autoload.php';
include 'include.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);
if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error);
    exit();
}

// Initialize S3 client
$s3 = new S3Client([
    'version'                 => 'latest',
    'region'                  => 'sfo3',
    'endpoint'                => 'https://sfo3.digitaloceanspaces.com',
    'use_path_style_endpoint' => true,
    'credentials'             => ['key' => $spaces_key, 'secret' => $spaces_secret],
]);

$bucket = 'voe';
$prefixes = ['samples', 'samples2'];
$totalRemoved = 0;

foreach ($prefixes as $prefix) {
    echo "Processing {$prefix} folder...\n";
    $lockFiles = [];
    
    // List all .lock files in this prefix efficiently
    $params = [
        'Bucket' => $bucket,
        'Prefix' => $prefix . '/',
    ];
    
    do {
        try {
            $result = $s3->listObjectsV2($params);
            
            if (!empty($result['Contents'])) {
                foreach ($result['Contents'] as $object) {
                    $key = $object['Key'];
                    // Only collect .lock files
                    if (substr($key, -5) === '.lock') {
                        $lockFiles[] = ['Key' => $key];
                        echo "Found lock file: {$key}\n";
                    }
                }
            }
            
            $params['ContinuationToken'] = $result['NextContinuationToken'] ?? null;
        } catch (AwsException $e) {
            echo "Error listing objects in {$prefix}: " . $e->getMessage() . "\n";
            break;
        }
    } while (!empty($params['ContinuationToken']));
    
    // Batch delete lock files (up to 1000 at a time)
    if (!empty($lockFiles)) {
        $chunks = array_chunk($lockFiles, 1000); // S3 deleteObjects limit is 1000
        
        foreach ($chunks as $chunk) {
            try {
                $result = $s3->deleteObjects([
                    'Bucket' => $bucket,
                    'Delete' => [
                        'Objects' => $chunk,
                        'Quiet' => false, // Set to true to reduce response size if you don't need details
                    ]
                ]);
                
                $deleted = count($result['Deleted'] ?? []);
                $errors = count($result['Errors'] ?? []);
                $totalRemoved += $deleted;
                
                echo "Batch deleted {$deleted} lock files from {$prefix}\n";
                if ($errors > 0) {
                    echo "Encountered {$errors} errors in this batch\n";
                    foreach ($result['Errors'] as $error) {
                        echo "Error deleting {$error['Key']}: {$error['Message']}\n";
                    }
                }
                
            } catch (AwsException $e) {
                echo "Error batch deleting from {$prefix}: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "No lock files found in {$prefix}\n";
    }
}

echo "\nCompleted! Total lock files removed: {$totalRemoved}\n";
$con->close();
