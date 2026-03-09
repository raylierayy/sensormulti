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
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
          font-family: 'Inter', 'Segoe UI', sans-serif;
          min-height: 100vh;
          display: flex;
          justify-content: center;
          align-items: center;
          background: 
            radial-gradient(ellipse at 20% 50%, rgba(120,119,198,0.35) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 20%, rgba(236,72,153,0.2) 0%, transparent 50%),
            radial-gradient(ellipse at 50% 80%, rgba(6,182,212,0.2) 0%, transparent 50%),
            #050816;
          position: relative;
          overflow: hidden;
        }

        /* Orbs */
        .orb { position: fixed; border-radius: 50%; filter: blur(80px); pointer-events: none; z-index: 0; }
        .orb-1 { width:400px; height:400px; background:rgba(102,126,234,0.25); top:-10%; left:-5%; animation: float 8s ease-in-out infinite; }
        .orb-2 { width:350px; height:350px; background:rgba(236,72,153,0.2); bottom:10%; right:-5%; animation: float 10s ease-in-out infinite 2s; }
        .orb-3 { width:280px; height:280px; background:rgba(6,182,212,0.2); top:50%; left:60%; animation: float 7s ease-in-out infinite 4s; }

        @keyframes float {
          0%, 100% { transform: translateY(0) scale(1); }
          50% { transform: translateY(-30px) scale(1.05); }
        }

        @keyframes fadeInUp {
          from { opacity:0; transform: translateY(30px); }
          to { opacity:1; transform: translateY(0); }
        }

        @keyframes pulse-glow {
          0%, 100% { box-shadow: 0 0 20px rgba(102,126,234,0.5); }
          50% { box-shadow: 0 0 40px rgba(102,126,234,0.8), 0 0 60px rgba(118,75,162,0.4); }
        }

        @keyframes shimmer {
          0% { background-position: -200% center; }
          100% { background-position: 200% center; }
        }

        /* Reduced motion accessibility */
        @media (prefers-reduced-motion: reduce) {
          .orb { animation: none !important; }
          .login-wrapper { animation: none !important; }
          .logo-icon-wrap { animation: none !important; }
        }

        /* Grid pattern overlay */
        body::after {
          content: '';
          position: fixed;
          inset: 0;
          background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
          background-size: 50px 50px;
          pointer-events: none;
          z-index: 0;
        }

        .login-wrapper {
          position: relative;
          z-index: 1;
          width: 100%;
          max-width: 440px;
          padding: 0 20px;
          animation: fadeInUp 0.6s ease both;
        }

        .login-container {
          background: rgba(255,255,255,0.07);
          backdrop-filter: blur(32px) saturate(180%);
          -webkit-backdrop-filter: blur(32px) saturate(180%);
          border: 1.5px solid rgba(255,255,255,0.15);
          border-radius: 32px;
          padding: 48px 40px;
          box-shadow: 0 24px 80px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.18);
          position: relative;
          overflow: hidden;
        }

        .login-container::before {
          content: '';
          position: absolute;
          top: 0; left: 0; right: 0; height: 1px;
          background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        }

        .logo-section { text-align: center; margin-bottom: 36px; }

        .logo-icon-wrap {
          width: 72px; height: 72px;
          background: linear-gradient(135deg, #667eea, #764ba2);
          border-radius: 20px;
          margin: 0 auto 16px;
          display: flex; align-items: center; justify-content: center;
          font-size: 1.8em;
          box-shadow: 0 8px 32px rgba(102,126,234,0.5);
          animation: pulse-glow 3s ease-in-out infinite;
        }

        .login-title {
          color: rgba(255,255,255,0.95);
          font-size: 1.7em;
          font-weight: 800;
          margin: 0 0 6px;
          letter-spacing: -0.02em;
        }

        .login-subtitle {
          color: rgba(255,255,255,0.45);
          font-size: 0.9em;
        }

        .form-group { margin-bottom: 20px; }

        .form-label {
          display: block;
          margin-bottom: 8px;
          color: rgba(255,255,255,0.7);
          font-weight: 600;
          font-size: 0.85em;
          text-transform: uppercase;
          letter-spacing: 0.06em;
        }

        .input-field {
          width: 100%;
          padding: 14px 20px;
          background: rgba(255,255,255,0.07);
          border: 1.5px solid rgba(255,255,255,0.12);
          border-radius: 14px;
          color: rgba(255,255,255,0.95);
          font-size: 0.95em;
          font-weight: 500;
          transition: all 0.25s ease;
          outline: none;
          box-sizing: border-box;
          font-family: 'Inter', sans-serif;
        }

        .input-field::placeholder { color: rgba(255,255,255,0.25); }

        .input-field:focus {
          border-color: rgba(102,126,234,0.7);
          background: rgba(102,126,234,0.08);
          box-shadow: 0 0 0 4px rgba(102,126,234,0.15);
        }

        .btn-login {
          width: 100%;
          padding: 15px;
          margin-top: 8px;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          border: none;
          border-radius: 14px;
          color: white;
          font-size: 1em;
          font-weight: 700;
          letter-spacing: 0.02em;
          cursor: pointer;
          box-shadow: 0 8px 24px rgba(102,126,234,0.5);
          transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
          position: relative;
          overflow: hidden;
          font-family: 'Inter', sans-serif;
        }

        .btn-login::before {
          content: '';
          position: absolute;
          top: 0; left: -100%; width: 100%; height: 100%;
          background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
          transition: left 0.5s;
        }

        .btn-login:hover {
          transform: translateY(-2px);
          box-shadow: 0 12px 36px rgba(102,126,234,0.7);
        }

        .btn-login:hover::before { left: 100%; }
        .btn-login:active { transform: translateY(0) scale(0.98); }

        .error-pill {
          background: rgba(239,68,68,0.15);
          border: 1px solid rgba(239,68,68,0.35);
          color: #fca5a5;
          padding: 12px 18px;
          border-radius: 12px;
          text-align: center;
          margin-bottom: 20px;
          font-weight: 600;
          font-size: 0.9em;
          backdrop-filter: blur(8px);
        }

        .footer-link {
          display: block;
          text-align: center;
          margin-top: 24px;
          color: rgba(255,255,255,0.4);
          text-decoration: none;
          font-size: 0.85em;
          transition: color 0.2s;
        }

        .footer-link:hover { color: rgba(255,255,255,0.7); }

        @media (max-width: 500px) {
          .login-container { padding: 36px 24px; }
          .login-title { font-size: 1.4em; }
        }
    </style>
</head>
<body>
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<div class="login-wrapper">
    <div class="login-container">
        <div class="logo-section">
            <div class="logo-icon-wrap">🅿️</div>
            <h1 class="login-title">Parking Aid System</h1>
            <p class="login-subtitle">Instructor Login Portal</p>
        </div>

        <?php if ($error): ?>
            <div class="error-pill">🚫 <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input type="text" id="username" name="username" class="input-field"
                       placeholder="Enter your username" required autocomplete="username">
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="input-field"
                       placeholder="Enter your password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn-login">Sign In →</button>
        </form>

        <a href="dashboard.php" class="footer-link">← Back to Dashboard</a>
    </div>
</div>
</body>
</html>
