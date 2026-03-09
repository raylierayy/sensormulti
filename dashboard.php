<?php
require 'session_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sensor System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="theme-modern.css">
</head>
<body>

<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <!-- Decorative orbs -->
        <div class="orb orb-purple" style="position:fixed; top:5%; right:10%; width:400px; height:400px; z-index:0; pointer-events:none;"></div>
        <div class="orb orb-cyan" style="position:fixed; bottom:10%; left:15%; width:350px; height:350px; z-index:0; pointer-events:none;"></div>

        <header class="topbar" style="position:relative; z-index:1;">
            <div class="welcome" style="font-size:1.1em; font-weight:700; display:flex; align-items:center; gap:10px;">
                <span style="font-size:1.3em;">👋</span>
                Welcome back, <span style="background: linear-gradient(135deg,#667eea,#f093fb); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
            <div class="profile" style="display:flex; gap:12px; align-items:center;">
                <div style="width:36px;height:36px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1em;cursor:pointer;transition:all 0.2s;" title="Notifications">🔔</div>
                <div style="width:36px;height:36px;background:linear-gradient(135deg,#667eea,#764ba2);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1em;cursor:pointer;box-shadow:0 4px 12px rgba(102,126,234,0.4);" title="Profile">👤</div>
            </div>
        </header>

        <section class="dashboard" style="position:relative; z-index:1;">
            <!-- Hero greeting -->
            <div class="glass-card-modern" style="padding:28px 32px; background: linear-gradient(135deg, rgba(102,126,234,0.15) 0%, rgba(118,75,162,0.15) 100%); border-color: rgba(102,126,234,0.3);">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
                    <div>
                        <h1 style="font-size:1.8em; font-weight:900; color:#fff; margin:0 0 6px; letter-spacing:-0.02em;">
                            Parking Aid <span style="background: linear-gradient(135deg,#667eea,#f093fb); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;">Sensor System</span>
                        </h1>
                        <p style="color:rgba(255,255,255,0.55); margin:0; font-size:0.95em;">Real-time parking sensor monitoring &amp; student management</p>
                    </div>
                    <div style="display:flex; gap:16px;">
                        <div style="text-align:center; padding:16px 24px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12); border-radius:16px;">
                            <div style="font-size:1.8em; font-weight:900; color:#667eea;">3</div>
                            <div style="font-size:0.75em; color:rgba(255,255,255,0.45); text-transform:uppercase; letter-spacing:0.05em;">Sensors</div>
                        </div>
                        <div style="text-align:center; padding:16px 24px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12); border-radius:16px;">
                            <div style="font-size:1.8em; font-weight:900; color:#10b981;">Live</div>
                            <div style="font-size:0.75em; color:rgba(255,255,255,0.45); text-transform:uppercase; letter-spacing:0.05em;">Status</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Bento Grid -->
            <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:20px;">
                <!-- Add Student -->
                <div class="glass-card-modern hover-lift" style="padding:28px; cursor:pointer;" onclick="window.location.href='add_student.php'">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#667eea,#764ba2);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.5em;margin-bottom:16px;box-shadow:0 8px 20px rgba(102,126,234,0.4);">➕</div>
                    <h3 style="color:#fff;margin:0 0 8px;font-size:1.1em;font-weight:700;">Add New Student</h3>
                    <p style="color:rgba(255,255,255,0.5);margin:0;font-size:0.85em;line-height:1.5;">Register a new student for parking tests</p>
                    <div style="margin-top:20px;">
                        <span style="display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:8px 18px;border-radius:10px;font-size:0.82em;font-weight:700;box-shadow:0 4px 12px rgba(102,126,234,0.35);">Get Started →</span>
                    </div>
                </div>
                
                <!-- Manage Students -->
                <div class="glass-card-modern hover-lift" style="padding:28px; cursor:pointer;" onclick="window.location.href='students.php'">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#06b6d4,#0891b2);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.5em;margin-bottom:16px;box-shadow:0 8px 20px rgba(6,182,212,0.4);">👥</div>
                    <h3 style="color:#fff;margin:0 0 8px;font-size:1.1em;font-weight:700;">Manage Students</h3>
                    <p style="color:rgba(255,255,255,0.5);margin:0;font-size:0.85em;line-height:1.5;">View scores, grades, and student history</p>
                    <div style="margin-top:20px;">
                        <span style="display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#06b6d4,#0891b2);color:#fff;padding:8px 18px;border-radius:10px;font-size:0.82em;font-weight:700;box-shadow:0 4px 12px rgba(6,182,212,0.35);">View All →</span>
                    </div>
                </div>
                
                <!-- Start Test -->
                <div class="glass-card-modern hover-lift" style="padding:28px; cursor:pointer; background:linear-gradient(135deg, rgba(16,185,129,0.12) 0%, rgba(6,182,212,0.08) 100%); border-color:rgba(16,185,129,0.3);" onclick="window.location.href='calibrate.php'">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#10b981,#06b6d4);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.5em;margin-bottom:16px;box-shadow:0 8px 20px rgba(16,185,129,0.4);">🚗</div>
                    <h3 style="color:#fff;margin:0 0 8px;font-size:1.1em;font-weight:700;">Start New Test</h3>
                    <p style="color:rgba(255,255,255,0.5);margin:0;font-size:0.85em;line-height:1.5;">Set up sensors and conduct a parking test</p>
                    <div style="margin-top:20px;">
                        <span style="display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#10b981,#06b6d4);color:#fff;padding:8px 18px;border-radius:10px;font-size:0.82em;font-weight:700;box-shadow:0 4px 12px rgba(16,185,129,0.35);">Start Now →</span>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="glass-card-modern" style="padding:24px 28px;">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:14px;">
                        <div style="width:10px;height:10px;background:#10b981;border-radius:50%;box-shadow:0 0 12px rgba(16,185,129,0.8);animation:pulse-glow 2s infinite;"></div>
                        <div>
                            <div style="font-weight:700; color:#fff; font-size:0.95em;">ℹ️ System Status</div>
                            <div style="font-size:0.82em; color:rgba(255,255,255,0.5); margin-top:2px;">All systems operational. Select an action to begin.</div>
                        </div>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <span style="background:rgba(16,185,129,0.15);color:#4ade80;border:1px solid rgba(74,222,128,0.3);padding:5px 14px;border-radius:999px;font-size:0.78em;font-weight:700;">● Online</span>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

</body>
</html>
