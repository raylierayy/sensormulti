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
    <link rel="stylesheet" href="theme-modern.css">
    <script>document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'dark');</script>
    <style>
        /* ── Monitor layout ──────────────────────────────────── */
        .test-panel { display: flex; flex-direction: column; gap: 20px; }
        .monitor-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }

        /* ── Sensor reading cards ────────────────────────────── */
        .sensor-box {
            background: rgba(10, 14, 26, 0.75);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 2px solid rgba(99, 102, 241, 0.30);
            color: #fff;
            padding: 24px 20px;
            border-radius: 20px;
            text-align: center;
            position: relative;
            box-shadow:
                0 8px 32px rgba(0,0,0,0.45),
                inset 0 1px 0 rgba(255,255,255,0.07),
                0 0 24px rgba(99,102,241,0.08);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .hw-label {
            position: absolute; top: 10px; left: 12px;
            background: rgba(255,255,255,0.12); color: rgba(255,255,255,0.70);
            padding: 3px 10px; border-radius: 999px; font-size: 0.68em; font-weight: 700;
            backdrop-filter: blur(6px); letter-spacing: 0.04em;
        }

        .sensor-title {
            color: #fde68a; font-size: 0.82em; margin: 32px 0 8px;
            font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em;
        }

        .sensor-val {
            font-size: 3.4em; font-weight: 900; line-height: 1;
            margin: 8px 0 4px; transition: color 0.3s ease; letter-spacing: -0.02em;
        }

        .sensor-unit {
            font-size: 0.35em; font-weight: 600; opacity: 0.70; vertical-align: super;
        }

        .calib-data {
            background: rgba(255,255,255,0.07); padding: 7px 12px; border-radius: 10px;
            font-size: 0.80em; color: rgba(255,255,255,0.55); margin-top: 10px; margin-bottom: 10px;
        }

        .processed-val {
            font-size: 0.90em; color: rgba(255,255,255,0.75);
            border-top: 1px solid rgba(255,255,255,0.12); padding-top: 10px;
            margin-top: 8px; line-height: 1.7;
        }

        .accuracy-bar {
            height: 4px; border-radius: 999px;
            background: rgba(255,255,255,0.10); margin-top: 10px; overflow: hidden;
        }

        .accuracy-bar-fill {
            height: 100%; border-radius: 999px;
            background: linear-gradient(90deg, #10b981, #06b6d4);
            transition: width 0.4s ease, background 0.3s ease;
            width: 0%;
        }

        /* ── Connection alarm ────────────────────────────────── */
        #connection-alert {
            display: none;
            background: rgba(239,68,68,0.18);
            border: 2px solid rgba(239,68,68,0.60);
            color: #fca5a5;
            padding: 14px 20px;
            border-radius: 14px;
            font-weight: 800;
            font-size: 1em;
            text-align: center;
            backdrop-filter: blur(10px);
            animation: pulse-alert 1.2s ease-in-out infinite;
        }

        @keyframes pulse-alert {
            0%, 100% { opacity: 1;    }
            50%       { opacity: 0.55; }
        }

        /* ── Control buttons ─────────────────────────────────── */
        .control-panel { display: flex; gap: 16px; margin-top: 20px; justify-content: center; flex-wrap: wrap; }

        .btn-finalize {
            background: linear-gradient(135deg, #10b981, #06b6d4);
            color: #fff; border: none;
            padding: 15px 36px; font-size: 1.05em;
            border-radius: 999px; cursor: pointer;
            font-weight: 800; font-family: 'Inter', sans-serif;
            box-shadow: 0 8px 28px rgba(16,185,129,0.45);
            transition: all 0.2s ease;
        }
        .btn-finalize:hover    { transform: translateY(-2px); box-shadow: 0 14px 36px rgba(16,185,129,0.65); }
        .btn-finalize:disabled { opacity: 0.45; cursor: not-allowed; transform: none; box-shadow: none; }

        /* Live indicator dot */
        .live-indicator {
            display: inline-flex; align-items: center; gap: 7px;
            background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.35);
            color: #fca5a5; padding: 5px 14px; border-radius: 999px; font-size: 0.80em; font-weight: 700;
        }

        .live-dot {
            width: 8px; height: 8px; background: #ef4444; border-radius: 50%;
            animation: pulse-alert 1.2s ease-in-out infinite;
        }

        @media (max-width: 900px) {
            .monitor-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome" style="display:flex; align-items:center; gap:10px;">
                <span class="live-indicator">
                    <span class="live-dot"></span> LIVE
                </span>
                Active Parking Test
            </div>
            <div class="profile">
                <button id="themeToggle" class="theme-toggle-btn" title="Switch to Light Mode" aria-label="Toggle theme">☀️</button>
                <span style="color:rgba(255,255,255,0.55); font-size:0.88em;">Student:</span>
                <strong style="color:#fff;"><?= $student_name ?></strong>
            </div>
        </header>

        <section class="content-section">
            <?php if ($error): ?>
                <div class="msg-error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="card animate-in">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h3 style="margin:0;">📡 Live Sensor Feed</h3>
                    <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.35);color:#fca5a5;padding:5px 14px;border-radius:999px;font-size:0.78em;font-weight:700;">
                        <span style="width:7px;height:7px;background:#ef4444;border-radius:50%;display:inline-block;animation:pulse-alert 1.2s infinite;"></span>
                        Recording
                    </span>
                </div>
                
                <div class="test-panel">
                    <!-- Connection loss alarm -->
                    <div id="connection-alert">🚨 Sensor connection lost! Check hardware and USB connection.</div>

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
                        <div class="sensor-box" id="sensor-box-<?= $hw['id'] ?>">
                            <span class="hw-label"><?= $title ?></span>
                            <div class="sensor-title">📍 <?= $hw['side'] ?> Side</div>
                            <div class="sensor-val">
                                <span id="val_<?= $hw['id'] ?>">--</span>
                                <span class="sensor-unit">cm</span>
                            </div>
                            <div class="calib-data">
                                🎯 Target: <strong><?= $hw['calib'] ?> cm</strong> &nbsp;|&nbsp;
                                ±&nbsp;<?= $hw['err'] ?> cm
                            </div>
                            <div class="processed-val">
                                Error: <span id="err_<?= $hw['id'] ?>">--</span> cm&ensp;
                                Accuracy: <strong><span id="acc_<?= $hw['id'] ?>">--</span>%</strong>
                            </div>
                            <div class="accuracy-bar">
                                <div class="accuracy-bar-fill" id="bar_<?= $hw['id'] ?>"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="control-panel">
                        <a href="calibrate.php?session_id=<?= $session_id ?>" class="btn-cancel" aria-label="Cancel test">← Cancel Test</a>
                        <button class="btn-finalize" id="finalizeBtn" onclick="finalizeTest()" aria-label="Finalize and save test results">✅ Finalize Results</button>
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

    // ── Connection alarm ──────────────────────────────────────────────
    const SENSOR_TIMEOUT_MS = 3000; // ms without update before alarm triggers
    let lastSensorUpdate = Date.now();
    let connectionAlarmShown = false;

    function showConnectionAlarm() {
        if (!connectionAlarmShown) {
            document.getElementById('connection-alert').style.display = 'block';
            connectionAlarmShown = true;
        }
    }

    function hideConnectionAlarm() {
        document.getElementById('connection-alert').style.display = 'none';
        connectionAlarmShown = false;
    }

    setInterval(() => {
        if (Date.now() - lastSensorUpdate > SENSOR_TIMEOUT_MS) {
            showConnectionAlarm();
        }
    }, 500);

    // ── Color-coded accuracy ──────────────────────────────────────────
    function getAccuracyColor(accuracy) {
        if (accuracy >= 90) return '#10b981'; // green
        if (accuracy >= 70) return '#f59e0b'; // yellow
        return '#ef4444';                     // red
    }

    function fetchSensor() {
        fetch('sensor.php')
            .then(r => r.text())
            .then(data => {
                if (data.trim()) {
                    lastSensorUpdate = Date.now();
                    if (connectionAlarmShown) hideConnectionAlarm();
                }

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

                // Color-coded feedback
                const color = getAccuracyColor(acc);
                document.getElementById(`val_${hw}`).style.color = color;
                const box = document.getElementById(`sensor-box-${hw}`);
                if (box) {
                    box.style.borderColor = color;
                    box.style.boxShadow = `0 8px 32px rgba(0,0,0,0.45), 0 0 24px ${color}33`;
                }

                // Accuracy bar fill
                const bar = document.getElementById(`bar_${hw}`);
                if (bar) {
                    bar.style.width = acc.toFixed(1) + '%';
                    bar.style.background = acc >= 90
                        ? 'linear-gradient(90deg, #10b981, #06b6d4)'
                        : acc >= 70
                        ? 'linear-gradient(90deg, #f59e0b, #fbbf24)'
                        : 'linear-gradient(90deg, #ef4444, #f87171)';
                }
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
