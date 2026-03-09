<?php
// User profile data
$user = [
  "name" => "Juan Dela Cruz",
  "email" => "juan@example.com",
  "phone" => "09171234567",
  "joinDate" => "2024-09-15",
  "totalHours" => 24.5,
  "lessonsCompleted" => 7,
  "averageScore" => 85.5,
  "licenseStatus" => "Learning",
  "instructor" => "Mr. Robert Santos"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Profile - Parking Aid</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding: 20px;
    }

    .profile-container {
      max-width: 900px;
      margin: 0 auto;
    }

    .back-btn {
      display: inline-block;
      margin-bottom: 20px;
      padding: 10px 20px;
      background: white;
      color: #1e3a8a;
      text-decoration: none;
      border-radius: 5px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .back-btn:hover {
      transform: translateX(-5px);
    }

    .profile-card {
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
      margin-bottom: 30px;
    }

    .profile-header {
      background: linear-gradient(135deg, #1e3a8a 0%, #2e4fa2 100%);
      color: white;
      padding: 40px 20px;
      text-align: center;
    }

    .profile-avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: rgba(255,255,255,0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 50px;
      margin: 0 auto 20px;
      border: 3px solid white;
    }

    .profile-name {
      font-size: 2em;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .profile-status {
      font-size: 1.1em;
      opacity: 0.9;
    }

    .profile-body {
      padding: 40px;
    }

    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 30px;
      margin-bottom: 40px;
    }

    .info-item {
      padding: 20px;
      background: #f9fafb;
      border-radius: 10px;
      border-left: 4px solid #1e3a8a;
    }

    .info-label {
      font-size: 0.85em;
      color: #666;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 8px;
    }

    .info-value {
      font-size: 1.5em;
      font-weight: 600;
      color: #1e3a8a;
    }

    .section {
      margin-bottom: 40px;
    }

    .section-title {
      font-size: 1.3em;
      font-weight: 600;
      margin-bottom: 20px;
      color: #1e3a8a;
      border-bottom: 2px solid #e5e7eb;
      padding-bottom: 10px;
    }

    .settings-group {
      background: #f9fafb;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 15px;
    }

    .setting-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 0;
      border-bottom: 1px solid #e5e7eb;
    }

    .setting-item:last-child {
      border-bottom: none;
    }

    .setting-label {
      font-weight: 500;
      color: #333;
    }

    .toggle-switch {
      width: 50px;
      height: 28px;
      background: #ccc;
      border-radius: 14px;
      position: relative;
      cursor: pointer;
      transition: background 0.3s;
    }

    .toggle-switch.active {
      background: #10b981;
    }

    .toggle-switch::after {
      content: '';
      position: absolute;
      width: 24px;
      height: 24px;
      background: white;
      border-radius: 50%;
      top: 2px;
      left: 2px;
      transition: left 0.3s;
    }

    .toggle-switch.active::after {
      left: 24px;
    }

    .action-buttons {
      display: flex;
      gap: 15px;
      margin-top: 30px;
    }

    .btn {
      flex: 1;
      padding: 12px 20px;
      border: none;
      border-radius: 5px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-primary {
      background: linear-gradient(135deg, #1e3a8a 0%, #2e4fa2 100%);
      color: white;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(30, 58, 138, 0.3);
    }

    .btn-secondary {
      background: #e5e7eb;
      color: #333;
    }

    .btn-secondary:hover {
      background: #d1d5db;
    }

    .btn-danger {
      background: #f87171;
      color: white;
    }

    .btn-danger:hover {
      background: #ef4444;
    }

    .stats-row {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      margin-top: 20px;
    }

    .stat-box {
      background: white;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 15px;
      text-align: center;
    }

    .stat-box-label {
      font-size: 0.9em;
      color: #666;
      margin-bottom: 5px;
    }

    .stat-box-value {
      font-size: 1.5em;
      font-weight: 600;
      color: #1e3a8a;
    }
  </style>
</head>
<body>
  <div class="profile-container">
    <a href="mainpage.php" class="back-btn">← Back to Main</a>

    <div class="profile-card">
      <div class="profile-header">
        <div class="profile-avatar">👤</div>
        <div class="profile-name"><?php echo htmlspecialchars($user['name']); ?></div>
        <div class="profile-status"><?php echo $user['licenseStatus']; ?> Driver • Assigned to <?php echo $user['instructor']; ?></div>
      </div>

      <div class="profile-body">
        <div class="info-grid">
          <div class="info-item">
            <div class="info-label">Email</div>
            <div class="info-value" style="font-size: 1.1em; color: #666;"><?php echo htmlspecialchars($user['email']); ?></div>
          </div>
          <div class="info-item">
            <div class="info-label">Phone</div>
            <div class="info-value" style="font-size: 1.1em; color: #666;"><?php echo htmlspecialchars($user['phone']); ?></div>
          </div>
          <div class="info-item">
            <div class="info-label">Member Since</div>
            <div class="info-value" style="font-size: 1.1em; color: #666;"><?php echo date('M d, Y', strtotime($user['joinDate'])); ?></div>
          </div>
          <div class="info-item">
            <div class="info-label">License Status</div>
            <div class="info-value" style="color: #f59e0b;"><?php echo $user['licenseStatus']; ?></div>
          </div>
        </div>

        <div class="section">
          <div class="section-title">Learning Statistics</div>
          <div class="stats-row">
            <div class="stat-box">
              <div class="stat-box-label">Total Hours</div>
              <div class="stat-box-value"><?php echo $user['totalHours']; ?> hrs</div>
            </div>
            <div class="stat-box">
              <div class="stat-box-label">Lessons Completed</div>
              <div class="stat-box-value"><?php echo $user['lessonsCompleted']; ?>/12</div>
            </div>
            <div class="stat-box">
              <div class="stat-box-label">Average Score</div>
              <div class="stat-box-value"><?php echo $user['averageScore']; ?>%</div>
            </div>
            <div class="stat-box">
              <div class="stat-box-label">Rank</div>
              <div class="stat-box-value">#12/150</div>
            </div>
          </div>
        </div>

        <div class="section">
          <div class="section-title">Account Settings</div>
          <div class="settings-group">
            <div class="setting-item">
              <span class="setting-label">Email Notifications</span>
              <div class="toggle-switch active"></div>
            </div>
            <div class="setting-item">
              <span class="setting-label">SMS Alerts</span>
              <div class="toggle-switch"></div>
            </div>
            <div class="setting-item">
              <span class="setting-label">Practice Reminders</span>
              <div class="toggle-switch active"></div>
            </div>
            <div class="setting-item">
              <span class="setting-label">Dark Mode</span>
              <div class="toggle-switch"></div>
            </div>
          </div>
        </div>

        <div class="action-buttons">
          <button class="btn btn-primary">Edit Profile</button>
          <button class="btn btn-secondary">Change Password</button>
          <button class="btn btn-danger">Logout</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.querySelectorAll('.toggle-switch').forEach(toggle => {
      toggle.addEventListener('click', function() {
        this.classList.toggle('active');
      });
    });
  </script>
</body>
</html>
