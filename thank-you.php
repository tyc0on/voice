<?php
$filename = basename(__FILE__, '.php');
$coreFilePath = 'core/' . $filename . '.php';

if (file_exists($coreFilePath)) {
    include $coreFilePath;
} else {
    http_response_code(500);
    echo "Core file not found: {$coreFilePath}";
}
