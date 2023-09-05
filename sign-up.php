<?php
$filename = basename(__FILE__, '.php'); // Get the filename without the extension
$coreFilePath = 'core/' . $filename . '.php'; // Build the path to the core file

if (file_exists($coreFilePath)) {
    include $coreFilePath;
} else {
    echo "Core file not found: $coreFilePath";
}
