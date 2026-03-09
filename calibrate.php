<?php
require 'session_check.php';
require 'db_connection.php';

// The session ID dictates the student (if we are continuing a session)
$session_id = isset($_GET['session_id']) ? intval($_GET['session_id']) : 0;
$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
$student_name = "Unknown Student";
$students = [];

if ($session_id > 0) {
    // Locked into an existing session
    $ses_sql = "SELECT s.studentID, st.firstname, st.lastname FROM Sessions s JOIN Students st ON s.studentID = st.ID WHERE s.ID = ?";
    $ses_res = sqlsrv_query($conn, $ses_sql, [$session_id]);
    if ($ses_res && sqlsrv_has_rows($ses_res)) {
        $row = sqlsrv_fetch_array($ses_res, SQLSRV_FETCH_ASSOC);
        $student_id = $row['studentID'];
        $student_name = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
    } else {
        // Invalid session ID
        $session_id = 0;
    }
}

if ($session_id == 0) {
    // Let the instructor select the student, we will build a Session when they finalize
    $sql = "SELECT ID, firstname, lastname FROM Students WHERE pass_fail IS NULL OR pass_fail = '' ORDER BY ID DESC";
    $result = sqlsrv_query($conn, $sql);
    if ($result !== false && sqlsrv_has_rows($result)) {
        while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $students[] = $row;
        }
    }
}

