<?php
include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}

// remove all .lock files from samples and samples2 folder
$folder = "/var/www/easyaivoice.com/public_html/samples";
$files = glob($folder . '/*.lock');
foreach ($files as $file) {
    if (is_file($file)) {
        // unlink($file);
        echo "Removed $file\n";
    }
}

$folder = "/var/www/easyaivoice.com/public_html/samples2";
$files = glob($folder . '/*.lock');
foreach ($files as $file) {
    if (is_file($file)) {
        // unlink($file);
        echo "Removed $file\n";
    }
}
