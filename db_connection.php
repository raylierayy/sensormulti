<?php
$serverName = "DESKTOP-P17AQJI\\SQLEXPRESS"; 
$connectionOptions = [ 
    "Database" => "multi_sensor_db", 
    "Uid" => "",   
    "PWD" => "" 
]; 

// Establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
