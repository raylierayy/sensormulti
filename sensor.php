<?php
// Only return sensor data if sensor.txt was written within the last 3 seconds.
// If the Arduino is disconnected, the Python script stops writing and the file
// goes stale — returning nothing here causes the JS to treat all sensors as disconnected.

$file = __DIR__ . "/sensor.txt";

if (!file_exists($file)) {
    echo "";
    exit;
}

$age = time() - filemtime($file);   // seconds since last write
if ($age > 3) {
    echo "";   // stale — Arduino not connected
    exit;
}

echo file_get_contents($file);
?>
