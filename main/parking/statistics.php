<?php
$stats = [
  "parkingSpaces" => [
    "available" => 45,
    "occupied" => 55,
    "reserved" => 10,
    "total" => 110
  ],
  "peakHours" => [
    "08:00" => 78,
    "09:00" => 85,
    "10:00" => 92,
    "11:00" => 88,
    "12:00" => 95
  ],
  "averageStayTime" => 45,
  "monthlyRevenue" => 15420,
  "topLocations" => [
    ["name" => "Zone A - Main", "usage" => 94],
    ["name" => "Zone B - Side", "usage" => 87],
    ["name" => "Zone C - Back", "usage" => 72],
    ["name" => "Zone D - Roof", "usage" => 58]
  ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Parking Statistics - Parking Aid</title>
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

    .stats-container {
      max-width: 1200px;
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

    .header {
      text-align: center;
      color: white;
      margin-bottom: 40px;
    }

    .header h1 {
      font-size: 2.5em;
      margin-bottom: 10px;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-card {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .stat-icon {
      font-size: 2.5em;
      margin-bottom: 10px;
    }

    .stat-label {
      font-size: 0.9em;
      color: #666;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 10px;
    }

    .stat-value {
      font-size: 2.2em;
      font-weight: bold;
      color: #1e3a8a;
      margin-bottom: 8px;
    }

    .stat-subtext {
      font-size: 0.85em;
      color: #999;
    }

    .parking-status {
      background: white;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }

    .parking-status h2 {
      color: #1e3a8a;
      margin-bottom: 25px;
      border-bottom: 2px solid #e5e7eb;
      padding-bottom: 10px;
    }

    .parking-spaces {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 15px;
      margin-bottom: 30px;
    }

    .space-item {
      background: #f9fafb;
      border-radius: 8px;
      padding: 15px;
      text-align: center;
      border-left: 4px solid #1e3a8a;
    }

    .space-label {
      font-size: 0.85em;
      color: #666;
      margin-bottom: 8px;
    }

    .space-value {
      font-size: 1.8em;
      font-weight: 600;
      color: #1e3a8a;
    }

    .space-percent {
      font-size: 0.8em;
      color: #999;
      margin-top: 5px;
    }

    .peak-hours {
      background: white;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }

    .peak-hours h2 {
      color: #1e3a8a;
      margin-bottom: 25px;
      border-bottom: 2px solid #e5e7eb;
      padding-bottom: 10px;
    }

    .chart {
      display: flex;
      align-items: flex-end;
      gap: 15px;
      height: 250px;
    }

    .chart-bar {
      flex: 1;
      background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
      border-radius: 5px 5px 0 0;
      position: relative;
      transition: transform 0.3s ease;
      display: flex;
      align-items: flex-end;
      justify-content: center;
    }

    .chart-bar:hover {
      transform: scaleY(1.1);
    }

    .chart-bar-label {
      position: absolute;
      bottom: -25px;
      width: 100%;
      text-align: center;
      font-size: 0.85em;
      color: #666;
    }

    .chart-bar-value {
      color: white;
      font-weight: 600;
      margin-bottom: 8px;
    }

    .top-locations {
      background: white;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .top-locations h2 {
      color: #1e3a8a;
      margin-bottom: 25px;
      border-bottom: 2px solid #e5e7eb;
      padding-bottom: 10px;
    }

    .location-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 15px 0;
      border-bottom: 1px solid #e5e7eb;
    }

    .location-item:last-child {
      border-bottom: none;
    }

    .location-name {
      font-weight: 500;
      color: #333;
    }

    .location-bar {
      flex: 1;
      height: 20px;
      background: #e5e7eb;
      border-radius: 10px;
      margin: 0 15px;
      overflow: hidden;
    }

    .location-fill {
      height: 100%;
      background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
      border-radius: 10px;
      transition: width 0.3s ease;
    }

    .location-percent {
      font-weight: 600;
      color: #1e3a8a;
      min-width: 50px;
      text-align: right;
    }
  </style>
</head>
<body>
  <div class="stats-container">
    <a href="mainpage.php" class="back-btn">← Back to Main</a>

    <div class="header">
      <h1>📊 Parking Statistics</h1>
      <p>Real-time parking lot usage and analytics</p>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">🅿️</div>
        <div class="stat-label">Available Spaces</div>
        <div class="stat-value"><?php echo $stats['parkingSpaces']['available']; ?></div>
        <div class="stat-subtext">Out of <?php echo $stats['parkingSpaces']['total']; ?> spaces</div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">🚗</div>
        <div class="stat-label">Currently Occupied</div>
        <div class="stat-value"><?php echo $stats['parkingSpaces']['occupied']; ?></div>
        <div class="stat-subtext"><?php echo round(($stats['parkingSpaces']['occupied']/$stats['parkingSpaces']['total'])*100); ?>% occupancy</div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">⏱️</div>
        <div class="stat-label">Avg. Stay Time</div>
        <div class="stat-value"><?php echo $stats['averageStayTime']; ?>m</div>
        <div class="stat-subtext">Average minutes parked</div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-label">Monthly Revenue</div>
        <div class="stat-value">$<?php echo number_format($stats['monthlyRevenue']); ?></div>
        <div class="stat-subtext">This month's total</div>
      </div>
    </div>

    <div class="parking-status">
      <h2>Parking Space Status</h2>
      <div class="parking-spaces">
        <div class="space-item">
          <div class="space-label">Available</div>
          <div class="space-value" style="color: #10b981;"><?php echo $stats['parkingSpaces']['available']; ?></div>
          <div class="space-percent"><?php echo round(($stats['parkingSpaces']['available']/$stats['parkingSpaces']['total'])*100); ?>%</div>
        </div>
        <div class="space-item">
          <div class="space-label">Occupied</div>
          <div class="space-value" style="color: #f59e0b;"><?php echo $stats['parkingSpaces']['occupied']; ?></div>
          <div class="space-percent"><?php echo round(($stats['parkingSpaces']['occupied']/$stats['parkingSpaces']['total'])*100); ?>%</div>
        </div>
        <div class="space-item">
          <div class="space-label">Reserved</div>
          <div class="space-value" style="color: #8b5cf6;"><?php echo $stats['parkingSpaces']['reserved']; ?></div>
          <div class="space-percent"><?php echo round(($stats['parkingSpaces']['reserved']/$stats['parkingSpaces']['total'])*100); ?>%</div>
        </div>
        <div class="space-item">
          <div class="space-label">Total Spaces</div>
          <div class="space-value" style="color: #1e3a8a;"><?php echo $stats['parkingSpaces']['total']; ?></div>
          <div class="space-percent">100%</div>
        </div>
      </div>
    </div>

    <div class="peak-hours">
      <h2>Peak Hours Today</h2>
      <div class="chart">
        <?php foreach ($stats['peakHours'] as $time => $occupancy): ?>
          <div class="chart-bar" style="height: <?php echo ($occupancy/100)*100; ?>%;">
            <div class="chart-bar-value"><?php echo $occupancy; ?>%</div>
            <div class="chart-bar-label"><?php echo $time; ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="top-locations">
      <h2>Zone Usage Comparison</h2>
      <?php foreach ($stats['topLocations'] as $location): ?>
        <div class="location-item">
          <div class="location-name"><?php echo htmlspecialchars($location['name']); ?></div>
          <div class="location-bar">
            <div class="location-fill" style="width: <?php echo $location['usage']; ?>%;"></div>
          </div>
          <div class="location-percent"><?php echo $location['usage']; ?>%</div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
