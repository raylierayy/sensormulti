<?php
$faqs = [
  [
    "question" => "How do I register for the parking course?",
    "answer" => "Visit the registration page, fill in your details, and submit the form. You'll receive a confirmation email within 24 hours. Your instructor will contact you to schedule your first lesson."
  ],
  [
    "question" => "What are the requirements for parallel parking?",
    "answer" => "Ensure your vehicle is properly positioned, maintain a safe distance from other vehicles, use your mirrors consistently, and practice smooth steering and acceleration. Check the Parallel Parking Basics lesson for detailed steps."
  ],
  [
    "question" => "How often should I practice?",
    "answer" => "We recommend practicing at least 3-4 times per week for optimal results. Consistent practice helps develop muscle memory and builds confidence in different parking scenarios."
  ],
  [
    "question" => "What sensors should I use while parking?",
    "answer" => "Modern vehicles have parking sensors that detect obstacles. Use both front and rear sensors. Additionally, always use your mirrors and perform visual checks before parking. Never rely solely on sensors."
  ],
  [
    "question" => "How do I improve my parking accuracy?",
    "answer" => "Focus on smooth steering inputs, maintain proper speed, use reference points, and practice regularly. Watch the performance videos in each lesson and ask your instructor for personalized tips."
  ],
  [
    "question" => "What is the pricing for the course?",
    "answer" => "Basic course: $199, Premium course with 1-on-1 sessions: $399. Contact support for group rates and special discounts. Payment plans are available."
  ],
  [
    "question" => "Can I reschedule my lessons?",
    "answer" => "Yes! You can reschedule up to 48 hours before your lesson through your profile. Go to Profile > My Lessons > Reschedule."
  ],
  [
    "question" => "How do I contact my instructor?",
    "answer" => "You can message your instructor directly through the platform, call the support hotline at 1-800-PARKING, or email support@parkingaid.com. Average response time is 2 hours."
  ]
];

