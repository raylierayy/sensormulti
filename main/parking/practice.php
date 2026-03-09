<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Parking Practice - Parking Aid</title>
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

    .practice-container {
      max-width: 1100px;
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

    .practice-modes {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
      margin-bottom: 40px;
    }

    .mode-card {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      display: flex;
      flex-direction: column;
    }

    .mode-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .mode-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 25px;
      text-align: center;
    }

    .mode-icon {
      font-size: 3em;
      margin-bottom: 10px;
    }

    .mode-title {
      font-size: 1.3em;
      font-weight: 600;
    }

    .mode-body {
      padding: 20px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .mode-description {
      color: #666;
      margin-bottom: 15px;
      flex: 1;
      font-size: 0.95em;
      line-height: 1.6;
    }

    .mode-stats {
      display: flex;
      gap: 15px;
      margin-bottom: 15px;
      padding-top: 15px;
      border-top: 1px solid #e5e7eb;
      font-size: 0.85em;
      color: #666;
    }

    .stat-badge {
      background: #f0f4f8;
      padding: 5px 10px;
      border-radius: 5px;
    }

    .btn {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 5px;
      background: linear-gradient(135deg, #1e3a8a 0%, #2e4fa2 100%);
      color: white;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn:hover {
      transform: scale(1.02);
      box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
    }

    .scenario-section {
      background: white;
      border-radius: 12px;
      padding: 30px;
      margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .scenario-title {
      color: #1e3a8a;
      font-size: 1.5em;
      margin-bottom: 20px;
      border-bottom: 2px solid #e5e7eb;
      padding-bottom: 10px;
    }

    .scenario-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }

    .scenario-item {
      background: #f9fafb;
      border-radius: 8px;
      padding: 20px;
      border-left: 4px solid #667eea;
      transition: all 0.3s ease;
    }

    .scenario-item:hover {
      transform: translateX(5px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .scenario-name {
      font-weight: 600;
      color: #1e3a8a;
      margin-bottom: 10px;
      font-size: 1.1em;
    }

    .scenario-details {
      font-size: 0.9em;
      color: #666;
      margin-bottom: 15px;
      line-height: 1.5;
    }

    .scenario-challenge {
      background: #fef3c7;
      border-left: 3px solid #f59e0b;
      padding: 10px;
      border-radius: 4px;
      font-size: 0.85em;
      margin-bottom: 15px;
      color: #92400e;
    }

    .difficulty {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.8em;
      font-weight: 600;
      margin-bottom: 15px;
    }

    .difficulty.easy {
      background: #d1fae5;
      color: #065f46;
    }

    .difficulty.medium {
      background: #fef3c7;
      color: #92400e;
    }

    .difficulty.hard {
      background: #fee2e2;
      color: #7f1d1d;
    }

    .start-btn {
      width: 100%;
      padding: 10px;
      background: linear-gradient(135deg, #1e3a8a 0%, #2e4fa2 100%);
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .start-btn:hover {
      transform: scale(1.02);
    }

    .quick-tips {
      background: #eff6ff;
      border-left: 4px solid #3b82f6;
      border-radius: 8px;
      padding: 20px;
      margin-top: 30px;
    }

    .quick-tips h3 {
      color: #1e40af;
      margin-bottom: 15px;
    }

    .tips-list {
      list-style: none;
    }

    .tips-list li {
      color: #1e40af;
      margin-bottom: 10px;
      padding-left: 25px;
      position: relative;
    }

    .tips-list li:before {
      content: "💡";
      position: absolute;
      left: 0;
    }
  </style>
</head>
<body>
  <div class="practice-container">
    <a href="mainpage.php" class="back-btn">← Back to Main</a>

    <div class="header">
      <h1>🎯 Practice Parking</h1>
      <p>Enhance your skills with various parking scenarios</p>
    </div>

    <div class="practice-modes">
      <div class="mode-card">
        <div class="mode-header">
          <div class="mode-icon">🎮</div>
          <div class="mode-title">Simulation Mode</div>
        </div>
        <div class="mode-body">
          <p class="mode-description">Practice parking in a realistic virtual environment with real-time feedback and sensor data.</p>
          <div class="mode-stats">
            <span class="stat-badge">⏱️ 15-30 min</span>
            <span class="stat-badge">📊 8 scenarios</span>
          </div>
          <button class="btn">Start Simulation</button>
        </div>
      </div>

      <div class="mode-card">
        <div class="mode-header">
          <div class="mode-icon">🏆</div>
          <div class="mode-title">Challenge Mode</div>
        </div>
        <div class="mode-body">
          <p class="mode-description">Test your skills in challenging conditions and compete with leaderboards.</p>
          <div class="mode-stats">
            <span class="stat-badge">🎯 Timed</span>
            <span class="stat-badge">💪 Hard</span>
          </div>
          <button class="btn">Start Challenge</button>
        </div>
      </div>

      <div class="mode-card">
        <div class="mode-header">
          <div class="mode-icon">📺</div>
          <div class="mode-title">Video Analysis</div>
        </div>
        <div class="mode-body">
          <p class="mode-description">Watch professional parking techniques and learn from expert demonstrations.</p>
          <div class="mode-stats">
            <span class="stat-badge">🎬 30+ videos</span>
            <span class="stat-badge">📚 All levels</span>
          </div>
          <button class="btn">Watch Videos</button>
        </div>
      </div>

      <div class="mode-card">
        <div class="mode-header">
          <div class="mode-icon">📝</div>
          <div class="mode-title">Quiz Mode</div>
        </div>
        <div class="mode-body">
          <p class="mode-description">Test your theoretical knowledge with interactive quizzes and get instant feedback.</p>
          <div class="mode-stats">
            <span class="stat-badge">❓ 50+ questions</span>
            <span class="stat-badge">✓ Instant feedback</span>
          </div>
          <button class="btn">Take Quiz</button>
        </div>
      </div>

      <div class="mode-card">
        <div class="mode-header">
          <div class="mode-icon">🌙</div>
          <div class="mode-title">Night Mode</div>
        </div>
        <div class="mode-body">
          <p class="mode-description">Practice parking at night with reduced visibility and low-light conditions.</p>
          <div class="mode-stats">
            <span class="stat-badge">🌑 Night driving</span>
            <span class="stat-badge">⚠️ Advanced</span>
          </div>
          <button class="btn">Start Night Practice</button>
        </div>
      </div>

      <div class="mode-card">
        <div class="mode-header">
          <div class="mode-icon">☔</div>
          <div class="mode-title">Weather Mode</div>
        </div>
        <div class="mode-body">
          <p class="mode-description">Master parking in rain, snow, and other challenging weather conditions.</p>
          <div class="mode-stats">
            <span class="stat-badge">🌧️ Weather</span>
            <span class="stat-badge">💪 Challenging</span>
          </div>
          <button class="btn">Weather Practice</button>
        </div>
      </div>
    </div>

    <div class="scenario-section">
      <h2 class="scenario-title">🏘️ Popular Practice Scenarios</h2>
      <div class="scenario-grid">
        <div class="scenario-item">
          <div class="scenario-name">Downtown Parallel Parking</div>
          <span class="difficulty easy">Beginner</span>
          <div class="scenario-details">Narrow street in downtown area with parked vehicles on both sides. Perfect for learning parallel parking basics.</div>
          <div class="scenario-challenge">🎯 Goal: Park within 2 attempts</div>
          <button class="start-btn">Start Scenario</button>
        </div>

        <div class="scenario-item">
          <div class="scenario-name">Mall Parking Lot</div>
          <span class="difficulty easy">Beginner</span>
          <div class="scenario-details">Large parking lot with multiple spaces and light traffic. Good for practicing parking awareness.</div>
          <div class="scenario-challenge">🎯 Goal: Find and park in marked space</div>
          <button class="start-btn">Start Scenario</button>
        </div>

        <div class="scenario-item">
          <div class="scenario-name">Highway Rest Stop</div>
          <span class="difficulty medium">Intermediate</span>
          <div class="scenario-details">Angled parking spaces with moderate traffic flow. Practice efficient parking decisions.</div>
          <div class="scenario-challenge">🎯 Goal: Park in under 2 minutes</div>
          <button class="start-btn">Start Scenario</button>
        </div>

        <div class="scenario-item">
          <div class="scenario-name">Rainy Street Parking</div>
          <span class="difficulty medium">Intermediate</span>
          <div class="scenario-details">Wet road conditions reduce traction. Learn to adjust for slippery surfaces.</div>
          <div class="scenario-challenge">🎯 Goal: Safe parking in wet conditions</div>
          <button class="start-btn">Start Scenario</button>
        </div>

        <div class="scenario-item">
          <div class="scenario-name">Tight Alley</div>
          <span class="difficulty hard">Advanced</span>
          <div class="scenario-details">Very narrow alley with minimal clearance. Maximum precision required.</div>
          <div class="scenario-challenge">🎯 Goal: Park without hitting walls</div>
          <button class="start-btn">Start Scenario</button>
        </div>

        <div class="scenario-item">
          <div class="scenario-name">Snow Covered Lot</div>
          <span class="difficulty hard">Advanced</span>
          <div class="scenario-details">Winter conditions with snow and ice. Traction is significantly reduced.</div>
          <div class="scenario-challenge">🎯 Goal: Successful parking on ice</div>
          <button class="start-btn">Start Scenario</button>
        </div>
      </div>

      <div class="quick-tips">
        <h3>💡 Quick Tips for Effective Practice</h3>
        <ul class="tips-list">
          <li><strong>Start with basics:</strong> Begin with beginner scenarios before attempting advanced challenges.</li>
          <li><strong>Practice consistently:</strong> 3-4 sessions per week yields better results than cramming.</li>
          <li><strong>Focus on technique:</strong> Speed will come naturally with proper technique.</li>
          <li><strong>Use feedback:</strong> Pay attention to real-time feedback and performance metrics.</li>
          <li><strong>Challenge yourself:</strong> Gradually increase difficulty to build confidence.</li>
          <li><strong>Review mistakes:</strong> Analyze what went wrong and try again with corrections.</li>
        </ul>
      </div>
    </div>
  </div>
</body>
</html>
