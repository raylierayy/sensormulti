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
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Enter Student Info</h2>
    <form action="update_student_info.php" method="POST" enctype="multipart/form-data">

      <label for="name">Name:</label>
      <input type="text" name="name" id="name" required />

      <label for="phone">Phone Number:</label>
      <input type="tel" name="phone" id="phone" required />

      <label for="picture">Picture (PNG, JPG, JPEG only):</label>
      <input type="file" name="picture" id="picture" accept=".png,.jpg,.jpeg" />

      <label for="date">Date:</label>
      <input type="date" name="date" id="date" required />

      <label for="email">Email:</label>
      <input type="email" name="email" id="email" required />

      <button type="submit">Register Student Info</button>
    </form>
  </div>
</body>
</html>