<?php
$lessons = [
  [
    "id" => 1,
    "title" => "Parallel Parking Basics",
    "description" => "Learn the fundamental techniques for parallel parking",
    "duration" => 15,
    "difficulty" => "Beginner",
    "completed" => true,
    "videoLink" => "#"
  ],
  [
    "id" => 2,
    "title" => "Perpendicular Parking",
    "description" => "Master the technique of perpendicular parking",
    "duration" => 20,
    "difficulty" => "Intermediate",
    "completed" => true,
    "videoLink" => "#"
  ],
  [
    "id" => 3,
    "title" => "Angle Parking Techniques",
    "description" => "Learn how to park at various angles",
    "duration" => 18,
    "difficulty" => "Intermediate",
    "completed" => false,
    "videoLink" => "#"
  ],
  [
    "id" => 4,
    "title" => "Parking Under Difficult Conditions",
    "description" => "Parking techniques in rain, snow, and tight spaces",
    "duration" => 25,
    "difficulty" => "Advanced",
    "completed" => false,
    "videoLink" => "#"
  ],
  [
    "id" => 5,
    "title" => "Traffic Awareness",
    "description" => "Understand traffic flow and safe parking practices",
    "duration" => 12,
    "difficulty" => "Beginner",
    "completed" => true,
    "videoLink" => "#"
  ],
  [
    "id" => 6,
    "title" => "Sensor & Camera Usage",
    "description" => "Effectively use parking sensors and backup cameras",
    "duration" => 10,
    "difficulty" => "Beginner",
    "completed" => true,
    "videoLink" => "#"
  ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Lessons - Parking Aid</title>
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

    .lessons-container {
      max-width: 1200px;
      margin: 0 auto;
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

    .filter-section {
      background: white;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 30px;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .filter-btn {
      padding: 8px 16px;
      border: 2px solid #e5e7eb;
      background: white;
      border-radius: 5px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 0.95em;
    }

    .filter-btn.active {
      background: #1e3a8a;
      color: white;
      border-color: #1e3a8a;
    }

    .filter-btn:hover {
      border-color: #1e3a8a;
    }

    .lessons-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
      gap: 25px;
      margin-bottom: 30px;
    }

    .lesson-card {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      display: flex;
      flex-direction: column;
    }

    .lesson-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .lesson-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 20px;
      position: relative;
    }

    .lesson-title {
      font-size: 1.3em;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .lesson-meta {
      display: flex;
      gap: 15px;
      font-size: 0.9em;
      opacity: 0.9;
    }

    .badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.8em;
      font-weight: 600;
    }

    .badge.beginner {
      background: #d1fae5;
      color: #065f46;
    }

    .badge.intermediate {
      background: #fef3c7;
      color: #92400e;
    }

    .badge.advanced {
      background: #fee2e2;
      color: #7f1d1d;
    }

    .badge.completed {
      background: #d1fae5;
      color: #065f46;
      position: absolute;
      top: 15px;
      right: 15px;
    }

    .lesson-content {
      padding: 20px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .lesson-description {
      color: #666;
      margin-bottom: 15px;
      font-size: 0.95em;
      line-height: 1.5;
    }

    .lesson-stats {
      display: flex;
      gap: 20px;
      margin-bottom: 15px;
      padding-top: 15px;
      border-top: 1px solid #e5e7eb;
      font-size: 0.9em;
      color: #666;
    }

    .stat {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .progress-indicator {
      height: 4px;
      background: #e5e7eb;
      border-radius: 2px;
      margin-bottom: 15px;
    }

    .progress-indicator.completed {
      background: #10b981;
    }

    .lesson-footer {
      margin-top: auto;
    }

    .btn {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-primary {
      background: linear-gradient(135deg, #1e3a8a 0%, #2e4fa2 100%);
      color: white;
    }

    .btn-primary:hover {
      transform: scale(1.02);
      box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
    }

    .btn-secondary {
      background: #e5e7eb;
      color: #333;
    }

    .btn-secondary:hover {
      background: #d1d5db;
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
  </style>
</head>
<body>
  <div class="lessons-container">
    <a href="mainpage.php" class="back-btn">← Back to Main</a>

    <div class="header">
      <h1>📚 Parking Lessons</h1>
      <p>Master parking techniques with our comprehensive lessons</p>
    </div>

    <div class="filter-section">
      <button class="filter-btn active">All Lessons</button>
      <button class="filter-btn">Beginner</button>
      <button class="filter-btn">Intermediate</button>
      <button class="filter-btn">Advanced</button>
      <button class="filter-btn">Completed</button>
    </div>

    <div class="lessons-grid">
      <?php foreach ($lessons as $lesson): ?>
        <div class="lesson-card">
          <div class="lesson-header">
            <div class="lesson-title"><?php echo htmlspecialchars($lesson['title']); ?></div>
            <?php if ($lesson['completed']): ?>
              <span class="badge completed">✓ Completed</span>
            <?php endif; ?>
            <div class="lesson-meta">
              <span>⏱️ <?php echo $lesson['duration']; ?> min</span>
            </div>
          </div>

          <div class="lesson-content">
            <?php if (!$lesson['completed']): ?>
              <div class="progress-indicator"></div>
            <?php else: ?>
              <div class="progress-indicator completed"></div>
            <?php endif; ?>

            <p class="lesson-description">
              <?php echo htmlspecialchars($lesson['description']); ?>
            </p>

            <div class="lesson-stats">
              <span class="badge <?php echo strtolower($lesson['difficulty']); ?>">
                <?php echo $lesson['difficulty']; ?>
              </span>
            </div>

            <div class="lesson-footer">
              <button class="btn btn-primary">
                <?php echo $lesson['completed'] ? 'Review Lesson' : 'Start Lesson'; ?>
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
