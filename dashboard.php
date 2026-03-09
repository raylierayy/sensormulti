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
</head>
<body>

<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</div>
            <div class="profile">
                <span title="Notifications">🔔</span>
                <span title="Profile">👤</span>
            </div>
        </header>

        <section class="dashboard">
            <div class="card">
                <h3>📋 Quick Actions</h3>
                <div class="nav-buttons">
                    <button onclick="window.location.href='add_student.php'">➕ Add New Student</button>
                    <button onclick="window.location.href='students.php'">👥 Manage Students & Scores</button>
                    <button onclick="window.location.href='calibrate.php'">🚗 Start New Test</button>
                </div>
            </div>

            <div class="card">
                <h3>ℹ️ System Status</h3>
                <p style="color:rgba(255,255,255,0.80);">Select an action from the sidebar or the quick actions above to manage students and conduct parking tests.</p>
            </div>
        </section>
    </main>
</div>

</body>
</html>
