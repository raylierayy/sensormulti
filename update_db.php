<?php
require 'db_connection.php';

// Drop the old table
$drop_sql = "IF OBJECT_ID('dbo.CalibrationPresets', 'U') IS NOT NULL DROP TABLE dbo.CalibrationPresets;";
sqlsrv_query($conn, $drop_sql);

// Create the new table
$create_sql = "
CREATE TABLE CalibrationPresets (
    ID INT IDENTITY(1,1) PRIMARY KEY,
    
    assigned_side_1 VARCHAR(50),
    calibration_distance_1 FLOAT,
    allowed_distance_error_1 FLOAT,
    
    assigned_side_2 VARCHAR(50),
    calibration_distance_2 FLOAT,
    allowed_distance_error_2 FLOAT,
    
    assigned_side_3 VARCHAR(50),
    calibration_distance_3 FLOAT,
    allowed_distance_error_3 FLOAT,
    
    created_at DATETIME DEFAULT GETDATE()
);
";
$stmt = sqlsrv_query($conn, $create_sql);

if ($stmt) {
    echo "Successfully updated CalibrationPresets table for multi-sensor presets.";
} else {
    echo "Error updating table: " . print_r(sqlsrv_errors(), true);
}
?>
