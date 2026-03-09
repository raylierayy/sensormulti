<?php
require 'session_check.php';
require 'db_connection.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$session_id = isset($_GET['session_id']) ? intval($_GET['session_id']) : 0;
if ($session_id <= 0) {
    die("Invalid session ID. <a href='students.php'>Back</a>");
}

// Fetch session & student info
$sql = "SELECT s.*, st.firstname, st.lastname, st.instructor
        FROM Sessions s
        JOIN Students st ON s.studentID = st.ID
        WHERE s.ID = ?";
$result = sqlsrv_query($conn, $sql, [$session_id]);
if ($result === false || !sqlsrv_has_rows($result)) {
    die("Session not found. <a href='students.php'>Back</a>");
}
$session = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

// Fetch sensor test rows
$tests_sql = "SELECT * FROM Sensors WHERE sessionID = ? ORDER BY ID ASC";
$tests_result = sqlsrv_query($conn, $tests_sql, [$session_id]);
$tests = [];
if ($tests_result !== false && sqlsrv_has_rows($tests_result)) {
    while ($t = sqlsrv_fetch_array($tests_result, SQLSRV_FETCH_ASSOC)) {
        $tests[] = $t;
    }
}

// Format datetime helper
function fmtDt($val) {
    if (!$val) return 'N/A';
    return ($val instanceof DateTime) ? $val->format('Y-m-d H:i:s') : $val;
}

$studentName   = htmlspecialchars($session['firstname'] . ' ' . $session['lastname']);
$instructor    = htmlspecialchars($session['instructor'] ?? 'N/A');
$sessionStatus = htmlspecialchars($session['session_status']);
$dateStarted   = fmtDt($session['datetime_started']);
$dateFinished  = fmtDt($session['datetime_finished']);
$avgRaw        = $session['avg_distance_error_raw'] !== null ? round($session['avg_distance_error_raw'], 2) . ' cm' : 'N/A';
$avgErrPct     = $session['avg_distance_error_percentage'] !== null ? round($session['avg_distance_error_percentage'], 2) . '%' : 'N/A';
$avgAcc        = $session['avg_computed_accuracy'] !== null ? round($session['avg_computed_accuracy'], 2) . '%' : 'N/A';

// Build test rows HTML
$testRowsHtml = '';
if (count($tests) > 0) {
    foreach ($tests as $t) {
        $sensors = [
            [
                'side'    => htmlspecialchars($t['assigned_side_1']),
                'calib'   => $t['calibration_distance_1'],
                'allowed' => $t['allowed_distance_error_1'],
                'final'   => $t['car_distance_from_line_1'],
                'errRaw'  => round($t['final_distance_error_raw_1'], 2),
                'errPct'  => round($t['final_distance_error_percentage_1'], 2),
                'acc'     => round($t['final_computed_accuracy_1'], 2),
            ],
            [
                'side'    => htmlspecialchars($t['assigned_side_2']),
                'calib'   => $t['calibration_distance_2'],
                'allowed' => $t['allowed_distance_error_2'],
                'final'   => $t['car_distance_from_line_2'],
                'errRaw'  => round($t['final_distance_error_raw_2'], 2),
                'errPct'  => round($t['final_distance_error_percentage_2'], 2),
                'acc'     => round($t['final_computed_accuracy_2'], 2),
            ],
            [
                'side'    => htmlspecialchars($t['assigned_side_3']),
                'calib'   => $t['calibration_distance_3'],
                'allowed' => $t['allowed_distance_error_3'],
                'final'   => $t['car_distance_from_line_3'],
                'errRaw'  => round($t['final_distance_error_raw_3'], 2),
                'errPct'  => round($t['final_distance_error_percentage_3'], 2),
                'acc'     => round($t['final_computed_accuracy_3'], 2),
            ],
        ];
        $firstRow = true;
        foreach ($sensors as $s) {
            $testIdCell = $firstRow
                ? '<td rowspan="3" style="font-weight:bold; font-size:1.15em; vertical-align:middle; text-align:center; border-right:2px solid #cbd5e1;">#' . $t['ID'] . '</td>'
                : '';
            $accColor = $s['acc'] >= 90 ? '#166534' : ($s['acc'] >= 70 ? '#854d0e' : '#991b1b');
            $testRowsHtml .= '
                <tr' . ($firstRow ? ' style="border-top:2px solid #cbd5e1;"' : '') . '>
                    ' . $testIdCell . '
                    <td style="font-weight:bold; color:#1d4ed8;">' . $s['side'] . '</td>
                    <td>' . $s['calib'] . '</td>
                    <td>' . $s['allowed'] . '</td>
                    <td style="font-weight:bold;">' . $s['final'] . '</td>
                    <td>' . $s['errRaw'] . '</td>
                    <td>' . $s['errPct'] . '%</td>
                    <td style="color:' . $accColor . '; font-weight:bold;">' . $s['acc'] . '%</td>
                </tr>';
            $firstRow = false;
        }
    }
} else {
    $testRowsHtml = '<tr><td colspan="8" style="padding:20px; text-align:center;">No sensor data logged in this session.</td></tr>';
}

