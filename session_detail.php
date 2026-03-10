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
    <link rel="stylesheet" href="theme-modern.css">
    <script>document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'dark');</script>
    <style>
        /* Classes defined in theme-modern.css: split-layout, info-panel, data-panel,
           panel-title, readonly-box, complex-table, side-Main */

        /* ── Table cell specific styles ───────────────────────── */
        .test-id-cell {
            border-right: 1px solid rgba(255, 255, 255, 0.18);
        }

        .averages-label-cell {
            color: rgba(255, 255, 255, 0.80);
        }

        .table-empty-cell {
            color: rgba(255, 255, 255, 0.60);
        }

        /* ── Light Mode Overrides ─────────────────────────────── */
        [data-theme="light"] .test-id-cell {
            border-right-color: rgba(0, 0, 0, 0.12);
        }

        [data-theme="light"] .averages-label-cell {
            color: #374151;
        }

        [data-theme="light"] .table-empty-cell {
            color: #6b7280;
        }

        [data-theme="light"] .averages-row td {
            color: #374151;
        }
    </style>
</head>
<body>

<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">
                <a href="student_detail.php?id=<?= $session['studentID'] ?>">Student Profile</a>
                › Session #<?= $session_id ?> Details
            </div>
            <div class="profile">
                <button id="themeToggle" class="theme-toggle-btn" title="Switch to Light Mode" aria-label="Toggle theme">☀️</button>
                <div style="width:34px;height:34px;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.13);border-radius:10px;display:flex;align-items:center;justify-content:center;cursor:pointer;" title="Notifications">🔔</div>
                <div style="width:34px;height:34px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:0.9em;" title="Profile">👤</div>
            </div>
        </header>

        <section class="content-section">
            <div class="split-layout">
                <!-- SESSION INFO PANEL -->
                <div class="info-panel">
                    <h3 class="panel-title">Session Overview</h3>
                    <div class="readonly-box">
                        <strong>Student</strong><br>
                        <span style="font-size:1.2em; color:#93c5fd; font-weight:700;"><?= htmlspecialchars($session['firstname'] . ' ' . $session['lastname']) ?></span>
                        <hr style="border:0; border-top:1px solid rgba(255,255,255,0.15); margin: 10px 0;">
                        
                        <strong>Status</strong><br>
                        <?php if ($session['session_status'] === 'Ongoing'): ?>
                            <span class="status-pill status-ongoing">Ongoing</span>
                        <?php else: ?>
                            <span class="status-pill status-completed">Completed</span>
                        <?php endif; ?>
                        
                        <hr style="border:0; border-top:1px solid rgba(255,255,255,0.15); margin: 10px 0;">
                        
                        <strong>Started At</strong><br>
                        <?php 
                            $ds = $session['datetime_started'];
                            echo ($ds instanceof DateTime) ? $ds->format('Y-m-d H:i:s') : $ds;
                        ?>
                        <br><br>
                        
                        <strong>Finished At</strong><br>
                        <?php 
                            $df = $session['datetime_finished'];
                            if ($df) {
                                echo ($df instanceof DateTime) ? $df->format('Y-m-d H:i:s') : $df;
                            } else {
                                echo "N/A";
                            }
                        ?>
                    </div>
                    
                    <a href="student_detail.php?id=<?= $session['studentID'] ?>" class="btn-cancel" style="display:block; text-align:center; padding:10px; margin-bottom:10px;">⬅ Back to Student</a>
                    
                    <a href="generate_pdf.php?session_id=<?= $session_id ?>" class="action-btn" style="display:block; text-align:center; margin-bottom:10px;">📄 Download PDF Report</a>
                    
                    <?php if ($session['session_status'] === 'Ongoing'): ?>
                        <a href="calibrate.php?session_id=<?= $session_id ?>" class="action-btn" style="display:block; text-align:center; background:rgba(34,197,94,0.30); border-color:rgba(34,197,94,0.60); color:#d1fae5; margin-bottom:10px;">+ Add Another Test</a>
                        <form method="POST" action="">
                            <input type="hidden" name="end_session_inner" value="1">
                            <button type="submit" class="action-btn" style="display:block; width:100%; text-align:center; background:rgba(185,28,28,0.35); border-color:rgba(239,68,68,0.60); color:#fecaca;">Finish Session</button>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- TEST HISTORY PANEL -->
                <div class="data-panel" style="overflow-x:auto;">
                    <h3 class="panel-title">Individual Sensor Tests (<?= count($tests) ?> attempts)</h3>
                    
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
                                    <tr class="sensor-group-start">
                                        <td rowspan="3" class="test-id-cell" style="font-weight:bold; font-size:1.2em; vertical-align: middle;">#<?= $t['ID'] ?></td>
                                        <td class="side-Main"><?= $t['assigned_side_1'] ?></td>
                                        <td><?= $t['calibration_distance_1'] ?></td>
                                        <td><?= $t['allowed_distance_error_1'] ?></td>
                                        <td style="font-weight:bold;"><?= $t['car_distance_from_line_1'] ?></td>
                                        <td><?= round($t['final_distance_error_raw_1'], 1) ?></td>
                                        <td><?= round($t['final_distance_error_percentage_1'], 1) ?>%</td>
                                        <td><?= round($t['final_computed_accuracy_1'], 1) ?>%</td>
                                    </tr>
                                    <!-- SENSOR 2 -->
                                    <tr>
                                        <td class="side-Main"><?= $t['assigned_side_2'] ?></td>
                                        <td><?= $t['calibration_distance_2'] ?></td>
                                        <td><?= $t['allowed_distance_error_2'] ?></td>
                                        <td style="font-weight:bold;"><?= $t['car_distance_from_line_2'] ?></td>
                                        <td><?= round($t['final_distance_error_raw_2'], 1) ?></td>
                                        <td><?= round($t['final_distance_error_percentage_2'], 1) ?>%</td>
                                        <td><?= round($t['final_computed_accuracy_2'], 1) ?>%</td>
                                    </tr>
                                    <!-- SENSOR 3 -->
                                    <tr>
                                        <td class="side-Main"><?= $t['assigned_side_3'] ?></td>
                                        <td><?= $t['calibration_distance_3'] ?></td>
                                        <td><?= $t['allowed_distance_error_3'] ?></td>
                                        <td style="font-weight:bold;"><?= $t['car_distance_from_line_3'] ?></td>
                                        <td><?= round($t['final_distance_error_raw_3'], 1) ?></td>
                                        <td><?= round($t['final_distance_error_percentage_3'], 1) ?>%</td>
                                        <td><?= round($t['final_computed_accuracy_3'], 1) ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <tr class="averages-row">
                                    <td colspan="5" class="averages-label-cell" style="text-align:right;">SESSION MULTI-SENSOR AVERAGES:</td>
                                    <td><?= $session['avg_distance_error_raw'] !== null ? round($session['avg_distance_error_raw'], 1) . ' cm' : 'N/A' ?></td>
                                    <td><?= $session['avg_distance_error_percentage'] !== null ? round($session['avg_distance_error_percentage'], 1) . '%' : 'N/A' ?></td>
                                    <td><?= $session['avg_computed_accuracy'] !== null ? round($session['avg_computed_accuracy'], 1) . '%' : 'N/A' ?></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="table-empty-cell" style="padding: 30px;">No sensor data logged in this session yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </section>
    </main>
</div>

<script>
/* Theme toggle */
(function () {
    const html = document.documentElement;
    const saved = localStorage.getItem('theme') || 'dark';
    html.setAttribute('data-theme', saved);
    const btn = document.getElementById('themeToggle');
    if (btn) {
        updateThemeIcon(btn, saved);
        btn.addEventListener('click', function () {
            const cur = html.getAttribute('data-theme');
            const next = cur === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            updateThemeIcon(btn, next);
        });
    }
    function updateThemeIcon(btn, theme) {
        btn.textContent = theme === 'dark' ? '☀️' : '🌙';
        btn.title = theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';
    }
})();
</script>

</body>
</html>
