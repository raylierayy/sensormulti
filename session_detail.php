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

        /* Glass-morphism panels */
        .info-panel, .data-panel {
            background: rgba(255,255,255,0.14);
            backdrop-filter: blur(18px) saturate(140%);
            -webkit-backdrop-filter: blur(18px) saturate(140%);
            border: 1px solid rgba(255,255,255,0.28);
            box-shadow: 0 18px 55px rgba(0,0,0,0.22), inset 0 1px 0 rgba(255,255,255,0.20);
            border-radius: 24px;
            padding: 24px;
        }
        .info-panel { flex: 1; min-width: 300px; }
        .data-panel { flex: 2; min-width: 500px; }

        .readonly-box { background: rgba(243,244,246,0.5); border: 1px solid rgba(229,231,235,0.6); padding: 15px; border-radius: 14px; color: #4b5563; margin-bottom:15px; }

        /* Glass pill status badges */
        .badge-ongoing {
            display: inline-block;
            background: rgba(254,240,138,0.55);
            color: #854d0e;
            padding: 5px 16px;
            border-radius: 999px;
            font-weight: bold;
            border: 1px solid rgba(234,179,8,0.4);
            backdrop-filter: blur(6px);
            font-size: 0.92em;
        }
        .badge-completed {
            display: inline-block;
            background: rgba(187,247,208,0.55);
            color: #166534;
            padding: 5px 16px;
            border-radius: 999px;
            font-weight: bold;
            border: 1px solid rgba(34,197,94,0.4);
            backdrop-filter: blur(6px);
            font-size: 0.92em;
        }

        /* Glass pill buttons */
        .btn-glass {
            display: block;
            text-align: center;
            padding: 11px 20px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.95em;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            margin-bottom: 10px;
        }
        .btn-glass-back {
            background: rgba(30,58,138,0.12);
            color: #1e3a8a;
            border: 1px solid rgba(30,58,138,0.25);
        }
        .btn-glass-back:hover { background: rgba(30,58,138,0.22); }
        .btn-glass-add {
            background: rgba(34,197,94,0.15);
            color: #166534;
            border: 1px solid rgba(34,197,94,0.35);
        }
        .btn-glass-add:hover { background: rgba(34,197,94,0.28); }
        .btn-glass-danger {
            background: rgba(185,28,28,0.12);
            color: #b91c1c;
            border: 1px solid rgba(185,28,28,0.28);
            width: 100%;
        }
        .btn-glass-danger:hover { background: rgba(185,28,28,0.22); }
        .btn-glass-pdf {
            background: rgba(59,130,246,0.13);
            color: #1d4ed8;
            border: 1px solid rgba(59,130,246,0.3);
        }
        .btn-glass-pdf:hover { background: rgba(59,130,246,0.24); }

        .complex-table { width: 100%; border-collapse: collapse; font-size: 0.85em; }
        .complex-table th, .complex-table td { padding: 10px; text-align: center; border-bottom: 1px solid rgba(238,238,238,0.8); }
        .complex-table th { background: rgba(248,251,255,0.7); color: #1e3a8a; }
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
                            <span class="badge-ongoing">Ongoing</span>
                        <?php else: ?>
                            <span class="badge-completed">Completed</span>
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
                    
                    <a href="student_detail.php?id=<?= $session['studentID'] ?>" class="btn-glass btn-glass-back">⬅ Back to Student</a>
                    
                    <a href="generate_pdf.php?session_id=<?= $session_id ?>" class="btn-glass btn-glass-pdf">📄 Download PDF Report</a>
                    
                    <?php if ($session['session_status'] === 'Ongoing'): ?>
                        <a href="calibrate.php?session_id=<?= $session_id ?>" class="btn-glass btn-glass-add">+ Add Another Test</a>
                        <form method="POST" action="">
                            <input type="hidden" name="end_session_inner" value="1">
                            <button type="submit" class="btn-glass btn-glass-danger">Finish Session</button>
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
