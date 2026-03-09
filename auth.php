<?php
session_start();
require 'db_connection.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prevent SQL injection
    // Use parameterized queries for SQL Server
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $params = array($username);
    $result = sqlsrv_query($conn, $sql, $params);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_has_rows($result)) {
        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        // In a real app we verify hash. Here we compare plain text as per "basic" request.
        if ($password == $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sensor System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            background: rgba(255,255,255,0.16);
            backdrop-filter: blur(22px) saturate(150%);
            -webkit-backdrop-filter: blur(22px) saturate(150%);
            border: 1px solid rgba(255,255,255,0.32);
            border-radius: 28px;
            padding: 44px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.28), inset 0 1px 0 rgba(255,255,255,0.24);
        }
        .login-container h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 10px;
            font-size: 1.7em;
            font-weight: 800;
            text-shadow: 0 2px 8px rgba(0,0,0,0.25);
        }
        .login-subtitle {
            text-align: center;
            color: rgba(255,255,255,0.70);
            font-size: 0.9em;
            margin-bottom: 30px;
        }
        .login-container .form-group {
            margin-bottom: 18px;
        }
        .login-container label {
            display: block;
            margin-bottom: 6px;
            color: rgba(255,255,255,0.88);
            font-weight: 600;
            font-size: 0.9em;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 12px 20px;
            border: 1px solid rgba(255,255,255,0.65);
            border-radius: 999px;
            background: rgba(255,255,255,0.88);
            font-weight: 600;
            font-size: 0.95em;
            color: #1e293b;
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .login-container input:focus {
            border-color: rgba(99,179,237,0.90);
            box-shadow: 0 0 0 3px rgba(99,179,237,0.25);
            background: rgba(255,255,255,0.97);
        }
        .login-btn {
            width: 100%;
            padding: 13px;
            background: rgba(255,255,255,0.18);
            backdrop-filter: blur(14px);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.50);
            border-radius: 999px;
            font-size: 1em;
            font-weight: 800;
            cursor: pointer;
            transition: transform 160ms ease, background 160ms ease, box-shadow 160ms ease;
            box-shadow: 0 10px 28px rgba(0,0,0,0.20);
            margin-top: 8px;
        }
        .login-btn:hover {
            transform: translateY(-2px);
            background: rgba(255,255,255,0.26);
            box-shadow: 0 14px 36px rgba(0,0,0,0.26);
        }
        .error-pill {
            background: rgba(239,68,68,0.20);
            border: 1px solid rgba(239,68,68,0.50);
            color: #fee2e2;
            padding: 10px 18px;
            border-radius: 999px;
            text-align: center;
            margin-bottom: 18px;
            font-weight: 600;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>🅿️ Parking Aid</h2>
    <p class="login-subtitle">Multi-Sensor Test System — Instructor Login</p>
    <?php if ($error): ?>
        <div class="error-pill">⚠️ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>
        </div>
        <button type="submit" class="login-btn">Login →</button>
    </form>
</div>

</body>
</html>
