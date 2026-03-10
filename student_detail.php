<?php
require 'session_check.php';
require 'db_connection.php';

$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = "";
$error = "";

// Handle form submission to edit student & finalize grade
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_student'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $instructor = $_POST['instructor'];
    $remarks = $_POST['remarks'];
    
    // Check if grading was triggered
    $pass_fail = isset($_POST['pass_fail']) ? $_POST['pass_fail'] : null;
    
    // If they changed the grade from Pending to Pass/Fail, we set datetime_finished
    // Let's pull current status first
    $current_sql = "SELECT pass_fail FROM Students WHERE ID = ?";
    $curr_result = sqlsrv_query($conn, $current_sql, [$student_id]);
    $curr_data = sqlsrv_fetch_array($curr_result, SQLSRV_FETCH_ASSOC);
    $curr_status = $curr_data['pass_fail'];

    if (!empty($pass_fail) && empty($curr_status)) {
        // Just graded
        $sql = "UPDATE Students SET firstname=?, lastname=?, instructor=?, remarks=?, pass_fail=?, datetime_finished=GETDATE() WHERE ID=?";
        $params = [$firstname, $lastname, $instructor, $remarks, $pass_fail, $student_id];
    } else {
        // Just a normal update (or it was already graded)
        $sql = "UPDATE Students SET firstname=?, lastname=?, instructor=?, remarks=?, pass_fail=? WHERE ID=?";
        $params = [$firstname, $lastname, $instructor, $remarks, $pass_fail, $student_id];
    }

    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt) {
        $message = "Student record updated applied!";
    } else {
        $error = "Error updating: " . print_r(sqlsrv_errors(), true);
    }
}

// Handle finishing a session from test_driver.php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['end_session'])) {
    $end_session = intval($_POST['end_session']);
    $end_sql = "UPDATE Sessions SET session_status = 'Completed', datetime_finished = GETDATE() WHERE ID = ?";
    sqlsrv_query($conn, $end_sql, [$end_session]);
    $message = "Testing session completed successfully!";
}

// Fetch student
$sql = "SELECT * FROM Students WHERE ID = ?";
$result = sqlsrv_query($conn, $sql, [$student_id]);
if ($result === false || !sqlsrv_has_rows($result)) {
    die("Student not found. <a href='students.php'>Back</a>");
}
$student = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
$is_finished = !empty($student['pass_fail']);

// Fetch Sessions for this student
$sessions_sql = "SELECT s.*, 
                 (SELECT COUNT(ID) FROM Sensors WHERE sessionID = s.ID) AS tests_count
                 FROM Sessions s WHERE studentID = ? ORDER BY s.ID DESC";
