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
$sessions_sql = "SELECT * FROM Sessions WHERE studentID = ? ORDER BY ID DESC";
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
    <style>
        .split-layout { display: flex; gap: 20px; flex-wrap: wrap; }
        .edit-panel { flex: 1; min-width: 300px; background: rgba(255,255,255,0.07); backdrop-filter: blur(24px) saturate(180%); -webkit-backdrop-filter: blur(24px) saturate(180%); border: 1.5px solid rgba(255,255,255,0.15); padding: 24px; border-radius: 24px; box-shadow: 0 8px 32px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.12); }
        .data-panel { flex: 2; min-width: 500px; background: rgba(255,255,255,0.07); backdrop-filter: blur(24px) saturate(180%); -webkit-backdrop-filter: blur(24px) saturate(180%); border: 1.5px solid rgba(255,255,255,0.15); padding: 24px; border-radius: 24px; box-shadow: 0 8px 32px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.12); }
        .readonly-box { background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.22); padding: 12px 16px; border-radius: 14px; color: rgba(255,255,255,0.82); margin-bottom:15px; }
        
        .grade-pass { background: rgba(16,185,129,0.20); border: 1px solid rgba(16,185,129,0.50); color: #d1fae5; padding: 14px; border-radius: 14px; text-align:center; font-size: 1.2em; font-weight:bold; }
        .grade-fail { background: rgba(239,68,68,0.20); border: 1px solid rgba(239,68,68,0.50); color: #fee2e2; padding: 14px; border-radius: 14px; text-align:center; font-size: 1.2em; font-weight:bold; }
        
        .complex-table { width: 100%; border-collapse: collapse; font-size: 0.85em; }
        .complex-table th, .complex-table td { padding: 10px 12px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.12); color: rgba(255,255,255,0.90); }
        .complex-table th { background: rgba(255,255,255,0.12); color: rgba(255,255,255,0.95); font-weight: 700; font-size: 0.82em; text-transform: uppercase; letter-spacing: 0.04em; }
        .side-Main { color: #93c5fd; font-weight: bold; }
        .panel-title { margin-top:0; color:#fff; border-bottom: 1px solid rgba(255,255,255,0.18); padding-bottom: 12px; margin-bottom: 16px; font-weight:700; }
    </style>
</head>
<body>

<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">
                <a href="students.php" style="text-decoration:none; color:inherit;">Students</a> › Grading Panel
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
                            <a href="calibrate.php?student_id=<?= $student['ID'] ?>" class="action-btn" style="background: #22c55e;">+ Start New Session</a>
                        </div>
                    <?php endif; ?>

                    <table class="complex-table">
                        <thead>
                            <tr>
                                <th>Session ID</th>
                                <th>Date/Time</th>
                                <th>Status</th>
                                <th>Avg Error<br>(cm)</th>
                                <th>Avg Error<br>%</th>
                                <th>Avg Accuracy<br>%</th>
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
                                        <td>
                                            <a href="session_detail.php?id=<?= $ses['ID'] ?>" class="action-btn" style="padding:6px 14px; font-size:0.88em;">View Tests</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">No testing sessions logged yet. Click "+ Start New Session" to begin.</td>
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
