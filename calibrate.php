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
    <link rel="stylesheet" href="theme-modern.css">
    <style>
        /* ── Status banners ──────────────────────────────────── */
        .status-banner {
            display: flex; align-items: center; gap: 12px; padding: 14px 20px;
            border-radius: 14px; font-weight: 600; margin-bottom: 20px;
        }
        .status-banner.checking  { background:rgba(147,197,253,0.15); border:1px solid rgba(147,197,253,0.40); color:#bfdbfe; }
        .status-banner.error     { background:rgba(252,165,165,0.12); border:1px solid rgba(252,165,165,0.40); color:#fecaca; }
        .status-banner.ok        { background:rgba(134,239,172,0.12); border:1px solid rgba(134,239,172,0.40); color:#bbf7d0; }

        /* ── Sensor check chips ──────────────────────────────── */
        .sensor-check-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 20px; }
        .sensor-chip {
            border-radius: 14px; padding: 16px 12px; text-align: center; font-weight: 700;
            border: 1px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.07);
            color: rgba(255,255,255,0.75); transition: all 0.3s ease;
        }
        .sensor-chip.connected    { background:rgba(16,185,129,0.12); border-color:rgba(16,185,129,0.45); color:#6ee7b7; }
        .sensor-chip.disconnected { background:rgba(239,68,68,0.12); border-color:rgba(239,68,68,0.45); color:#fca5a5; }
        .sensor-chip .chip-icon   { font-size:1.5em; display:block; margin-bottom:6px; }

        /* ── 3-sensor setup grid ─────────────────────────────── */
        .setup-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 20px; }
        .setup-card {
            background: rgba(255,255,255,0.06);
            border: 1.5px solid rgba(255,255,255,0.12);
            border-radius: 18px; padding: 20px;
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            transition: border-color 0.3s ease;
        }
        .setup-card h4 {
            margin: 0 0 14px; color: #fff; font-size: 0.98em; font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,0.12); padding-bottom: 10px;
            display: flex; align-items: center; gap: 8px;
        }

        /* ── Proceed button ──────────────────────────────────── */
        .btn-proceed {
            background: linear-gradient(135deg, #10b981, #06b6d4);
            color: #fff; border: none;
            border-radius: 16px; padding: 16px 30px; font-size: 1.05em; font-weight: 700; width: 100%;
            cursor: pointer; margin-top: 20px;
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
            box-shadow: 0 8px 28px rgba(16,185,129,0.40);
            font-family: 'Inter', sans-serif;
        }
        .btn-proceed:hover        { transform: translateY(-2px); box-shadow: 0 14px 36px rgba(16,185,129,0.60); }
        .btn-proceed:disabled     { opacity: 0.40; filter: grayscale(0.7); cursor: not-allowed; transform: none; box-shadow: none; }

        /* ── Capture button ──────────────────────────────────── */
        .btn-capture {
            background: linear-gradient(135deg, rgba(37,99,235,0.45), rgba(99,102,241,0.45));
            color: #bfdbfe; border: 1px solid rgba(99,102,241,0.45);
            padding: 10px 15px; border-radius: 12px; font-weight: 700; cursor: pointer; width: 100%;
            transition: all 0.2s ease; backdrop-filter: blur(8px); font-family: 'Inter', sans-serif;
        }
        .btn-capture:hover  { background: linear-gradient(135deg, rgba(37,99,235,0.65), rgba(99,102,241,0.65)); transform: translateY(-1px); }
        .btn-capture:disabled { opacity: 0.50; cursor: not-allowed; transform: none; }
        .captured-value { font-size: 1.6em; font-weight: 900; color: #5eead4; text-align: center; display: block; margin-top: 8px; }

        /* ── Step header ─────────────────────────────────────── */
        .step-header {
            display: flex; align-items: center; gap: 14px; margin-bottom: 20px;
        }
        .step-badge {
            width: 36px; height: 36px; flex-shrink: 0;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            font-size: 1em; font-weight: 800; color: #fff;
            box-shadow: 0 6px 16px rgba(99,102,241,0.40);
        }
        .step-header h3 { margin: 0; color: #fff; font-size: 1.05em; font-weight: 700; }
        .step-header p  { margin: 2px 0 0; color: rgba(255,255,255,0.42); font-size: 0.83em; }

        /* ── Live feed display ───────────────────────────────── */
        .live-feed-box {
            display: flex; justify-content: space-between; gap: 10px;
            background: rgba(255,255,255,0.07); padding: 12px; border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.12); margin-bottom: 12px;
        }
        .live-feed-cell { text-align: center; flex: 1; }
        .live-feed-label {
            display: block; font-size: 0.70em; color: rgba(255,255,255,0.45);
            font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px;
        }
        .live-feed-value { font-size: 1.7em; font-weight: 900; color: #93c5fd; }
        .captured-feed-value { font-size: 1.7em; font-weight: 900; color: #5eead4; }
        .live-feed-divider { width: 1px; background: rgba(255,255,255,0.12); margin: 4px 0; }

        @media (max-width: 900px) {
            .setup-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">🚙 Setup Parking Test</div>
            <div class="profile">
                <div style="width:34px;height:34px;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.13);border-radius:10px;display:flex;align-items:center;justify-content:center;cursor:pointer;" title="Notifications">🔔</div>
                <div style="width:34px;height:34px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:0.9em;" title="Profile">👤</div>
            </div>
        </header>

        <section class="content-section">
            <div class="card animate-in" id="step1Card">
                <div class="step-header">
                    <div class="step-badge">1</div>
                    <div>
                        <h3>Verify Hardware Connection</h3>
                        <p>Checking that all sensor modules are online and responding.</p>
                    </div>
                </div>
                <div id="statusBanner" class="status-banner checking">
                    <span id="statusIcon">🔄</span>
                    <span id="statusText">Checking sensors via sensor.php...</span>
                </div>

                <div class="sensor-check-grid" style="grid-template-columns: 1fr;">
                    <div class="sensor-chip" id="chip_M">Main Sensor Module<br><small id="chipval_M">--</small></div>
                </div>
                
                <button class="action-btn" id="retryBtn" onclick="checkSensors()" style="display:none; background:#b91c1c;">🔁 Retry Connection</button>
            </div>

            <div class="card animate-in" id="step2Card" style="display:none;">
                <div class="step-header" style="margin-bottom:20px;">
                    <div class="step-badge">2</div>
                    <div>
                        <h3>Configure Test Parameters</h3>
                        <p>Assign sides, capture target distances, and lock each sensor.</p>
                    </div>
                </div>
                
                <?php if($session_id == 0 && count($students) == 0): ?>
                    <div class="msg-error">
                        ❌ No active students available. You must <a href="add_student.php" style="color:#fca5a5;">register a student</a> before testing.
                    </div>
                <?php else: ?>
                    <form method="POST" action="test_driver.php" id="setupForm">
                        <input type="hidden" name="session_id" value="<?= $session_id ?>">
                        
                        <?php if($session_id > 0): ?>
                            <input type="hidden" name="student_id" value="<?= $student_id ?>">
                            <div class="form-group" style="background:rgba(255,255,255,0.10); padding:15px; border-radius:14px; border:1px solid rgba(255,255,255,0.22);">
                                <label>Continuing Testing For Session #<?= $session_id ?></label>
                                <div style="font-size: 1.15em; font-weight: bold; color: #93c5fd; padding: 10px; background: rgba(255,255,255,0.08); border: 1px dashed rgba(147,197,253,0.50); border-radius: 10px;">
                                    👤 <?= $student_name ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="form-group" style="background:rgba(255,255,255,0.10); padding:15px; border-radius:14px; border:1px solid rgba(255,255,255,0.22);">
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
                            <div class="form-group" style="background:rgba(251,191,36,0.12); padding:20px; border-radius:14px; border:1px solid rgba(251,191,36,0.40); margin-bottom: 20px;">
                                <label style="font-weight:bold; color:#fde68a; font-size:1.05em;">🏅 Rapid Configuration: Load Saved Parking Standard</label>
                                <select id="global_preset_selector" class="form-control" style="font-size:1.05em; padding:10px;" onchange="loadGlobalPreset(this.value)">
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
                                <h4><span style="font-size:1.1em;">📡</span> <?= $title ?></h4>
                                <div class="form-group">
                                    <label>Assign to Side</label>
                                    <select name="side_<?= $hw_id ?>" id="side_<?= $hw_id ?>" required class="form-control side-selector" onchange="updateSideSelectors()" aria-label="Sensor <?= $hw_id ?> side">
                                        <option value="">-- Select Side --</option>
                                        <option value="None">No side</option>
                                        <option value="Left">Left Side</option>
                                        <option value="Front">Front</option>
                                        <option value="Right">Right Side</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Perfect Parking Distance</label>
                                    <div class="live-feed-box">
                                        <div class="live-feed-cell">
                                            <span class="live-feed-label">Live Feed</span>
                                            <span id="live_feed_<?= $hw_id ?>" class="live-feed-value">-- cm</span>
                                        </div>
                                        <div class="live-feed-divider"></div>
                                        <div class="live-feed-cell">
                                            <span class="live-feed-label">Captured</span>
                                            <span class="captured-feed-value" id="display_calib_<?= $hw_id ?>">--</span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-capture" id="btn_capture_<?= $hw_id ?>"
                                            onclick="captureBaseline('<?= $hw_id ?>')"
                                            aria-label="Capture sensor <?= $hw_id ?> baseline">
                                        📷 Capture Baseline
                                    </button>
                                    <input type="hidden" id="target_distance_<?= $hw_id ?>" name="target_distance_<?= $hw_id ?>" value="" required>
                                </div>
                                <div class="form-group">
                                    <label>Allowed Error (cm)</label>
                                    <input type="number" step="0.1" name="allowed_error_<?= $hw_id ?>" id="allowed_error_<?= $hw_id ?>" value="5" required class="form-control" aria-label="Allowed error for sensor <?= $hw_id ?>">
                                </div>
                                
                                <div style="display:flex; gap:10px; margin-top:14px;">
                                    <button type="button" id="btn_lock_<?= $hw_id ?>" class="action-btn" style="flex:1;" onclick="lockSensor('<?= $hw_id ?>')" aria-label="Lock sensor <?= $hw_id ?> calibration">🔒 Lock</button>
                                    <button type="button" id="btn_unlock_<?= $hw_id ?>" class="action-btn" style="flex:1; display:none; background:rgba(185,28,28,0.45);" onclick="unlockSensor('<?= $hw_id ?>')" aria-label="Unlock sensor <?= $hw_id ?>">🔓 Unlock</button>
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
