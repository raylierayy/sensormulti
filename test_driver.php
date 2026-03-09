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

        /* Glass-morphism sensor card */
        .sensor-box {
            background: rgba(255,255,255,0.14);
            backdrop-filter: blur(14px) saturate(140%);
            -webkit-backdrop-filter: blur(14px) saturate(140%);
            border: 1px solid rgba(255,255,255,0.25);
            box-shadow: 0 18px 45px rgba(0,0,0,0.25), inset 0 1px 0 rgba(255,255,255,0.25);
            border-radius: 24px;
            color: #1e3a8a;
            padding: 24px 20px 20px;
            width: 30%;
            min-width: 250px;
            text-align: center;
            font-family: 'Segoe UI', sans-serif;
            position: relative;
            transition: box-shadow 0.3s;
        }
        .sensor-box:hover {
            box-shadow: 0 22px 55px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.3);
        }

        .hw-label {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(30,58,138,0.12);
            color: #1e3a8a;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.72em;
            font-weight: 600;
            border: 1px solid rgba(30,58,138,0.18);
        }
        .sensor-title {
            color: #1e3a8a;
            font-size: 1.05em;
            margin: 22px 0 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .sensor-val {
            font-size: 3.5em;
            font-weight: bold;
            margin: 10px 0;
            transition: color 0.4s ease;
        }
        .processed-val {
            font-size: 1.05em;
            color: #334155;
            margin-top: 8px;
            border-top: 1px solid rgba(30,58,138,0.15);
            padding-top: 10px;
            transition: color 0.4s ease;
        }
        .calib-data {
            background: rgba(30,58,138,0.07);
            padding: 6px 10px;
            border-radius: 10px;
            font-size: 0.82em;
            color: #475569;
            margin-top: 6px;
        }

        /* Connection alert banner */
        .connection-alert {
            display: none;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(239,68,68,0.90);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 2px solid rgba(254,202,202,0.8);
            border-radius: 999px;
            padding: 12px 28px;
            color: white;
            font-weight: bold;
            font-size: 1em;
            z-index: 9999;
            box-shadow: 0 8px 30px rgba(239,68,68,0.35);
            white-space: nowrap;
        }
        .connection-alert.visible { display: block; }

        .control-panel { display: flex; gap: 20px; margin-top: 20px; justify-content: center; }
        .btn-green { background: linear-gradient(135deg,#059669,#047857); color: white; padding: 15px 30px; font-size: 1.2em; border: none; border-radius: 999px; cursor: pointer; transition: 0.2s; font-weight: bold; box-shadow: 0 4px 14px rgba(5,150,105,0.3); }
        .btn-green:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(5,150,105,0.45); }
        .btn-green:disabled { opacity: 0.5; cursor: not-allowed; }
        
        .btn-cancel { background: rgba(107,114,128,0.15); color: #374151; padding: 15px 30px; font-size: 1.2em; border: 1px solid rgba(107,114,128,0.3); border-radius: 999px; cursor: pointer; text-decoration: none; transition: 0.2s; font-weight: 600; backdrop-filter: blur(8px); }
        .btn-cancel:hover { background: rgba(107,114,128,0.25); }
    </style>
</head>
<body>

<!-- Connection loss alarm banner -->
<div id="connectionAlert" class="connection-alert">⚠️ Sensor Connection Lost — Check Python Script</div>

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
                            $sideLabel = $hw['side'];
                            // Format descriptive sensor name from assigned side
                            $sensorTitle = ($sideLabel && $sideLabel !== 'None') ? htmlspecialchars($sideLabel) . ' Sensor' : 'Sensor ' . $hw['id'];
                        ?>
                        <div class="sensor-box">
                            <span class="hw-label">Module #<?= $hw['id'] ?></span>
                            <div class="sensor-title"><?= $sensorTitle ?></div>
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
    let lastUpdateTime = Date.now();
    let connectionLost = false;

    // Returns a color based on accuracy percentage
    function getAccuracyColor(accuracy) {
        if (accuracy >= 90) return '#10b981'; // Green – perfect
        if (accuracy >= 70) return '#f59e0b'; // Yellow – warning
        return '#ef4444';                      // Red – danger
    }

    function fetchSensor() {
        fetch('sensor.php')
            .then(r => r.text())
            .then(data => {
                if (data && data.trim().length > 0) {
                    lastUpdateTime = Date.now();
                    setConnectionAlarm(false);

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
                }

                // --- UI DUMMY BYPASS FOR MISSING SENSORS ---
                hwNodes.forEach(id => {
                    if (currentRaw[id] === 0) {
                        currentRaw[id] = 10;
                    }
                });
                
                updateDOM();
            })
            .catch(e => {
                console.error("Poll fail", e);
                checkConnectionTimeout();
            });
    }

    function checkConnectionTimeout() {
        if (Date.now() - lastUpdateTime > 3000) {
            setConnectionAlarm(true);
        }
    }

    function setConnectionAlarm(lost) {
        const el = document.getElementById('connectionAlert');
        if (lost && !connectionLost) {
            connectionLost = true;
            el.classList.add('visible');
        } else if (!lost && connectionLost) {
            connectionLost = false;
            el.classList.remove('visible');
        }
    }

    function updateDOM() {
        // Check for stale data (no updates for 3+ seconds)
        checkConnectionTimeout();

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

                // Apply color coding based on accuracy
                const color = getAccuracyColor(acc);
                document.getElementById(`val_${hw}`).style.color = color;
                document.getElementById(`err_${hw}`).closest('.processed-val').style.color = color;
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
