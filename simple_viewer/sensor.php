<?php
// We will look for sensor.txt one directory up, since serial_to_file.py is running there
$file = dirname(__DIR__) . "/sensor.txt";

if (!file_exists($file)) {
    echo "ERROR: sensor.txt not found. Is serial_to_file.py running?";
    exit;
}

$age = time() - filemtime($file);
if ($age > 3) {
    echo "ERROR: Data is stale ({$age}s old). Arduino is disconnected or Python script stopped.";
    exit;
}

$content = trim(file_get_contents($file));
if (empty($content)) {
    echo "ERROR: sensor.txt is empty.";
    exit;
}

// Support raw numbers or "XX cm" format
$cleaned = str_replace("cm", "", $content);
$val = trim($cleaned);

if (!is_numeric($val)) {
    echo "ERROR: Invalid data received: " . htmlspecialchars($content);
    exit;
}

echo $val;
?>
