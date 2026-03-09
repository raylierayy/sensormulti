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
    <title>Login - Parking Aid Sensor System</title>
    <style>
        /* ===== GLASS THEME FOR LOGIN ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;

            /* GLASS GRADIENT BACKGROUND */
            background: linear-gradient(135deg,
                #667eea 0%,
                #764ba2 50%,
                #f093fb 100%);
            position: relative;
            overflow: hidden;
        }

        /* Radial glow overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 20% 50%,
                rgba(255,255,255,0.15) 0%,
                transparent 50%);
            pointer-events: none;
        }

        /* MAIN GLASS LOGIN CONTAINER */
        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 50px 40px;

            /* Glass morphism */
            background: rgba(255,255,255,0.14);
            backdrop-filter: blur(20px) saturate(140%);
            -webkit-backdrop-filter: blur(20px) saturate(140%);

            border: 1px solid rgba(255,255,255,0.28);
            border-radius: 32px;

            box-shadow:
                0 24px 60px rgba(0,0,0,0.25),
                inset 0 1px 0 rgba(255,255,255,0.25);

            position: relative;
            z-index: 1;
        }

        /* Glass glow effect on container */
        .login-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(
                circle at 50% 0%,
                rgba(255,255,255,0.2) 0%,
                transparent 50%
            );
            pointer-events: none;
        }

        /* Logo/Icon Area */
        .logo-section {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-icon {
            font-size: 4em;
            margin-bottom: 10px;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        }

        h2 {
            text-align: center;
            color: rgba(255,255,255,0.95);
            font-size: 1.8em;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .subtitle {
            text-align: center;
            color: rgba(255,255,255,0.75);
            font-size: 0.95em;
            margin-bottom: 30px;
        }

        /* GLASS FORM GROUP */
        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        /* Floating Label */
        label {
            display: block;
            margin-bottom: 8px;
            color: rgba(255,255,255,0.85);
            font-weight: 600;
            font-size: 0.9em;
            letter-spacing: 0.3px;
        }

        /* PILL INPUT FIELDS */
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 14px 20px;

            /* Glass input */
            background: rgba(255,255,255,0.85);
            border: 1px solid rgba(255,255,255,0.50);
            border-radius: 999px; /* PILL SHAPE */

            font-size: 1em;
            font-weight: 500;
            color: #111827;

            box-shadow:
                0 8px 16px rgba(0,0,0,0.08),
                inset 0 1px 0 rgba(255,255,255,0.60);

            transition: all 0.3s ease;
            outline: none;
        }

        input:focus {
            background: rgba(255,255,255,0.95);
            border-color: rgba(13,110,253,0.85);
            box-shadow:
                0 12px 24px rgba(13,110,253,0.25),
                inset 0 1px 0 rgba(255,255,255,0.70);
            transform: translateY(-1px);
        }

        input::placeholder {
            color: rgba(17,24,39,0.45);
        }

        /* GLASS PILL BUTTON */
        button[type="submit"] {
            width: 100%;
            padding: 16px;
            margin-top: 10px;

            /* Glass button with accent */
            background: rgba(13,110,253,0.85);
            border: 1px solid rgba(255,255,255,0.60);
            border-radius: 999px; /* PILL */

            color: rgba(255,255,255,0.98);
            font-size: 1.05em;
            font-weight: 900;
            letter-spacing: 0.5px;
            text-transform: uppercase;

            cursor: pointer;

            backdrop-filter: blur(12px);
            box-shadow:
                0 12px 28px rgba(13,110,253,0.35),
                inset 0 1px 0 rgba(255,255,255,0.25);

            transition: all 0.2s ease;
        }

        button[type="submit"]:hover {
            background: rgba(13,110,253,0.95);
            transform: translateY(-2px);
            box-shadow:
                0 16px 36px rgba(13,110,253,0.45),
                inset 0 1px 0 rgba(255,255,255,0.30);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        /* ERROR MESSAGE PILL */
        .error {
            background: rgba(239,68,68,0.85);
            color: rgba(255,255,255,0.95);
            padding: 12px 20px;
            border-radius: 999px; /* PILL */
            text-align: center;
            margin-bottom: 20px;
            font-weight: 700;
            font-size: 0.95em;

            border: 1px solid rgba(255,255,255,0.25);
            backdrop-filter: blur(8px);
            box-shadow: 0 8px 20px rgba(239,68,68,0.35);
        }

        /* Footer Links */
        .footer-links {
            margin-top: 25px;
            text-align: center;
        }

        .footer-links a {
            color: rgba(255,255,255,0.80);
            text-decoration: none;
            font-size: 0.9em;
            font-weight: 600;

            padding: 8px 16px;
            border-radius: 999px;

            background: rgba(255,255,255,0.10);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.15);

            transition: all 0.2s ease;
            display: inline-block;
        }

        .footer-links a:hover {
            background: rgba(255,255,255,0.18);
            border-color: rgba(255,255,255,0.30);
            transform: translateY(-1px);
        }

        /* Responsive */
        @media (max-width: 500px) {
            .login-container {
                margin: 20px;
                padding: 40px 30px;
            }

            h2 {
                font-size: 1.5em;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="logo-section">
        <div class="logo-icon">🅿️</div>
        <h2>Parking Aid System</h2>
        <p class="subtitle">Instructor Login Portal</p>
    </div>

    <?php if ($error): ?>
        <div class="error">🚫 <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text"
                   id="username"
                   name="username"
                   placeholder="Enter your username"
                   required
                   autocomplete="username">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password"
                   id="password"
                   name="password"
                   placeholder="Enter your password"
                   required
                   autocomplete="current-password">
        </div>

        <button type="submit">🚀 Sign In</button>
    </form>

    <div class="footer-links">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
