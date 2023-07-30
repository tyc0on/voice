<?php
// Step 1: Read the text from the file
$file = 'conversation.txt'; // Replace this with your file path
$text = file_get_contents($file);

// Step 2: Search and replace
$pattern = "/ — (Yesterday|Today) at ([01]?[0-9]|2[0-3]):[0-5]?[0-9] (AM|PM)/";
$replacement = "";
$cleaned_text = preg_replace($pattern, $replacement, $text);

// Step 3: Write the result back to the file
file_put_contents($file, $cleaned_text);
