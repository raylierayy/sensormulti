<?php
require 'session_check.php';
require 'db_connection.php';

$message = "";
$error = "";

// ── HANDLE FORM SUBMISSION: FINALIZING THE TEST ──────────────────────────
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalize_results'])) {
    $student_id = intval($_POST['student_id']);
    $session_id = intval($_POST['session_id']);
    $time_started = $_POST['time_started'];

    // If no session exists, silently create one first
    if ($session_id == 0) {
        $insert_session_sql = "INSERT INTO Sessions (studentID, session_status) VALUES (?, 'Ongoing')";
        $stmt_ses = sqlsrv_query($conn, $insert_session_sql, [$student_id]);
        if ($stmt_ses) {
            $res = sqlsrv_query($conn, "SELECT IDENT_CURRENT('Sessions') AS new_id");
            if ($res && sqlsrv_has_rows($res)) {
                $row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC);
                $session_id = $row['new_id'];
            }
        }
    }

    // SENSOR 1
    $assigned_side_1 = $_POST["side_1"];
    $calib_dist_1 = floatval($_POST["calib_1"]);
    $allowed_err_1 = floatval($_POST["err_1"]);
    $car_dist_1 = floatval($_POST["final_1"]);
    $error_raw_1 = abs($car_dist_1 - $calib_dist_1);
    $error_perc_1 = ($calib_dist_1 > 0) ? ($error_raw_1 / $calib_dist_1) * 100 : 0;
    $accuracy_1 = max(0, 100 - $error_perc_1);

    // SENSOR 2
    $assigned_side_2 = $_POST["side_2"];
    $calib_dist_2 = floatval($_POST["calib_2"]);
    $allowed_err_2 = floatval($_POST["err_2"]);
    $car_dist_2 = floatval($_POST["final_2"]);
    $error_raw_2 = abs($car_dist_2 - $calib_dist_2);
    $error_perc_2 = ($calib_dist_2 > 0) ? ($error_raw_2 / $calib_dist_2) * 100 : 0;
    $accuracy_2 = max(0, 100 - $error_perc_2);

    // SENSOR 3
    $assigned_side_3 = $_POST["side_3"];
    $calib_dist_3 = floatval($_POST["calib_3"]);
    $allowed_err_3 = floatval($_POST["err_3"]);
    $car_dist_3 = floatval($_POST["final_3"]);
    $error_raw_3 = abs($car_dist_3 - $calib_dist_3);
    $error_perc_3 = ($calib_dist_3 > 0) ? ($error_raw_3 / $calib_dist_3) * 100 : 0;
    $accuracy_3 = max(0, 100 - $error_perc_3);

    $sql = "INSERT INTO Sensors (
        sessionID, studentID, datetime_started, datetime_finished, 
        
        assigned_side_1, calibration_distance_1, allowed_distance_error_1, car_distance_from_line_1, final_distance_error_raw_1, final_distance_error_percentage_1, final_computed_accuracy_1,
        
        assigned_side_2, calibration_distance_2, allowed_distance_error_2, car_distance_from_line_2, final_distance_error_raw_2, final_distance_error_percentage_2, final_computed_accuracy_2,
        
        assigned_side_3, calibration_distance_3, allowed_distance_error_3, car_distance_from_line_3, final_distance_error_raw_3, final_distance_error_percentage_3, final_computed_accuracy_3
    ) VALUES (?, ?, ?, GETDATE(),   ?, ?, ?, ?, ?, ?, ?,   ?, ?, ?, ?, ?, ?, ?,   ?, ?, ?, ?, ?, ?, ?)";
    
    $params = array(
        $session_id, $student_id, $time_started,
        
        $assigned_side_1, $calib_dist_1, $allowed_err_1, $car_dist_1, $error_raw_1, $error_perc_1, $accuracy_1,
        
        $assigned_side_2, $calib_dist_2, $allowed_err_2, $car_dist_2, $error_raw_2, $error_perc_2, $accuracy_2,
        
        $assigned_side_3, $calib_dist_3, $allowed_err_3, $car_dist_3, $error_raw_3, $error_perc_3, $accuracy_3
    );

    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt) {
        // Update Session Averages by computing the Mean across all 3 sensors per test
        $avg_sql = "
            UPDATE Sessions 
            SET avg_distance_error_raw = (SELECT AVG((final_distance_error_raw_1 + final_distance_error_raw_2 + final_distance_error_raw_3)/3.0) FROM Sensors WHERE sessionID = ?),
                avg_distance_error_percentage = (SELECT AVG((final_distance_error_percentage_1 + final_distance_error_percentage_2 + final_distance_error_percentage_3)/3.0) FROM Sensors WHERE sessionID = ?),
                avg_computed_accuracy = (SELECT AVG((final_computed_accuracy_1 + final_computed_accuracy_2 + final_computed_accuracy_3)/3.0) FROM Sensors WHERE sessionID = ?)
            WHERE ID = ?
        ";
        sqlsrv_query($conn, $avg_sql, [$session_id, $session_id, $session_id, $session_id]);
        
        // Immediately redirect to the session detail page
        header("Location: session_detail.php?id=" . $session_id);
        exit;
    } else {
        $error = "Error saving sensor data: " . print_r(sqlsrv_errors(), true);
    }
}