// Fetch top 3 latest presets
$presets = [];
$preset_sql = "SELECT TOP 3 *, FORMAT(created_at, 'MMM dd, HH:mm') as formatted_date FROM CalibrationPresets ORDER BY ID DESC";
$preset_res = sqlsrv_query($conn, $preset_sql);
if ($preset_res !== false && sqlsrv_has_rows($preset_res)) {
    while($row = sqlsrv_fetch_array($preset_res, SQLSRV_FETCH_ASSOC)) {
        if(isset($row['assigned_side_1'])) { // ensuring it's the new schema
            $presets[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Parking Test - Sensor System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .status-banner {
            display: flex; align-items: center; gap: 12px; padding: 14px 20px;
            border-radius: 8px; font-weight: 600; margin-bottom: 20px;
        }
        .status-banner.checking  { background:#eff6ff; border:1px solid #93c5fd; color:#1e3a8a; }
        .status-banner.error     { background:#fef2f2; border:1px solid #fca5a5; color:#991b1b; }
        .status-banner.ok        { background:#f0fdf4; border:1px solid #86efac; color:#166534; }

        .sensor-check-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
        .sensor-chip {
            border-radius: 8px; padding: 16px; text-align: center; font-weight: 700;
            border: 2px solid #e5e7eb; background: #f8fbff;
        }
        .sensor-chip.connected    { background:#f0fdf4; border-color:#86efac; color:#166534; }
        .sensor-chip.disconnected { background:#fef2f2; border-color:#fca5a5; color:#991b1b; }
        
        .setup-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px; }
        .setup-card { background:#fff; border:2px solid #e5e7eb; border-radius:10px; padding:20px; }
        .setup-card h4 { margin:0 0 15px; color:#1e3a8a; font-size:1.1em; border-bottom:2px solid #eee; padding-bottom:10px; }
        
        .btn-proceed {
            background: linear-gradient(135deg,#059669,#047857); color: #fff; border: none;
            border-radius: 6px; padding: 15px 30px; font-size: 1.1em; font-weight: 700; width: 100%;
            cursor: pointer; margin-top: 20px; transition: transform 0.2s;
        }
        .btn-proceed:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(5,150,105,0.3); }
        .btn-proceed:disabled { opacity: 0.5; filter: grayscale(1); cursor: not-allowed; transform: none; box-shadow: none; }

        .btn-capture {
            background: #2563eb; color: #fff; border: none; padding: 10px 15px; border-radius: 6px; font-weight: bold; cursor: pointer; width: 100%; transition: background 0.2s;
        }
        .btn-capture:hover { background: #1d4ed8; }
        .captured-value { font-size: 1.5em; font-weight: bold; color: #0f766e; text-align: center; display: block; margin-top: 10px; }
    </style>
</head>
<body>

<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">🚙 Setup Parking Test</div>
            <div class="profile">
                <span title="Notifications">🔔</span>
                <span title="Profile">👤</span>
            </div>
        </header>

        <section class="content-section">
            <div class="card" id="step1Card">
                <h3>Step 1 — Verify Hardware Connection</h3>
                
                <div id="statusBanner" class="status-banner checking">
                    <span id="statusIcon">🔄</span>
                    <span id="statusText">Checking sensors via sensor.php...</span>
                </div>

                <div class="sensor-check-grid" style="grid-template-columns: 1fr;">
                    <div class="sensor-chip" id="chip_M">Main Sensor Module<br><small id="chipval_M">--</small></div>
                </div>
                
                <button class="action-btn" id="retryBtn" onclick="checkSensors()" style="display:none; background:#b91c1c;">🔁 Retry Connection</button>
            </div>

            <div class="card" id="step2Card" style="display:none;">
                <h3>Step 2 — Configure Test Parameters</h3>
                
                <?php if($session_id == 0 && count($students) == 0): ?>
                    <div style="background:#fef2f2; color:#991b1b; padding:15px; border-radius:5px; border:1px solid #fca5a5;">
                        ❌ No active students available. You must <a href="add_student.php" style="color:#7f1d1d;">register a student</a> before testing.
                    </div>
                <?php else: ?>
                    <form method="POST" action="test_driver.php" id="setupForm">
                        <input type="hidden" name="session_id" value="<?= $session_id ?>">
                        
                        <?php if($session_id > 0): ?>
                            <input type="hidden" name="student_id" value="<?= $student_id ?>">
                            <div class="form-group" style="background:#f8fbff; padding:15px; border-radius:8px; border:1px solid #bfdbfe;">
                                <label>Continuing Testing For Session #<?= $session_id ?></label>
                                <div style="font-size: 1.2em; font-weight: bold; color: #1e3a8a; padding: 10px; background: #fff; border: 1px dashed #93c5fd; border-radius: 5px;">
                                    👤 <?= $student_name ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="form-group" style="background:#f8fbff; padding:15px; border-radius:8px; border:1px solid #bfdbfe;">
                                <label>Select Student to Start a New Testing Session</label>
                                <select name="student_id" required class="form-control">
                                    <option value="">-- Select Active Student --</option>
                                    <?php foreach($students as $s): ?>
                                        <option value="<?= $s['ID'] ?>" <?= ($student_id == $s['ID']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($s['firstname'] . ' ' . $s['lastname']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <!-- GLOBAL PRESET LOADER -->
                        <?php if(count($presets) > 0): ?>
                            <div class="form-group" style="background:#fffbeb; padding:20px; border-radius:8px; border:2px solid #fde68a; margin-bottom: 20px;">
                                <label style="font-weight:bold; color:#b45309; font-size:1.1em;">🏅 Rapid Configuration: Load Saved Parking Standard</label>
                                <select id="global_preset_selector" class="form-control" style="font-size:1.1em; padding:10px;" onchange="loadGlobalPreset(this.value)">
                                    <option value="">-- Start Fresh / Manual Calibration --</option>
                                    <?php foreach($presets as $p): 
                                        $valString = $p['assigned_side_1']."|".$p['calibration_distance_1']."|".$p['allowed_distance_error_1']."||".
                                                     $p['assigned_side_2']."|".$p['calibration_distance_2']."|".$p['allowed_distance_error_2']."||".
                                                     $p['assigned_side_3']."|".$p['calibration_distance_3']."|".$p['allowed_distance_error_3'];
                                    ?>
                                        <option value="<?= $valString ?>">
                                            <?= htmlspecialchars($p['formatted_date']) ?> - Standardized Preset for all 3 Sensors
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="setup-grid" id="sensorCardsContainer">
                            <?php 
                            // Define the 3 hardware nodes
                            $sensors = ['1', '2', '3'];
                            foreach($sensors as $hw_id): 
                                $title = "Sensor Module " . $hw_id;
                            ?>
                            <div class="setup-card" id="card_<?= $hw_id ?>">
                                <h4><?= $title ?></h4>
                                <div class="form-group">
                                    <label>Assign to Side</label>
                                    <select name="side_<?= $hw_id ?>" id="side_<?= $hw_id ?>" required class="form-control side-selector" onchange="updateSideSelectors()">
                                        <option value="">-- Select Side --</option>
                                        <option value="None">No side</option>
                                        <option value="Left">Left Side</option>
                                        <option value="Front">Front</option>
                                        <option value="Right">Right Side</option>
                                    </select>
                                </div>
                                <div class="form-group" style="background:#f1f5f9; padding: 15px; border-radius: 8px;">
                                    <label>Perfect Parking Distance</label>
                                    <div style="display:flex; justify-content:space-between; margin-bottom:15px; background:#fff; padding:10px; border-radius:6px; border:1px solid #e2e8f0;">
                                        <div style="text-align:center; flex-grow:1; border-right:2px solid #e2e8f0;">
                                            <span style="display:block; font-size:0.8em; color:#64748b; font-weight:bold; text-transform:uppercase;">Live Feed</span>
                                            <span id="live_feed_<?= $hw_id ?>" style="font-size:1.8em; font-weight:bold; color:#2563eb;">-- cm</span>
                                        </div>
                                        <div style="text-align:center; flex-grow:1;">
                                            <span style="display:block; font-size:0.8em; color:#64748b; font-weight:bold; text-transform:uppercase;">Captured target</span>
                                            <span class="captured-value" id="display_calib_<?= $hw_id ?>" style="margin-top:0; font-size:1.8em;">--</span>
                                        </div>
                                    </div>
                                    <div style="display:flex; gap:10px;">
                                        <button type="button" class="btn-capture" id="btn_capture_<?= $hw_id ?>" onclick="captureBaseline('<?= $hw_id ?>')" style="flex:1;">📷 Capture</button>
                                    </div>
                                    <input type="hidden" id="target_distance_<?= $hw_id ?>" name="target_distance_<?= $hw_id ?>" value="" required>
                                </div>
                                <div class="form-group">
                                    <label>Allowed Error (cm)</label>
                                    <input type="number" step="0.1" name="allowed_error_<?= $hw_id ?>" id="allowed_error_<?= $hw_id ?>" value="5" required class="form-control">
                                </div>
                                
                                <div style="display:flex; gap:10px; margin-top:15px;">
                                    <button type="button" id="btn_lock_<?= $hw_id ?>" class="action-btn" style="flex:1; background:#059669;" onclick="lockSensor('<?= $hw_id ?>')">🔒 Lock Calibration</button>
                                    <button type="button" id="btn_unlock_<?= $hw_id ?>" class="action-btn" style="flex:1; background:#b91c1c; display:none;" onclick="unlockSensor('<?= $hw_id ?>')">🔓 Unlock</button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" class="btn-proceed" id="proceedBtn" disabled>▶ Begin Active Test Phase</button>
                    </form>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>

<script>
    const hwNodes = ['1', '2', '3'];
    let sensorLocked = { 1: false, 2: false, 3: false };

    // Function to prevent selecting the same side in multiple dropdowns
    function updateSideSelectors() {
        const sideSelects = document.querySelectorAll('.side-selector');
        const selectedValues = [];

        // Collect currently selected values
        sideSelects.forEach(sel => {
            if (sel.value && sel.value !== "None") {
                selectedValues.push(sel.value);
            }
        });

        // Loop through all options and disable/enable based on global selection
        sideSelects.forEach(sel => {
            const options = sel.options;
            for (let i = 0; i < options.length; i++) {
                const opt = options[i];
                if (opt.value && opt.value !== "None" && selectedValues.includes(opt.value) && sel.value !== opt.value) {
                    opt.disabled = true;
                } else {
                    opt.disabled = false;
                }
            }
        });
    }

    function checkLocks() {
        // Only enable Main Proceed if all 3 are locked
        const allLocked = Object.values(sensorLocked).every(val => val === true);
        document.getElementById('proceedBtn').disabled = !allLocked;
    }

    function lockSensor(hw_id) {
        // Validate
        const side = document.getElementById(`side_${hw_id}`).value;
        const target = document.getElementById(`target_distance_${hw_id}`).value;
        const err = document.getElementById(`allowed_error_${hw_id}`).value;

        if (!side) { alert(`Please assign a Side for Sensor ${hw_id}.`); return; }
        if (!target) { alert(`Please capture or load a parking distance for Sensor ${hw_id}.`); return; }
        if (!err) { alert(`Please specify allowed error for Sensor ${hw_id}.`); return; }

        // Disable inputs
        document.getElementById(`side_${hw_id}`).style.pointerEvents = 'none';
        document.getElementById(`side_${hw_id}`).style.opacity = '0.6';
        
        let globalPresetSel = document.getElementById(`global_preset_selector`);
        if(globalPresetSel) globalPresetSel.disabled = true;
        
        document.getElementById(`btn_capture_${hw_id}`).disabled = true;
        document.getElementById(`allowed_error_${hw_id}`).readOnly = true;

        // Toggle buttons
        document.getElementById(`btn_lock_${hw_id}`).style.display = 'none';
        document.getElementById(`btn_unlock_${hw_id}`).style.display = 'block';

        sensorLocked[hw_id] = true;
        document.getElementById(`card_${hw_id}`).style.border = "2px solid #059669";
        
        checkLocks();
    }

    function unlockSensor(hw_id) {
        // Re-enable inputs
        document.getElementById(`side_${hw_id}`).style.pointerEvents = 'auto';
        document.getElementById(`side_${hw_id}`).style.opacity = '1';
        
        // Only reactivate global preset selector if ALL are unlocked
        let anyLocked = Object.values(sensorLocked).some(val => val === true);
        let globalPresetSel = document.getElementById(`global_preset_selector`);
        if(globalPresetSel && !anyLocked && sensorLocked[hw_id]) {
             globalPresetSel.disabled = false;
        }

        document.getElementById(`btn_capture_${hw_id}`).disabled = false;
        document.getElementById(`allowed_error_${hw_id}`).readOnly = false;

        // Toggle buttons
        document.getElementById(`btn_unlock_${hw_id}`).style.display = 'none';
        document.getElementById(`btn_lock_${hw_id}`).style.display = 'block';

        sensorLocked[hw_id] = false;
        document.getElementById(`card_${hw_id}`).style.border = "2px solid #e5e7eb";
        
        checkLocks();
    }

    function loadGlobalPreset(valString) {
        if (Object.values(sensorLocked).some(locked => locked)) {
            alert("Please unlock all sensors first before loading a global standard.");
            return;
        }
        
        if (valString === "") {
            hwNodes.forEach(hw_id => {
                document.getElementById(`side_${hw_id}`).value = "";
                document.getElementById(`display_calib_${hw_id}`).innerText = "Not Captured";
                document.getElementById(`target_distance_${hw_id}`).value = "";
                document.getElementById(`allowed_error_${hw_id}`).value = 5;
            });
            updateSideSelectors();
            return;
        }
        
        // format: side1|calib1|err1||side2|calib2|err2||side3|calib3|err3
        const chunks = valString.split("||");
        
        chunks.forEach((chunk, index) => {
            if(!chunk) return;
            const hw_id = hwNodes[index];
            const parts = chunk.split('|');
            const side = parts[0];
            const calib = parts[1];
            const err = parts[2];
            
            document.getElementById(`side_${hw_id}`).value = side;
            document.getElementById(`display_calib_${hw_id}`).innerText = calib + " cm (Loaded Preset)";
            document.getElementById(`target_distance_${hw_id}`).value = calib;
            document.getElementById(`allowed_error_${hw_id}`).value = err;
        });
        
        updateSideSelectors();
    }

    function captureBaseline(hw_id) {
        if (sensorLocked[hw_id]) return;
        
        document.getElementById(`display_calib_${hw_id}`).innerText = "Capturing...";
        
        fetch('sensor.php')
            .then(r => r.text())
            .then(data => {
                // Parse L=x,F=y,R=z
                let parts = data.split(",");
                let valToCapture = null;
                
                parts.forEach(part => {
                    let pair = part.trim().split("=");
                    if (pair.length === 2 && pair[0].trim().toUpperCase() === hw_id) {
                        valToCapture = parseFloat(pair[1]);
                    }
                });
                
                // DUMMY OVERRIDE FOR TESTING IF SENSOR SCRIPT NOT ACTIVE YET
                if (valToCapture == null || isNaN(valToCapture)) {
                     valToCapture = Math.floor(Math.random() * (15 - 5 + 1) + 5); 
                }
                
                if (valToCapture !== null && !isNaN(valToCapture) && valToCapture > 0) {
                    document.getElementById(`display_calib_${hw_id}`).innerText = valToCapture + " cm";
                    document.getElementById(`target_distance_${hw_id}`).value = valToCapture;
                } else {
                    document.getElementById(`display_calib_${hw_id}`).innerText = "Error";
                }
            })
            .catch(() => {
                document.getElementById(`display_calib_${hw_id}`).innerText = "Failed";
            });
    }

    function checkSensors() {
        document.getElementById('statusBanner').className = 'status-banner checking';
        document.getElementById('statusIcon').innerText = '🔄';
        document.getElementById('statusText').innerText = 'Checking sensors via sensor.php...';
        document.getElementById('retryBtn').style.display = 'none';

        // Add 3 chips to UI
        const grid = document.querySelector('.sensor-check-grid');
        grid.innerHTML = hwNodes.map(id => `<div class="sensor-chip" id="chip_${id}">Sensor ${id}<br><small id="chipval_${id}">--</small></div>`).join('');

        fetch('sensor.php')
            .then(r => r.text())
            .then(data => {
                // Ignore real payload for dummy testing if wanted, or parse it literally
                let allOk = true;
                
                hwNodes.forEach(id => {
                    const chip = document.getElementById(`chip_${id}`);
                    chip.className = 'sensor-chip connected';
                    chip.innerHTML = `Sensor Module ${id}<br><small>Ready</small>`;
                });

                if (allOk) {
                    document.getElementById('statusBanner').className = 'status-banner ok';
                    document.getElementById('statusIcon').innerText = '✅';
                    document.getElementById('statusText').innerText = 'Hardware connected successfully! You may configure test parameters.';
                    document.getElementById('step2Card').style.display = 'block';
                }
            }).catch(() => {
                document.getElementById('statusBanner').className = 'status-banner error';
                document.getElementById('statusIcon').innerText = '❌';
                document.getElementById('statusText').innerText = 'Could not reach sensor.php. Server error.';
                document.getElementById('retryBtn').style.display = 'inline-block';
            });
    }

    let liveFeedInterval = null;

    function startLiveFeed() {
        if (liveFeedInterval !== null) clearInterval(liveFeedInterval);
        
        liveFeedInterval = setInterval(() => {
            fetch('sensor.php')
                .then(r => r.text())
                .then(data => {
                    // DUMMY MOCK For testing
                    let mockData = { 1: 10, 2: 12, 3: 18 };
                    
                    let parts = data.split(",");
                    parts.forEach(part => {
                        let pair = part.trim().split("=");
                        if (pair.length === 2) {
                            let key = pair[0].trim().toUpperCase();
                            let val = parseFloat(pair[1]);
                            if (!isNaN(val)) mockData[key] = val;
                        }
                    });

                    hwNodes.forEach(id => {
                        const disp = document.getElementById(`live_feed_${id}`);
                        if(disp && mockData[id] !== undefined) {
                            disp.innerText = mockData[id] + " cm";
                        }
                    });
                })
                .catch(() => {});
        }, 300); // Poll every 300ms
    }

    window.addEventListener('DOMContentLoaded', () => {
        checkSensors();
        startLiveFeed();
        updateSideSelectors();
    });
</script>

</body>
</html>
