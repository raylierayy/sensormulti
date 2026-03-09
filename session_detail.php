<?php
require 'session_check.php';
require 'db_connection.php';

$session_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch Session
$sql = "SELECT s.*, st.firstname, st.lastname FROM Sessions s JOIN Students st ON s.studentID = st.ID WHERE s.ID = ?";
$result = sqlsrv_query($conn, $sql, [$session_id]);
if ($result === false || !sqlsrv_has_rows($result)) {
    die("Session not found. <a href='students.php'>Back</a>");
}
$session = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

// Handle finishing a session from within this page
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['end_session_inner'])) {
    $end_sql = "UPDATE Sessions SET session_status = 'Completed', datetime_finished = GETDATE() WHERE ID = ?";
    sqlsrv_query($conn, $end_sql, [$session_id]);
    
    // Refresh the status
    $session['session_status'] = 'Completed';
    $session['datetime_finished'] = new DateTime(); // Approximation for immediate UI update
}

// Fetch tests (Sensors)
$tests_sql = "SELECT * FROM Sensors WHERE sessionID = ? ORDER BY ID ASC";
$tests_result = sqlsrv_query($conn, $tests_sql, [$session_id]);
$tests = [];
if ($tests_result !== false && sqlsrv_has_rows($tests_result)) {
    while($t = sqlsrv_fetch_array($tests_result, SQLSRV_FETCH_ASSOC)) {
        $tests[] = $t;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Details - Sensor System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .split-layout { display: flex; gap: 20px; flex-wrap: wrap; }
        .info-panel { flex: 1; min-width: 300px; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; }
        .data-panel { flex: 2; min-width: 500px; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; }
        .readonly-box { background: #f3f4f6; border: 1px solid #e5e7eb; padding: 15px; border-radius: 5px; color: #4b5563; margin-bottom:15px; }
        
        .complex-table { width: 100%; border-collapse: collapse; font-size: 0.85em; }
        .complex-table th, .complex-table td { padding: 10px; text-align: center; border-bottom: 1px solid #eee; }
        .complex-table th { background: #f8fbff; color: #1e3a8a; }
        .side-Main { color: #3b82f6; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">
                <a href="student_detail.php?id=<?= $session['studentID'] ?>" style="text-decoration:none; color:inherit;">Student Profile</a> › Session #<?= $session_id ?> Details
            </div>
        </header>

        <section class="content-section">
            <div class="split-layout">
                <!-- SESSION INFO PANEL -->
                <div class="info-panel">
                    <h3 style="margin-top:0; color:#1e3a8a; border-bottom: 2px solid #eee; padding-bottom: 10px;">Session Overview</h3>
                    <div class="readonly-box">
                        <strong>Student:</strong><br>
                        <span style="font-size:1.2em; color:#1e3a8a;"><?= htmlspecialchars($session['firstname'] . ' ' . $session['lastname']) ?></span>
                        <hr style="border:0; border-top:1px solid #ddd; margin: 10px 0;">
                        
                        <strong>Status:</strong><br>
                        <?php if ($session['session_status'] === 'Ongoing'): ?>
                            <span style="background:#fef08a; color:#854d0e; padding:4px 8px; border-radius:4px; font-weight:bold;">Ongoing</span>
                        <?php else: ?>
                            <span style="background:#bbf7d0; color:#166534; padding:4px 8px; border-radius:4px; font-weight:bold;">Completed</span>
                        <?php endif; ?>
                        
                        <hr style="border:0; border-top:1px solid #ddd; margin: 10px 0;">
                        
                        <strong>Started At:</strong><br>
                        <?php 
                            $ds = $session['datetime_started'];
                            echo ($ds instanceof DateTime) ? $ds->format('Y-m-d H:i:s') : $ds;
                        ?>
                        <br><br>
                        
                        <strong>Finished At:</strong><br>
                        <?php 
                            $df = $session['datetime_finished'];
                            if ($df) {
                                echo ($df instanceof DateTime) ? $df->format('Y-m-d H:i:s') : $df;
                            } else {
                                echo "N/A";
                            }
                        ?>
                    </div>
                    
                    <a href="student_detail.php?id=<?= $session['studentID'] ?>" class="btn-cancel" style="display:block; text-align:center; padding:10px;">⬅ Back to Student</a>
                    
                    <?php if ($session['session_status'] === 'Ongoing'): ?>
                        <br>
                        <a href="calibrate.php?session_id=<?= $session_id ?>" class="action-btn" style="display:block; text-align:center; background:#22c55e; margin-bottom:10px;">+ Add Another Test</a>
                        <form method="POST" action="">
                            <input type="hidden" name="end_session_inner" value="1">
                            <button type="submit" class="action-btn" style="display:block; width:100%; text-align:center; background:#b91c1c;">Finish Session</button>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- TEST HISTORY PANEL -->
                <div class="data-panel" style="overflow-x:auto;">
                    <h3 style="margin-top:0; color:#1e3a8a; border-bottom: 2px solid #eee; padding-bottom: 10px;">Individual Sensor Tests (<?= count($tests) ?> attempts)</h3>
                    
                    <table class="complex-table">
                        <thead>
                            <tr>
                                <th>Test ID</th>
                                <th>Sensor<br>Side</th>
                                <th>Target<br>(cm)</th>
                                <th>Allowed<br>Err (cm)</th>
                                <th>Final<br>Reading (cm)</th>
                                <th>Error<br>Raw (cm)</th>
                                <th>Error %</th>
                                <th style="border-right: 2px solid #cbd5e1;">Accuracy %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($tests) > 0): ?>
                                <?php foreach($tests as $t): ?>
                                    <!-- SENSOR 1 -->
                                    <tr style="border-top: 2px solid #cbd5e1; background: #f8fafc;">
                                        <td rowspan="3" style="font-weight:bold; font-size:1.2em; border-right: 2px solid #cbd5e1; background:#fff; vertical-align: middle;">#<?= $t['ID'] ?></td>
                                        <td style="font-weight:bold; color:#1d4ed8;"><?= $t['assigned_side_1'] ?></td>
                                        <td><?= $t['calibration_distance_1'] ?></td>
                                        <td><?= $t['allowed_distance_error_1'] ?></td>
                                        <td style="font-weight:bold;"><?= $t['car_distance_from_line_1'] ?></td>
                                        <td><?= round($t['final_distance_error_raw_1'], 1) ?></td>
                                        <td><?= round($t['final_distance_error_percentage_1'], 1) ?>%</td>
                                        <td style="border-right: 2px solid #cbd5e1;"><?= round($t['final_computed_accuracy_1'], 1) ?>%</td>
                                    </tr>
                                    <!-- SENSOR 2 -->
                                    <tr style="background: #f8fafc;">
                                        <td style="font-weight:bold; color:#1d4ed8;"><?= $t['assigned_side_2'] ?></td>
                                        <td><?= $t['calibration_distance_2'] ?></td>
                                        <td><?= $t['allowed_distance_error_2'] ?></td>
                                        <td style="font-weight:bold;"><?= $t['car_distance_from_line_2'] ?></td>
                                        <td><?= round($t['final_distance_error_raw_2'], 1) ?></td>
                                        <td><?= round($t['final_distance_error_percentage_2'], 1) ?>%</td>
                                        <td style="border-right: 2px solid #cbd5e1;"><?= round($t['final_computed_accuracy_2'], 1) ?>%</td>
                                    </tr>
                                    <!-- SENSOR 3 -->
                                    <tr style="background: #f8fafc;">
                                        <td style="font-weight:bold; color:#1d4ed8;"><?= $t['assigned_side_3'] ?></td>
                                        <td><?= $t['calibration_distance_3'] ?></td>
                                        <td><?= $t['allowed_distance_error_3'] ?></td>
                                        <td style="font-weight:bold;"><?= $t['car_distance_from_line_3'] ?></td>
                                        <td><?= round($t['final_distance_error_raw_3'], 1) ?></td>
                                        <td><?= round($t['final_distance_error_percentage_3'], 1) ?>%</td>
                                        <td style="border-right: 2px solid #cbd5e1;"><?= round($t['final_computed_accuracy_3'], 1) ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <tr style="background:#eff6ff; font-weight:bold; border-top: 3px solid #60a5fa;">
                                    <td colspan="5" style="text-align:right;">SESSION MULTI-SENSOR AVERAGES:</td>
                                    <td><?= $session['avg_distance_error_raw'] !== null ? round($session['avg_distance_error_raw'], 1) . ' cm' : 'N/A' ?></td>
                                    <td><?= $session['avg_distance_error_percentage'] !== null ? round($session['avg_distance_error_percentage'], 1) . '%' : 'N/A' ?></td>
                                    <td><?= $session['avg_computed_accuracy'] !== null ? round($session['avg_computed_accuracy'], 1) . '%' : 'N/A' ?></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="padding: 30px;">No sensor data logged in this session yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </section>
    </main>
</div>

</body>
</html>
