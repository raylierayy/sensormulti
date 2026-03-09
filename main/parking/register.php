<?php
// Form validation functions
$errors = [];
$success = "";

function validateEmail($email) {
  return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
  return preg_match('/^[0-9]{10,}$/', preg_replace('/[^0-9]/', '', $phone));
}

function validateName($name) {
  return strlen(trim($name)) >= 2 && strlen(trim($name)) <= 100;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
  $phone = isset($_POST["phone"]) ? trim($_POST["phone"]) : "";
  $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
  $date = isset($_POST["date"]) ? trim($_POST["date"]) : "";

  // Validation
  if (empty($name)) {
    $errors[] = "Name is required.";
  } elseif (!validateName($name)) {
    $errors[] = "Name must be between 2 and 100 characters.";
  }

  if (empty($phone)) {
    $errors[] = "Phone number is required.";
  } elseif (!validatePhone($phone)) {
    $errors[] = "Phone number must contain at least 10 digits.";
  }

  if (empty($email)) {
    $errors[] = "Email is required.";
  } elseif (!validateEmail($email)) {
    $errors[] = "Please enter a valid email address.";
  }

  if (empty($date)) {
    $errors[] = "Date is required.";
  } elseif (strtotime($date) > time()) {
    $errors[] = "Date cannot be in the future.";
  }

  if (empty($errors)) {
    $success = "✅ Student information registered successfully!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register Student - Parking Aid</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 40px 20px;
      min-height: 100vh;
    }

    .form-container {
      max-width: 600px;
      margin: auto;
      background-color: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    }

    .back-btn {
      display: inline-block;
      margin-bottom: 20px;
      padding: 10px 20px;
      background: transparent;
      color: #1e3a8a;
      text-decoration: none;
      border-radius: 5px;
      font-weight: 600;
      transition: all 0.3s ease;
      cursor: pointer;
      border: 2px solid #1e3a8a;
    }

    .back-btn:hover {
      background: #1e3a8a;
      color: white;
    }

    h2 {
      text-align: center;
      margin-bottom: 10px;
      color: #1e3a8a;
    }

    .form-subtitle {
      text-align: center;
      color: #666;
      margin-bottom: 30px;
      font-size: 0.95em;
    }

    .alert {
      padding: 15px;
      border-radius: 6px;
      margin-bottom: 20px;
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }

    .alert-success {
      background: #d1fae5;
      color: #065f46;
      border: 1px solid #6ee7b7;
    }

    .alert-error {
      background: #fee2e2;
      color: #7f1d1d;
      border: 1px solid #fca5a5;
    }

    .alert-icon {
      font-size: 1.2em;
      flex-shrink: 0;
    }

    .alert-content {
      flex: 1;
    }

    .alert-content ul {
      list-style: none;
      margin: 0;
      padding: 0;
    }

    .alert-content li {
      padding: 3px 0;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
    }

    .required {
      color: #ef4444;
    }

    input[type="text"],
    input[type="email"],
    input[type="date"],
    input[type="file"],
    input[type="tel"],
    select {
      width: 100%;
      padding: 12px;
      margin-top: 5px;
      border: 2px solid #e5e7eb;
      border-radius: 6px;
      font-size: 0.95em;
      transition: all 0.3s ease;
      background-color: #f9fafb;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="date"]:focus,
    input[type="file"]:focus,
    input[type="tel"]:focus,
    select:focus {
      outline: none;
      border-color: #1e3a8a;
      background-color: white;
      box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }

    input[type="file"] {
      padding: 8px;
    }

    .file-help {
      font-size: 0.85em;
      color: #666;
      margin-top: 5px;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
    }

    @media (max-width: 600px) {
      .form-row {
        grid-template-columns: 1fr;
      }

      .form-container {
        padding: 25px;
      }
    }

    .button-group {
      display: flex;
      gap: 10px;
      margin-top: 30px;
    }

    button {
      flex: 1;
      padding: 12px;
      border: none;
      border-radius: 6px;
      font-size: 1em;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-submit {
      background: linear-gradient(135deg, #1e3a8a 0%, #2e4fa2 100%);
      color: white;
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(30, 58, 138, 0.3);
    }

    .btn-reset {
      background: #e5e7eb;
      color: #333;
    }

    .btn-reset:hover {
      background: #d1d5db;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <a href="mainpage.php" class="back-btn">← Back</a>

    <h2>📝 Register Student Information</h2>
    <p class="form-subtitle">Fill in the details to register a new student</p>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success">
        <div class="alert-icon">✓</div>
        <div class="alert-content"><?php echo $success; ?></div>
      </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <div class="alert-icon">⚠</div>
        <div class="alert-content">
          <strong>Please fix the following errors:</strong>
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
      <div class="form-row">
        <div class="form-group">
          <label for="name">Full Name <span class="required">*</span></label>
          <input type="text" name="name" id="name" placeholder="John Doe" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required />
        </div>

        <div class="form-group">
          <label for="phone">Phone Number <span class="required">*</span></label>
          <input type="tel" name="phone" id="phone" placeholder="09171234567" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required />
        </div>
      </div>

      <div class="form-group">
        <label for="email">Email Address <span class="required">*</span></label>
        <input type="email" name="email" id="email" placeholder="student@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required />
      </div>

      <div class="form-group">
        <label for="date">Registration Date <span class="required">*</span></label>
        <input type="date" name="date" id="date" value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : date('Y-m-d'); ?>" required />
      </div>

      <div class="form-group">
        <label for="picture">Profile Picture (Optional)</label>
        <input type="file" name="picture" id="picture" accept=".png,.jpg,.jpeg,.gif" />
        <div class="file-help">Accepted formats: PNG, JPG, JPEG, GIF (Max 5MB)</div>
      </div>

      <div class="button-group">
        <button type="submit" class="btn-submit">✓ Register Student</button>
        <button type="reset" class="btn-reset">↻ Clear Form</button>
      </div>
    </form>
  </div>

  <script>
    function validateForm() {
      const name = document.getElementById('name').value.trim();
      const phone = document.getElementById('phone').value.trim();
      const email = document.getElementById('email').value.trim();

      if (name.length < 2) {
        alert('Name must be at least 2 characters long.');
        return false;
      }

      const phoneDigits = phone.replace(/[^0-9]/g, '');
      if (phoneDigits.length < 10) {
        alert('Phone number must contain at least 10 digits.');
        return false;
      }

      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        alert('Please enter a valid email address.');
        return false;
      }

      return true;
    }
  </script>
</body>
</html>
