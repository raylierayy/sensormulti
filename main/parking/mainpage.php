<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Parking Aid Management</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f9ff;
      color: #333;
      overflow-x: hidden;
    }

    .container {
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 260px;
      background: linear-gradient(180deg, #1e3a8a 0%, #2e4fa2 100%);
      color: white;
      padding: 20px 15px;
      position: fixed;
      height: 100vh;
      overflow-y: auto;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      z-index: 1000;
    }

    .sidebar h2 {
      margin-bottom: 30px;
      font-size: 1.5em;
      text-align: center;
      border-bottom: 2px solid rgba(255,255,255,0.2);
      padding-bottom: 15px;
    }

    .sidebar nav {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .sidebar nav a, .sidebar nav form button {
      display: block;
      color: white;
      text-decoration: none;
      padding: 12px 15px;
      border-radius: 6px;
      transition: all 0.3s ease;
      cursor: pointer;
      border: none;
      background: transparent;
      text-align: left;
      font-size: 0.95em;
      font-weight: 500;
    }

    .sidebar nav a:hover, .sidebar nav form button:hover {
      background-color: rgba(255,255,255,0.15);
      padding-left: 20px;
    }

    .sidebar nav a:before {
      margin-right: 10px;
    }

    .sidebar .logout-btn {
      background-color: rgba(239, 68, 68, 0.3) !important;
      color: #fca5a5;
      margin-top: auto;
      border: 1px solid rgba(239, 68, 68, 0.5);
    }

    .sidebar .logout-btn:hover {
      background-color: rgba(239, 68, 68, 0.5) !important;
      color: white;
    }

    .main-content {
      flex: 1;
      margin-left: 260px;
      padding: 20px;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .welcome {
      font-size: 1.3em;
      font-weight: 600;
      color: #1e3a8a;
    }

    .profile {
      display: flex;
      gap: 20px;
      font-size: 1.3em;
      cursor: pointer;
    }

    .profile span:hover {
      transform: scale(1.2);
      transition: 0.2s;
    }

    .dashboard {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .card {
      background-color: white;
      border-radius: 10px;
      padding: 25px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      border: 1px solid #e5e7eb;
    }

    .card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .card h3 {
      margin-top: 0;
      color: #1e3a8a;
      margin-bottom: 15px;
    }

    .nav-buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-bottom: 0 !important;
    }

    .nav-buttons button {
      padding: 12px 24px;
      background: linear-gradient(135deg, #1e3a8a 0%, #2e4fa2 100%);
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
      flex: 1;
      min-width: 140px;
    }

    .nav-buttons button:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 80px;
        padding: 10px 5px;
      }

      .sidebar h2 {
        font-size: 1em;
        padding: 10px 0;
        margin-bottom: 20px;
      }

      .sidebar nav a, .sidebar nav form button {
        padding: 10px 5px;
        font-size: 0.75em;
        text-align: center;
      }

      .sidebar nav a:hover, .sidebar nav form button:hover {
        padding-left: 5px;
      }

      .main-content {
        margin-left: 80px;
        padding: 15px;
      }

      .nav-buttons {
        flex-direction: column;
      }

      .nav-buttons button {
        min-width: auto;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <h2>🅿️ Parking Aid</h2>
      <nav>
        <a href="dashboard.php">📊 Dashboard</a>
        <a href="lessons.php">📚 Lessons</a>
        <a href="practice.php">🎯 Practice</a>
        <a href="statistics.php">📈 Statistics</a>
        <a href="profile.php">👤 Profile</a>
        <a href="help.php">❓ Help</a>
        <form method="POST" action="">
          <button type="submit" class="logout-btn">🚪 Logout</button>
        </form>
      </nav>
    </aside>

    <main class="main-content">
      <header class="topbar">
        <div class="welcome">Welcome, Student Driver 👋</div>
        <div class="profile">
          <span title="Notifications">🔔</span>
          <span title="Profile" onclick="window.location.href='profile.php'">👤</span>
        </div>
      </header>

      <section class="dashboard">
        <div class="card">
          <h3>📋 Quick Actions</h3>
          <div class="nav-buttons">
            <button onclick="window.location.href='studentlist.php'">Student List</button>
            <button onclick="window.location.href='practice.php'">Start Practice</button>
            <button onclick="window.location.href='lessons.php'">View Lessons</button>
            <button onclick="window.location.href='dashboard.php'">My Progress</button>
          </div>
        </div>

        <div class="card">
          <h3>🎯 Your Learning Journey</h3>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
              <div style="font-size: 0.9em; color: #666; margin-bottom: 5px;">Lessons Completed</div>
              <div style="font-size: 2em; font-weight: bold; color: #1e3a8a;">7/12</div>
              <progress value="58" max="100" style="width: 100%; height: 6px; border-radius: 3px;"></progress>
            </div>
            <div>
              <div style="font-size: 0.9em; color: #666; margin-bottom: 5px;">Average Score</div>
              <div style="font-size: 2em; font-weight: bold; color: #10b981;">85.5%</div>
              <progress value="85" max="100" style="width: 100%; height: 6px; border-radius: 3px;"></progress>
            </div>
            <div>
              <div style="font-size: 0.9em; color: #666; margin-bottom: 5px;">Practice Hours</div>
              <div style="font-size: 2em; font-weight: bold; color: #f59e0b;">24.5h</div>
              <progress value="50" max="100" style="width: 100%; height: 6px; border-radius: 3px;"></progress>
            </div>
          </div>
        </div>

        <div class="card">
          <h3>📊 Recent Activity</h3>
          <ul style="list-style: none; margin: 0;">
            <li style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">✅ Completed "Parallel Parking Basics" - <span style="color: #999;">2 hours ago</span></li>
            <li style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">📝 Scored 92% in "Sensor Usage Quiz" - <span style="color: #999;">5 hours ago</span></li>
            <li style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">🎯 Completed practice session - <span style="color: #999;">Yesterday</span></li>
            <li style="padding: 10px 0;">💬 Instructor gave feedback on performance - <span style="color: #999;">2 days ago</span></li>
          </ul>
        </div>
      </section>
    </main>
  </div>
</body>
</html>