$sessions_result = sqlsrv_query($conn, $sessions_sql, [$student_id]);
$sessions = [];
if ($sessions_result !== false && sqlsrv_has_rows($sessions_result)) {
    while($s = sqlsrv_fetch_array($sessions_result, SQLSRV_FETCH_ASSOC)) {
        $sessions[] = $s;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Grading - Sensor System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="theme-modern.css">
    <script>document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'dark');</script>
    <style>
        /* Classes defined in theme-modern.css: split-layout, info-panel, edit-panel,
           data-panel, panel-title, readonly-box, grade-pass, grade-fail,
           complex-table, side-Main */

        /* Sessions table enhancements */
        .sessions-table th, .sessions-table td {
            text-align: center;
        }
        .sessions-table td:first-child { font-weight: 700; color: #a5b4fc; }

        /* Start session btn variant */
        .btn-start-session {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 20px;
            background: linear-gradient(135deg, #10b981, #06b6d4);
            color: #fff !important;
            border-radius: 11px;
            font-size: 0.85em;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(16,185,129,0.40);
        }

        .btn-start-session:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(16,185,129,0.60);
        }

        /* ── Light Mode Overrides ─────────────────────────────── */
        [data-theme="light"] .sessions-table td:first-child {
            color: #6366f1;
        }
        /* All form labels inside the edit panel */
        [data-theme="light"] .edit-panel .form-group label {
            color: #374151 !important;
        }
        /* N/A placeholder spans in session table (no class = not a status pill) */
        [data-theme="light"] .sessions-table span:not([class]) {
            color: #9ca3af !important;
        }
    </style>
</head>
<body>

<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">
                <a href="students.php">Students</a> › Grading Panel
            </div>
            <div class="profile">
                <button id="themeToggle" class="theme-toggle-btn" title="Switch to Light Mode" aria-label="Toggle theme">☀️</button>
                <div style="width:34px;height:34px;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.13);border-radius:10px;display:flex;align-items:center;justify-content:center;cursor:pointer;" title="Notifications">🔔</div>
                <div style="width:34px;height:34px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:0.9em;" title="Profile">👤</div>
            </div>
        </header>

        <section class="content-section">
            <?php if ($message): ?>
                <div class="msg-success">✅ <?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="msg-error">❌ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="split-layout">
                <!-- EDIT STUDENT PANEL -->
                <div class="edit-panel">
                    <h3 class="panel-title">Student Profile & Grading</h3>
                    
                    <?php if ($is_finished): ?>
                        <?php if (strtolower($student['pass_fail']) == 'pass'): ?>
                            <div class="grade-pass" style="margin-bottom:20px;">🎉 PASSED</div>
                        <?php else: ?>
                            <div class="grade-fail" style="margin-bottom:20px;">❌ FAILED</div>
                        <?php endif; ?>
                        <div class="readonly-box">
                            <strong>Date Finished:</strong><br>
                            <?php 
                                $df = $student['datetime_finished'];
                                echo ($df instanceof DateTime) ? $df->format('Y-m-d H:i:s') : $df;
                            ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <input type="hidden" name="update_student" value="1">
                        
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="firstname" value="<?php echo htmlspecialchars($student['firstname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="lastname" value="<?php echo htmlspecialchars($student['lastname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Instructor</label>
                            <input type="text" name="instructor" value="<?php echo htmlspecialchars($student['instructor']); ?>" required>
                        </div>
                        
                        <!-- Grading section -->
                        <div class="form-group" style="background:rgba(255,255,255,0.10); padding:15px; border-radius:14px; border:1px solid rgba(255,255,255,0.20); margin-top:20px;">
                            <label style="color:rgba(255,255,255,0.95);"><strong>Final Grade Decision</strong></label>
                            <select name="pass_fail" style="width:100%; padding:10px; margin-bottom:10px;">
                                <option value="" <?php if(empty($student['pass_fail'])) echo 'selected'; ?>>[Pending / Still Testing]</option>
                                <option value="Pass" <?php if(strcasecmp($student['pass_fail'], 'Pass')==0) echo 'selected'; ?>>Pass</option>
                                <option value="Fail" <?php if(strcasecmp($student['pass_fail'], 'Fail')==0) echo 'selected'; ?>>Fail</option>
                            </select>
                            
                            <label>Final Remarks (Optional)</label>
                            <textarea name="remarks" rows="4" style="width:100%; padding:10px; border:1px solid rgba(255,255,255,0.45); border-radius:14px; background:rgba(255,255,255,0.88); color:#1e293b;"><?php echo htmlspecialchars($student['remarks']); ?></textarea>
                        </div>
                        
                        <button type="submit" class="action-btn" style="width:100%;">Save Changes</button>
                    </form>
                </div>

                <!-- SESSION HISTORY PANEL -->
                <div class="data-panel" style="overflow-x:auto;">
                    <h3 class="panel-title">Testing Sessions History (<?= count($sessions) ?> rows)</h3>
                    
                    <?php if (!$is_finished): ?>
                        <div style="margin-bottom: 15px;">
                            <a href="calibrate.php?student_id=<?= $student['ID'] ?>" class="btn-start-session" aria-label="Start new testing session">
                                ＋ Start New Session
                            </a>
                        </div>
                    <?php endif; ?>

                    <table class="complex-table sessions-table">
                        <thead>
                            <tr>
                                <th>Session ID</th>
                                <th>Date/Time</th>
                                <th>Status</th>
                                <th>Avg Error<br>(cm)</th>
                                <th>Avg Error<br>%</th>
                                <th>Avg Accuracy<br>%</th>
                                <th>Tests Logged</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($sessions) > 0): ?>
                                <?php foreach($sessions as $ses): 
                                    $ds = $ses['datetime_started'];
                                    $dstr = ($ds instanceof DateTime) ? $ds->format('Y-m-d H:i') : $ds;
                                ?>
                                    <tr>
                                        <td style="font-weight:bold; color:#1e3a8a;">#<?= $ses['ID'] ?></td>
                                        <td><?= $dstr ?></td>
                                        <td>
                                            <?php if ($ses['session_status'] === 'Ongoing'): ?>
                                                <span class="status-pill status-ongoing">Ongoing</span>
                                            <?php else: ?>
                                                <span class="status-pill status-completed">Completed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($ses['avg_distance_error_raw'] !== null): ?>
                                                <?= round($ses['avg_distance_error_raw'], 1) ?> cm
                                            <?php else: ?>
                                                <span style="color:rgba(255,255,255,0.45);">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($ses['avg_distance_error_percentage'] !== null): ?>
                                                <?= round($ses['avg_distance_error_percentage'], 1) ?>%
                                            <?php else: ?>
                                                <span style="color:rgba(255,255,255,0.45);">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="font-weight:bold;">
                                            <?php if ($ses['avg_computed_accuracy'] !== null): ?>
                                                <?= round($ses['avg_computed_accuracy'], 1) ?>%
                                            <?php else: ?>
                                                <span style="color:rgba(255,255,255,0.45);">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="font-weight:bold;">
                                            <?= $ses['tests_count'] ?> attempts
                                        </td>
                                        <td>
                                            <a href="session_detail.php?id=<?= $ses['ID'] ?>" class="action-btn" style="padding:6px 14px; font-size:0.88em;">View Tests</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8">No testing sessions logged yet. Click "+ Start New Session" to begin.</td>
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