$tutorials = [
  ["title" => "Getting Started", "link" => "#"],
  ["title" => "Using Parking Sensors", "link" => "#"],
  ["title" => "Parallel Parking Step-by-Step", "link" => "#"],
  ["title" => "Understanding Mirror Angles", "link" => "#"],
  ["title" => "Night Parking Safety", "link" => "#"],
  ["title" => "Parking in Tight Spaces", "link" => "#"]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Help & Support - Parking Aid</title>
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

    .help-container {
      max-width: 1000px;
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

    .search-box {
      background: white;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .search-input {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid #e5e7eb;
      border-radius: 5px;
      font-size: 1em;
      transition: border-color 0.3s;
    }

    .search-input:focus {
      outline: none;
      border-color: #1e3a8a;
    }

    .support-channels {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-bottom: 40px;
    }

    .channel {
      background: white;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }

    .channel:hover {
      transform: translateY(-5px);
    }

    .channel-icon {
      font-size: 2.5em;
      margin-bottom: 10px;
    }

    .channel-title {
      font-weight: 600;
      color: #1e3a8a;
      margin-bottom: 5px;
    }

    .channel-info {
      font-size: 0.9em;
      color: #666;
    }

    .faq-section {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }

    .faq-header {
      background: linear-gradient(135deg, #1e3a8a 0%, #2e4fa2 100%);
      color: white;
      padding: 25px;
    }

    .faq-header h2 {
      font-size: 1.5em;
    }

    .faq-item {
      border-bottom: 1px solid #e5e7eb;
      padding: 0;
    }

    .faq-item:last-child {
      border-bottom: none;
    }

    .faq-question {
      background: white;
      padding: 20px;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: background 0.3s ease;
      font-weight: 500;
      color: #333;
    }

    .faq-question:hover {
      background: #f9fafb;
    }

    .faq-toggle {
      font-size: 1.3em;
      transition: transform 0.3s ease;
    }

    .faq-toggle.open {
      transform: rotate(180deg);
    }

    .faq-answer {
      padding: 0 20px;
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease, padding 0.3s ease;
      background: #f9fafb;
    }

    .faq-answer.open {
      padding: 20px;
      max-height: 500px;
    }

    .faq-text {
      color: #666;
      line-height: 1.6;
    }

    .tutorials-section {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .tutorials-header {
      background: linear-gradient(135deg, #1e3a8a 0%, #2e4fa2 100%);
      color: white;
      padding: 25px;
    }

    .tutorials-header h2 {
      font-size: 1.5em;
    }

    .tutorials-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 15px;
      padding: 20px;
    }

    .tutorial-card {
      background: #f9fafb;
      border-radius: 8px;
      padding: 20px;
      text-align: center;
      border: 2px solid #e5e7eb;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .tutorial-card:hover {
      border-color: #1e3a8a;
      background: white;
      transform: translateY(-3px);
    }

    .tutorial-icon {
      font-size: 2em;
      margin-bottom: 10px;
    }

    .tutorial-title {
      font-weight: 600;
      color: #1e3a8a;
      margin-bottom: 10px;
    }

    .tutorial-btn {
      padding: 8px 16px;
      background: linear-gradient(135deg, #1e3a8a 0%, #2e4fa2 100%);
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .tutorial-btn:hover {
      transform: scale(1.05);
    }

    .contact-section {
      background: white;
      border-radius: 10px;
      padding: 30px;
      text-align: center;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .contact-section h2 {
      color: #1e3a8a;
      margin-bottom: 20px;
    }

    .contact-info {
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
    }

    .contact-item {
      flex: 1;
      min-width: 200px;
    }

    .contact-label {
      font-weight: 600;
      color: #666;
      margin-bottom: 8px;
    }

    .contact-value {
      font-size: 1.1em;
      color: #1e3a8a;
      font-weight: 500;
    }
  </style>
</head>
<body>
  <div class="help-container">
    <a href="mainpage.php" class="back-btn">← Back to Main</a>

    <div class="header">
      <h1>❓ Help & Support</h1>
      <p>Find answers and get assistance with your parking lessons</p>
    </div>

    <div class="search-box">
      <input type="text" class="search-input" placeholder="Search for help... (e.g., 'How to register', 'Payment', 'Rescheduling')">
    </div>

    <div class="support-channels">
      <div class="channel">
        <div class="channel-icon">📞</div>
        <div class="channel-title">Phone Support</div>
        <div class="channel-info">1-800-PARKING (24/7)</div>
      </div>
      <div class="channel">
        <div class="channel-icon">📧</div>
        <div class="channel-title">Email</div>
        <div class="channel-info">support@parkingaid.com</div>
      </div>
      <div class="channel">
        <div class="channel-icon">💬</div>
        <div class="channel-title">Live Chat</div>
        <div class="channel-info">Available 8am-8pm</div>
      </div>
      <div class="channel">
        <div class="channel-icon">📱</div>
        <div class="channel-title">Social Media</div>
        <div class="channel-info">@ParkingAidApp</div>
      </div>
    </div>

    <div class="faq-section">
      <div class="faq-header">
        <h2>📋 Frequently Asked Questions</h2>
      </div>
      <?php foreach ($faqs as $index => $faq): ?>
        <div class="faq-item">
          <div class="faq-question" onclick="toggleFAQ(this)">
            <span><?php echo htmlspecialchars($faq['question']); ?></span>
            <span class="faq-toggle">▼</span>
          </div>
          <div class="faq-answer">
            <p class="faq-text"><?php echo htmlspecialchars($faq['answer']); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="tutorials-section">
      <div class="tutorials-header">
        <h2>🎥 Video Tutorials</h2>
      </div>
      <div class="tutorials-grid">
        <?php foreach ($tutorials as $tutorial): ?>
          <div class="tutorial-card">
            <div class="tutorial-icon">🎬</div>
            <div class="tutorial-title"><?php echo htmlspecialchars($tutorial['title']); ?></div>
            <button class="tutorial-btn">Watch Now</button>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div style="height: 20px;"></div>

    <div class="contact-section">
      <h2>Still Need Help?</h2>
      <p style="color: #666; margin-bottom: 20px;">Our support team is ready to assist you. Reach out through any of these channels:</p>
      <div class="contact-info">
        <div class="contact-item">
          <div class="contact-label">Support Hours</div>
          <div class="contact-value">24/7 Available</div>
        </div>
        <div class="contact-item">
          <div class="contact-label">Average Response</div>
          <div class="contact-value">2 Hours</div>
        </div>
        <div class="contact-item">
          <div class="contact-label">Satisfaction Rate</div>
          <div class="contact-value">98%</div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function toggleFAQ(element) {
      const question = element;
      const answer = element.nextElementSibling;
      
      // Close all other FAQs
      document.querySelectorAll('.faq-answer').forEach(a => {
        if (a !== answer) {
          a.classList.remove('open');
        }
      });
      
      document.querySelectorAll('.faq-toggle').forEach(toggle => {
        if (toggle !== question.querySelector('.faq-toggle')) {
          toggle.classList.remove('open');
        }
      });
      
      // Toggle current FAQ
      answer.classList.toggle('open');
      question.querySelector('.faq-toggle').classList.toggle('open');
    }
  </script>
</body>
</html>
