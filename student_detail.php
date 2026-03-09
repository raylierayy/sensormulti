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
    <style>
        .split-layout { display: flex; gap: 20px; flex-wrap: wrap; }
        .edit-panel { flex: 1; min-width: 300px; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; }
        .data-panel { flex: 2; min-width: 500px; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; }
        .readonly-box { background: #f3f4f6; border: 1px solid #e5e7eb; padding: 10px; border-radius: 5px; color: #4b5563; margin-bottom:15px; }
        
        .grade-pass { background: rgba(34,197,94,0.1); border: 2px solid #22c55e; color: #166534; padding: 15px; border-radius: 8px; text-align:center; font-size: 1.2em; font-weight:bold; }
        .grade-fail { background: rgba(239,68,68,0.1); border: 2px solid #ef4444; color: #991b1b; padding: 15px; border-radius: 8px; text-align:center; font-size: 1.2em; font-weight:bold; }
        
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
                <a href="students.php" style="text-decoration:none; color:inherit;">Students</a> › Grading Panel
            </div>
        </header>

        <section class="content-section">
            <?php if ($message): ?>
                <div style="color: green; margin-bottom: 15px; background: #f0fdf4; padding: 10px; border-left: 4px solid #22c55e;">✅ <?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div style="color: red; margin-bottom: 15px; background: #fef2f2; padding: 10px; border-left: 4px solid #ef4444;">❌ <?php echo $error; ?></div>
            <?php endif; ?>

            <div class="split-layout">
                <!-- EDIT STUDENT PANEL -->
                <div class="edit-panel">
                    <h3 style="margin-top:0; color:#1e3a8a; border-bottom: 2px solid #eee; padding-bottom: 10px;">Student Profile & Grading</h3>
                    
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
                        <div class="form-group" style="background:#f8fbff; padding:15px; border-radius:5px; border:1px solid #bfdbfe; margin-top:20px;">
                            <label style="color:#1e3a8a;"><strong>Final Grade Decision</strong></label>
                            <select name="pass_fail" style="width:100%; padding:10px; margin-bottom:10px;">
                                <option value="" <?php if(empty($student['pass_fail'])) echo 'selected'; ?>>[Pending / Still Testing]</option>
                                <option value="Pass" <?php if(strcasecmp($student['pass_fail'], 'Pass')==0) echo 'selected'; ?>>Pass</option>
                                <option value="Fail" <?php if(strcasecmp($student['pass_fail'], 'Fail')==0) echo 'selected'; ?>>Fail</option>
                            </select>
                            
                            <label>Final Remarks (Optional)</label>
                            <textarea name="remarks" rows="4" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;"><?php echo htmlspecialchars($student['remarks']); ?></textarea>
                        </div>
                        
                        <button type="submit" class="action-btn" style="width:100%;">Save Changes</button>
                    </form>
                </div>

                <!-- SESSION HISTORY PANEL -->
                <div class="data-panel" style="overflow-x:auto;">
                    <h3 style="margin-top:0; color:#1e3a8a; border-bottom: 2px solid #eee; padding-bottom: 10px;">Testing Sessions History (<?= count($sessions) ?> rows)</h3>
                    
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
                                                <span style="background:#fef08a; color:#854d0e; padding:4px 8px; border-radius:4px; font-weight:bold;">Ongoing</span>
                                            <?php else: ?>
                                                <span style="background:#bbf7d0; color:#166534; padding:4px 8px; border-radius:4px; font-weight:bold;">Completed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($ses['avg_distance_error_raw'] !== null): ?>
                                                <?= round($ses['avg_distance_error_raw'], 1) ?> cm
                                            <?php else: ?>
                                                <span style="color:#9ca3af;">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($ses['avg_distance_error_percentage'] !== null): ?>
                                                <?= round($ses['avg_distance_error_percentage'], 1) ?>%
                                            <?php else: ?>
                                                <span style="color:#9ca3af;">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="font-weight:bold;">
                                            <?php if ($ses['avg_computed_accuracy'] !== null): ?>
                                                <?= round($ses['avg_computed_accuracy'], 1) ?>%
                                            <?php else: ?>
                                                <span style="color:#9ca3af;">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="session_detail.php?id=<?= $ses['ID'] ?>" class="action-btn" style="background:#3b82f6; padding:6px 12px; font-size:0.9em;">View Tests</a>
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