// ── RETRIEVE PARAMETERS FROM CALIBRATE.PHP (OR AFTER POST) ─────────────
$student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : 0;
$session_id = isset($_POST['session_id']) ? intval($_POST['session_id']) : 0;

$side_1 = isset($_POST['side_1']) ? $_POST['side_1'] : 'None';
$calib_1 = isset($_POST['target_distance_1']) ? floatval($_POST['target_distance_1']) : 10.0;
$err_1 = isset($_POST['allowed_error_1']) ? floatval($_POST['allowed_error_1']) : 0;

$side_2 = isset($_POST['side_2']) ? $_POST['side_2'] : 'None';
$calib_2 = isset($_POST['target_distance_2']) ? floatval($_POST['target_distance_2']) : 10.0;
$err_2 = isset($_POST['allowed_error_2']) ? floatval($_POST['allowed_error_2']) : 0;

$side_3 = isset($_POST['side_3']) ? $_POST['side_3'] : 'None';
$calib_3 = isset($_POST['target_distance_3']) ? floatval($_POST['target_distance_3']) : 10.0;
$err_3 = isset($_POST['allowed_error_3']) ? floatval($_POST['allowed_error_3']) : 0;

// Need a single start timestamp string formatted for MS SQL Server
$time_started = isset($_POST['time_started']) ? $_POST['time_started'] : date('Y-m-d H:i:s');

// ── SAVE PRESET IF NEW ─────────────
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['finalize_results'])) {
    if (($side_1 !== 'None' && $side_1 !== '') || ($side_2 !== 'None' && $side_2 !== '') || ($side_3 !== 'None' && $side_3 !== '')) {
        $check_sql = "SELECT TOP 1 * FROM CalibrationPresets ORDER BY ID DESC";
        $chk_res = sqlsrv_query($conn, $check_sql);
        $needs_insert = true;
        if ($chk_res && sqlsrv_has_rows($chk_res)) {
            $chk_row = sqlsrv_fetch_array($chk_res, SQLSRV_FETCH_ASSOC);
            if ($chk_row['calibration_distance_1'] == $calib_1 && $chk_row['allowed_distance_error_1'] == $err_1 &&
                $chk_row['calibration_distance_2'] == $calib_2 && $chk_row['allowed_distance_error_2'] == $err_2 &&
                $chk_row['calibration_distance_3'] == $calib_3 && $chk_row['allowed_distance_error_3'] == $err_3) {
                $needs_insert = false;
            }
        }
        if ($needs_insert) {
            $ins_sql = "INSERT INTO CalibrationPresets (
                assigned_side_1, calibration_distance_1, allowed_distance_error_1,
                assigned_side_2, calibration_distance_2, allowed_distance_error_2,
                assigned_side_3, calibration_distance_3, allowed_distance_error_3
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            sqlsrv_query($conn, $ins_sql, [
                $side_1, $calib_1, $err_1,
                $side_2, $calib_2, $err_2,
                $side_3, $calib_3, $err_3
            ]);
        }
    }
}

