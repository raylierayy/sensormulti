<?php
require 'session_check.php';
require 'db_connection.php';

$session_id = isset($_GET['session_id']) ? intval($_GET['session_id']) : 0;

if ($session_id <= 0) {
    header("Location: students.php");
    exit;
}

// Fetch session + student data
$sql = "SELECT s.*, st.firstname, st.lastname FROM Sessions s 
        JOIN Students st ON s.studentID = st.ID WHERE s.ID = ?";
$result = sqlsrv_query($conn, $sql, [$session_id]);
if ($result === false || !sqlsrv_has_rows($result)) {
    die("Session not found. <a href='students.php'>Back</a>");
}
$session = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

// Fetch sensor tests
$tests_sql = "SELECT * FROM Sensors WHERE sessionID = ? ORDER BY ID ASC";
$tests_result = sqlsrv_query($conn, $tests_sql, [$session_id]);
$tests = [];
while ($t = sqlsrv_fetch_array($tests_result, SQLSRV_FETCH_ASSOC)) {
    $tests[] = $t;
}

$student_name = htmlspecialchars($session['firstname'] . ' ' . $session['lastname']);
$started = ($session['datetime_started'] instanceof DateTime) 
    ? $session['datetime_started']->format('Y-m-d H:i:s') 
    : $session['datetime_started'];
$finished = $session['datetime_finished']
    ? (($session['datetime_finished'] instanceof DateTime) 
        ? $session['datetime_finished']->format('Y-m-d H:i:s')
        : $session['datetime_finished'])
    : 'N/A';

// ── If DomPDF is available, use it; otherwise output inline HTML ──────
$vendor_autoload = __DIR__ . '/vendor/autoload.php';

$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #1e293b; }
        h1 { color: #1e3a8a; margin-bottom: 4px; }
        h2 { color: #1e3a8a; margin-top: 24px; }
        .meta { margin-bottom: 18px; }
        .meta p { margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #1e3a8a; color: #fff; padding: 8px 10px; text-align: center; font-size: 12px; }
        td { padding: 7px 10px; text-align: center; border-bottom: 1px solid #e2e8f0; }
        tr:nth-child(even) td { background: #f8fafc; }
        .avg-row td { background: #eff6ff; font-weight: bold; border-top: 2px solid #60a5fa; }
        .footer { margin-top: 30px; font-size: 11px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <h1>Parking Test — Session Report #' . $session_id . '</h1>
    <div class="meta">
        <p><strong>Student:</strong> ' . $student_name . '</p>
        <p><strong>Status:</strong> ' . htmlspecialchars($session['session_status']) . '</p>
        <p><strong>Started:</strong> ' . $started . '</p>
        <p><strong>Finished:</strong> ' . $finished . '</p>
    </div>

    <h2>Sensor Test Results</h2>
    <table>
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
        <tbody>';

if (count($tests) > 0) {
    foreach ($tests as $t) {
        // Sensor 1
        $html .= '<tr>
            <td rowspan="3" style="vertical-align:middle; font-weight:bold;">#' . $t['ID'] . '</td>
            <td>' . htmlspecialchars($t['assigned_side_1']) . '</td>
            <td>' . $t['calibration_distance_1'] . '</td>
            <td>' . $t['allowed_distance_error_1'] . '</td>
            <td><strong>' . $t['car_distance_from_line_1'] . '</strong></td>
            <td>' . round($t['final_distance_error_raw_1'], 1) . '</td>
            <td>' . round($t['final_distance_error_percentage_1'], 1) . '%</td>
            <td>' . round($t['final_computed_accuracy_1'], 1) . '%</td>
        </tr>';
        // Sensor 2
        $html .= '<tr>
            <td>' . htmlspecialchars($t['assigned_side_2']) . '</td>
            <td>' . $t['calibration_distance_2'] . '</td>
            <td>' . $t['allowed_distance_error_2'] . '</td>
            <td><strong>' . $t['car_distance_from_line_2'] . '</strong></td>
            <td>' . round($t['final_distance_error_raw_2'], 1) . '</td>
            <td>' . round($t['final_distance_error_percentage_2'], 1) . '%</td>
            <td>' . round($t['final_computed_accuracy_2'], 1) . '%</td>
        </tr>';
        // Sensor 3
        $html .= '<tr>
            <td>' . htmlspecialchars($t['assigned_side_3']) . '</td>
            <td>' . $t['calibration_distance_3'] . '</td>
            <td>' . $t['allowed_distance_error_3'] . '</td>
            <td><strong>' . $t['car_distance_from_line_3'] . '</strong></td>
            <td>' . round($t['final_distance_error_raw_3'], 1) . '</td>
            <td>' . round($t['final_distance_error_percentage_3'], 1) . '%</td>
            <td>' . round($t['final_computed_accuracy_3'], 1) . '%</td>
        </tr>';
    }

    $avg_err_raw  = $session['avg_distance_error_raw'] !== null ? round($session['avg_distance_error_raw'], 1) . ' cm' : 'N/A';
    $avg_err_perc = $session['avg_distance_error_percentage'] !== null ? round($session['avg_distance_error_percentage'], 1) . '%' : 'N/A';
    $avg_acc      = $session['avg_computed_accuracy'] !== null ? round($session['avg_computed_accuracy'], 1) . '%' : 'N/A';

    $html .= '<tr class="avg-row">
        <td colspan="5" style="text-align:right;">SESSION MULTI-SENSOR AVERAGES:</td>
        <td>' . $avg_err_raw . '</td>
        <td>' . $avg_err_perc . '</td>
        <td>' . $avg_acc . '</td>
    </tr>';
} else {
    $html .= '<tr><td colspan="8" style="padding:20px;">No sensor data recorded in this session.</td></tr>';
}

$html .= '
        </tbody>
    </table>
    <div class="footer">Generated by Parking Aid Multi-Sensor System — ' . date('Y-m-d H:i:s') . '</div>
</body>
</html>';

if (file_exists($vendor_autoload)) {
    require $vendor_autoload;
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("Session_{$session_id}_Report.pdf", ["Attachment" => true]);
} else {
    // DomPDF not installed — render the report as a printable HTML page
    echo $html;
}
?>
