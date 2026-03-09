<?php
// Simulated student data
$students = [
  "001" => [
    "name" => "Juan Dela Cruz",
    "phone" => "09171234567",
    "email" => "juan@example.com",
    "date" => "2025-10-01",
    "picture" => "juan.jpg"
  ],
  "002" => [
    "name" => "Maria Santos",
    "phone" => "09181234567",
    "email" => "maria@example.com",
    "date" => "2025-10-05",
    "picture" => "maria.png"
  ]
];

$student_id = '';
$student = null;

// Handle search
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["student_id"])) {
  $student_id = trim($_POST["student_id"]);
  if (array_key_exists($student_id, $students)) {
    $student = $students[$student_id];
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Student Info</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f0f4f8;
      padding: 40px;
    }

    .form-container {
      max-width: 600px;
      margin: auto;
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
    }

    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }

    input[type="text"],
    input[type="email"],
    input[type="date"],
    input[type="file"],
    input[type="tel"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      margin-top: 25px;
      width: 100%;
      padding: 12px;
      background-color: #1e3a8a;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
    }

    button:hover {
      background-color: #2e4fa2;
    }

    .search-box {
      margin-bottom: 30px;
    }

    .current-pic {
      font-size: 14px;
      color: #555;
      margin-top: 5px;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Edit Student Info</h2>

    <!-- Search Form -->
    <form method="POST" class="search-box">
      <label for="student_id">Enter Student ID:</label>
      <input type="text" name="student_id" id="student_id" value="<?php echo htmlspecialchars($student_id); ?>" required />
      <button type="submit">Search</button>
    </form>

    <!-- Display Form if Student Found -->
    <?php if ($student): ?>
      <form action="update_student_info.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>" />

        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($student['name']); ?>" required />

        <label for="phone">Phone Number:</label>
        <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" required />

        <label for="picture">Picture (PNG, JPG, JPEG only):</label>
        <input type="file" name="picture" id="picture" accept=".png,.jpg,.jpeg" />
        <div class="current-pic">Current file: <?php echo htmlspecialchars($student['picture']); ?></div>

        <label for="date">Date:</label>
        <input type="date" name="date" id="date" value="<?php echo htmlspecialchars($student['date']); ?>" required />

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($student['email']); ?>" required />

        <button type="submit">Update Student Info</button>
      </form>
    <?php elseif ($student_id): ?>
      <p>No student found with ID: <strong><?php echo htmlspecialchars($student_id); ?></strong></p>
    <?php endif; ?>
  </div>
</body>
</html>