$html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Session Report #' . $session_id . '</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #1f2937; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #1e3a8a; padding-bottom: 16px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #1e3a8a; margin: 0 0 4px; }
        .header p { margin: 2px 0; color: #6b7280; font-size: 12px; }
        .section-title { font-size: 15px; font-weight: bold; color: #1e3a8a; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; margin: 20px 0 10px; }
        .overview-grid { display: table; width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .overview-row { display: table-row; }
        .overview-cell { display: table-cell; padding: 6px 10px; border: 1px solid #e5e7eb; width: 50%; vertical-align: top; }
        .overview-cell strong { color: #374151; }
        .status-ongoing { background: #fef9c3; color: #854d0e; padding: 2px 8px; border-radius: 4px; font-weight: bold; }
        .status-completed { background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 4px; font-weight: bold; }
        table.data-table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 10px; }
        table.data-table th { background: #eff6ff; color: #1e3a8a; padding: 8px 6px; text-align: center; border: 1px solid #cbd5e1; font-size: 11px; }
        table.data-table td { padding: 7px 6px; text-align: center; border: 1px solid #e5e7eb; }
        .averages-row { background: #eff6ff; font-weight: bold; border-top: 3px solid #60a5fa; }
        .footer { text-align: center; margin-top: 30px; font-size: 11px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Multi-Sensor Parking Test Report</h1>
        <p>Student Driver Assessment System &mdash; Session #' . $session_id . '</p>
        <p>Generated: ' . date('Y-m-d H:i:s') . '</p>
    </div>

    <div class="section-title">Session Overview</div>
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="padding:7px 10px; border:1px solid #e5e7eb; width:50%;"><strong>Student Name:</strong><br>' . $studentName . '</td>
            <td style="padding:7px 10px; border:1px solid #e5e7eb;"><strong>Instructor:</strong><br>' . $instructor . '</td>
        </tr>
        <tr>
            <td style="padding:7px 10px; border:1px solid #e5e7eb;"><strong>Session ID:</strong><br>#' . $session_id . '</td>
            <td style="padding:7px 10px; border:1px solid #e5e7eb;"><strong>Status:</strong><br>
                <span class="status-' . strtolower($sessionStatus) . '">' . $sessionStatus . '</span>
            </td>
        </tr>
        <tr>
            <td style="padding:7px 10px; border:1px solid #e5e7eb;"><strong>Started At:</strong><br>' . $dateStarted . '</td>
            <td style="padding:7px 10px; border:1px solid #e5e7eb;"><strong>Finished At:</strong><br>' . $dateFinished . '</td>
        </tr>
    </table>

    <div class="section-title">Sensor Test Results (' . count($tests) . ' attempt(s))</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Test ID</th>
                <th>Sensor Side</th>
                <th>Target (cm)</th>
                <th>Allowed Err (cm)</th>
                <th>Final Reading (cm)</th>
                <th>Error Raw (cm)</th>
                <th>Error %</th>
                <th>Accuracy %</th>
            </tr>
        </thead>
        <tbody>
            ' . $testRowsHtml . '
            <tr class="averages-row">
                <td colspan="5" style="text-align:right; padding:8px 6px;">SESSION MULTI-SENSOR AVERAGES:</td>
                <td>' . $avgRaw . '</td>
                <td>' . $avgErrPct . '</td>
                <td>' . $avgAcc . '</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Student Driver Multi-Sensor Parking Test System &mdash; Thesis Project &mdash; Confidential
    </div>
</body>
</html>';

// Generate PDF with DomPDF
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream('session_report_' . $session_id . '.pdf', ['Attachment' => true]);