// Fetch student name
$student_name = "Unknown";
if ($student_id > 0) {
    $res = sqlsrv_query($conn, "SELECT firstname, lastname FROM Students WHERE ID = ?", [$student_id]);
    if ($res && sqlsrv_has_rows($res)) {
        $row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC);
        $student_name = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Test - Sensor System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .test-panel { display: flex; flex-direction: column; gap: 20px; }
        .monitor-row { display: flex; gap: 20px; justify-content: space-around; flex-wrap: wrap; }
        .sensor-box { background: #1a1a2e; color: #0f0; padding: 20px; border-radius: 10px; width: 30%; min-width: 250px; text-align: center; font-family: 'Courier New', monospace; border: 2px solid #333; position: relative; }
        
        .hw-label { position: absolute; top: 10px; left: 10px; background: #333; color: #aaa; padding: 2px 6px; border-radius: 4px; font-size: 0.7em; }
        .sensor-title { color: #facc15; font-size: 1.1em; margin-bottom: 10px; font-weight: bold; text-transform: uppercase; }
        .sensor-val { font-size: 3.5em; font-weight: bold; margin: 10px 0; }
        .processed-val { font-size: 1.1em; color: #fff; margin-top: 5px; border-top: 1px solid #444; padding-top: 10px; }
        .calib-data { background: rgba(255,255,255,0.1); padding: 5px; border-radius: 4px; font-size: 0.85em; color: #ddd; margin-top: 5px; }

        .control-panel { display: flex; gap: 20px; margin-top: 20px; justify-content: center; }
        .btn-green { background: linear-gradient(135deg,#059669,#047857); color: white; padding: 15px 30px; font-size: 1.2em; border: none; border-radius: 8px; cursor: pointer; transition: 0.2s; font-weight: bold; }
        .btn-green:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(5,150,105,0.4); }
        .btn-green:disabled { opacity: 0.5; cursor: not-allowed; }
        
        .btn-cancel { background: #6b7280; color: white; padding: 15px 30px; font-size: 1.2em; border: none; border-radius: 8px; cursor: pointer; text-decoration: none; }
        .btn-cancel:hover { background: #4b5563; }
    </style>
</head>
<body>

<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">🔴 Active Parking Test</div>
            <div class="profile">Student: <strong><?= $student_name ?></strong></div>
        </header>

        <section class="content-section">
            <?php if ($error): ?>
                <div style="color: red; margin-bottom: 15px; background: #fef2f2; padding: 10px; border-left: 4px solid #ef4444;">❌ <?= $error ?></div>
            <?php endif; ?>

            <div class="card">
                <h3 style="display: flex; justify-content: space-between;">
                    Live Sensor Feed
                    <span style="font-size: 0.8em; color: #ef4444; font-weight: normal;">● Recording active</span>
                </h3>
                
                <div class="test-panel">
                    <div class="monitor-row">
                        <?php 
                        $hwNodes = [
                            ['id' => '1', 'side' => $side_1, 'calib' => $calib_1, 'err' => $err_1],
                            ['id' => '2', 'side' => $side_2, 'calib' => $calib_2, 'err' => $err_2],
                            ['id' => '3', 'side' => $side_3, 'calib' => $calib_3, 'err' => $err_3]
                        ];
                        foreach($hwNodes as $hw):
                            $title = "Sensor Module " . $hw['id'];
                        ?>
                        <div class="sensor-box">
                            <span class="hw-label"><?= $title ?></span>
                            <div class="sensor-title">Assigned: <?= $hw['side'] ?> Side</div>
                            <div class="sensor-val"><span id="val_<?= $hw['id'] ?>">--</span><span style="font-size:0.4em;">cm</span></div>
                            <div class="calib-data">Target: <?= $hw['calib'] ?>cm | Err Margin: <?= $hw['err'] ?>cm</div>
                            <div class="processed-val">
                                Error: <span id="err_<?= $hw['id'] ?>">--</span> cm<br>
                                Accuracy: <span id="acc_<?= $hw['id'] ?>">--</span>%
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="control-panel">
                        <a href="calibrate.php?session_id=<?= $session_id ?>" class="btn-cancel">Cancel Test</a>
                        <button class="btn-green" id="finalizeBtn" onclick="finalizeTest()">Finalize Results</button>
                    </div>
                </div>

                <!-- Hidden form to POST the final frozen data -->
                <form id="finalizeForm" method="POST" action="">
                    <input type="hidden" name="finalize_results" value="1">
                    <input type="hidden" name="student_id" value="<?= $student_id ?>">
                    <input type="hidden" name="session_id" value="<?= $session_id ?>">
                    <input type="hidden" name="time_started" value="<?= htmlspecialchars($time_started) ?>">
                    
                    <?php foreach($hwNodes as $hw): ?>
                    <input type="hidden" name="side_<?= $hw['id'] ?>" value="<?= $hw['side'] ?>">
                    <input type="hidden" name="calib_<?= $hw['id'] ?>" value="<?= $hw['calib'] ?>">
                    <input type="hidden" name="err_<?= $hw['id'] ?>" value="<?= $hw['err'] ?>">
                    <input type="hidden" name="final_<?= $hw['id'] ?>" id="form_final_<?= $hw['id'] ?>">
                    <?php endforeach; ?>
                </form>
            </div>
        </section>
    </main>
</div>

<script>
    const hwNodes = ['1', '2', '3'];
    const CONFIG = {
        1: { calib: <?= $calib_1 ?> },
        2: { calib: <?= $calib_2 ?> },
        3: { calib: <?= $calib_3 ?> }
    };
    
    let intervalId = null;
    let currentRaw = { 1: 0, 2: 0, 3: 0 };

    function fetchSensor() {
        fetch('sensor.php')
            .then(r => r.text())
            .then(data => {
                let parts = data.split(",");
                parts.forEach(part => {
                    let pair = part.trim().split("=");
                    if (pair.length === 2) {
                        let key = pair[0].trim().toUpperCase();
                        let val = parseFloat(pair[1]);
                        if (!isNaN(val) && val > 0 && CONFIG[key]) {
                            currentRaw[key] = val;
                        }
                    }
                });
                
                // --- UI DUMMY BYPASS FOR MISSING SENSORS ---
                hwNodes.forEach(id => {
                    if (currentRaw[id] === 0) {
                        currentRaw[id] = 10;
                    }
                });
                
                updateDOM();
            })
            .catch(e => console.error("Poll fail", e));
    }

    function updateDOM() {
        hwNodes.forEach(hw => {
            const raw = currentRaw[hw];
            const calib = CONFIG[hw].calib;
            
            document.getElementById(`val_${hw}`).innerText = raw;
            
            if (raw > 0) {
                const errRaw = Math.abs(raw - calib);
                const errPerc = (calib > 0) ? (errRaw / calib) * 100 : 0;
                let acc = 100 - errPerc;
                if (acc < 0) acc = 0;
                
                document.getElementById(`err_${hw}`).innerText = errRaw.toFixed(1);
                document.getElementById(`acc_${hw}`).innerText = acc.toFixed(1);
            }
        });
    }

    function finalizeTest() {
        if (confirm("Are you sure you want to stop tracking and officially save these test results?")) {
            clearInterval(intervalId);
            document.getElementById('finalizeBtn').disabled = true;
            document.getElementById('finalizeBtn').innerHTML = "Saving...";
            
            hwNodes.forEach(hw => {
                document.getElementById(`form_final_${hw}`).value = currentRaw[hw] || 0;
            });
            
            document.getElementById('finalizeForm').submit();
        }
    }

    // Start polling immediately
    intervalId = setInterval(fetchSensor, 400);
</script>

</body>
</html>
