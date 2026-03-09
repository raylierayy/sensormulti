<?php
// Analytics Dashboard
$stats = [
  "totalLessons" => 12,
  "completedLessons" => 7,
  "averageScore" => 85.5,
  "practiceHours" => 24.5,
  "parkingAttempts" => 45,
  "successRate" => 92
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard - Parking Aid</title>
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

    .dashboard-container {
      max-width: 1200px;
      margin: 0 auto;
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }

    .dashboard-header {
      background: linear-gradient(135deg, #1e3a8a 0%, #2e4fa2 100%);
      color: white;
      padding: 40px 20px;
      text-align: center;
    }

    .dashboard-header h1 {
      font-size: 2.5em;
      margin-bottom: 10px;
    }

    .dashboard-header p {
      font-size: 1.1em;
      opacity: 0.9;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
      padding: 30px;
    }

    .stat-card {
      background: white;
      border-radius: 10px;
      padding: 25px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      border-left: 5px solid #1e3a8a;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .stat-card.active {
      border-left-color: #10b981;
    }

    .stat-card.warning {
      border-left-color: #f59e0b;
    }

    .stat-card.success {
      border-left-color: #10b981;
    }

    .stat-label {
      font-size: 0.9em;
      color: #666;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 10px;
    }

    .stat-value {
      font-size: 2.5em;
      font-weight: bold;
      color: #1e3a8a;
      margin-bottom: 10px;
    }

    .stat-subtext {
      font-size: 0.9em;
      color: #999;
    }

    .progress-bar {
      width: 100%;
      height: 8px;
      background: #e5e7eb;
      border-radius: 4px;
      overflow: hidden;
      margin-top: 15px;
    }

    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #1e3a8a, #2e4fa2);
      border-radius: 4px;
      transition: width 0.3s ease;
    }

    .charts-section {
      padding: 30px;
      background: #f9fafb;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
    }

    .chart-container {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .chart-title {
      font-size: 1.2em;
      font-weight: 600;
      margin-bottom: 20px;
      color: #1e3a8a;
    }

    .simple-chart {
      display: flex;
      align-items: flex-end;
      gap: 10px;
      height: 200px;
    }

    .bar {
      flex: 1;
      background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
      border-radius: 5px 5px 0 0;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      color: white;
      font-weight: bold;
      transition: transform 0.3s ease;
    }

    .bar:hover {
      transform: scaleY(1.1);
    }

    .action-section {
      padding: 30px;
      text-align: center;
    }

    .action-buttons {
      display: flex;
      gap: 15px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn {
      padding: 12px 30px;
      border: none;
      border-radius: 5px;
      font-size: 1em;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
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
  </style>
</head>
<body>
  <div class="dashboard-container">
    <div class="dashboard-header">
      <h1>📊 Performance Dashboard</h1>
      <p>Track your parking learning progress</p>
    </div>

    <div class="stats-grid">
      <div class="stat-card success">
        <div class="stat-label">Completed Lessons</div>
        <div class="stat-value"><?php echo $stats['completedLessons']; ?>/<?php echo $stats['totalLessons']; ?></div>
        <div class="progress-bar">
          <div class="progress-fill" style="width: <?php echo ($stats['completedLessons']/$stats['totalLessons'])*100; ?>%"></div>
        </div>
        <div class="stat-subtext">Keep practicing to master all lessons</div>
      </div>

      <div class="stat-card success">
        <div class="stat-label">Average Score</div>
        <div class="stat-value"><?php echo $stats['averageScore']; ?>%</div>
        <div class="progress-bar">
          <div class="progress-fill" style="width: <?php echo $stats['averageScore']; ?>%"></div>
        </div>
        <div class="stat-subtext">Excellent performance! 🎉</div>
      </div>

      <div class="stat-card active">
        <div class="stat-label">Practice Hours</div>
        <div class="stat-value"><?php echo $stats['practiceHours']; ?>h</div>
        <div class="stat-subtext">Total time invested in learning</div>
      </div>

      <div class="stat-card success">
        <div class="stat-label">Success Rate</div>
        <div class="stat-value"><?php echo $stats['successRate']; ?>%</div>
        <div class="progress-bar">
          <div class="progress-fill" style="width: <?php echo $stats['successRate']; ?>%"></div>
        </div>
        <div class="stat-subtext">Outstanding parking accuracy</div>
      </div>

      <div class="stat-card warning">
        <div class="stat-label">Parking Attempts</div>
        <div class="stat-value"><?php echo $stats['parkingAttempts']; ?></div>
        <div class="stat-subtext">Practice makes perfect</div>
      </div>

      <div class="stat-card">
        <div class="stat-label">Rank</div>
        <div class="stat-value">#12</div>
        <div class="stat-subtext">Out of 150 students</div>
      </div>
    </div>

    <div class="charts-section">
      <div class="chart-container">
        <div class="chart-title">Weekly Progress</div>
        <div class="simple-chart">
          <div class="bar" style="height: 40%; font-size: 0.8em;">12</div>
          <div class="bar" style="height: 60%; font-size: 0.8em;">18</div>
          <div class="bar" style="height: 70%; font-size: 0.8em;">21</div>
          <div class="bar" style="height: 55%; font-size: 0.8em;">16</div>
          <div class="bar" style="height: 80%; font-size: 0.8em;">24</div>
        </div>
      </div>

      <div class="chart-container">
        <div class="chart-title">Skill Level</div>
        <ul style="list-style: none; padding: 0;">
          <li style="margin: 15px 0;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
              <span>Parallel Parking</span>
              <span style="color: #1e3a8a; font-weight: bold;">95%</span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width: 95%;"></div>
            </div>
          </li>
          <li style="margin: 15px 0;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
              <span>Perpendicular Parking</span>
              <span style="color: #1e3a8a; font-weight: bold;">88%</span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width: 88%;"></div>
            </div>
          </li>
          <li style="margin: 15px 0;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
              <span>Angle Parking</span>
              <span style="color: #1e3a8a; font-weight: bold;">75%</span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width: 75%;"></div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <div class="action-section">
      <div class="action-buttons">
        <button class="btn btn-primary" onclick="window.location.href='mainpage.php'">Back to Main</button>
        <button class="btn btn-secondary" onclick="window.location.href='lessons.php'">View Lessons</button>
        <button class="btn btn-primary" onclick="window.location.href='practice.php'">Start Practice</button>
      </div>
    </div>
  </div>
</body>
</html>
