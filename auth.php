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

    $sql    = "SELECT id, username, password FROM users WHERE username = ?";
    $params = array($username);
    $result = sqlsrv_query($conn, $sql, $params);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_has_rows($result)) {
        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        if ($password == $row['password']) {
            $_SESSION['user_id']  = $row['id'];
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0a0e1a;
            overflow: hidden;
        }

        /* ── Background mesh ──────────────────────────────────── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse at 15% 50%,  rgba(99,102,241,0.22) 0%, transparent 55%),
                radial-gradient(ellipse at 85% 15%,  rgba(139,92,246,0.18) 0%, transparent 55%),
                radial-gradient(ellipse at 50% 90%,  rgba(6,182,212,0.14)  0%, transparent 55%);
            pointer-events: none;
            z-index: 0;
        }

        /* Grid pattern */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
        }

        /* ── Floating orbs ────────────────────────────────────── */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            z-index: 0;
            animation: float 9s ease-in-out infinite;
        }
        .orb-1 { width:480px; height:480px; background:rgba(99,102,241,0.18); top:-8%; left:-4%; }
        .orb-2 { width:400px; height:400px; background:rgba(139,92,246,0.15); bottom:5%; right:-3%; animation-delay:-4s; }
        .orb-3 { width:320px; height:320px; background:rgba(6,182,212,0.14); top:45%; left:55%; animation-delay:-7s; animation-duration:11s; }

        @keyframes float {
            0%,100% { transform: translateY(0) scale(1); }
            50%      { transform: translateY(-22px) scale(1.04); }
        }

        @keyframes fadeInUp {
            from { opacity:0; transform:translateY(28px); }
            to   { opacity:1; transform:translateY(0); }
        }

        @keyframes pulse-ring {
            0%,100% { box-shadow: 0 0 0 0 rgba(99,102,241,0.40); }
            50%      { box-shadow: 0 0 0 10px rgba(99,102,241,0.0); }
        }

        @keyframes shimmer-sweep {
            0%   { left: -100%; }
            100% { left: 100%;  }
        }

        /* ── Split layout ─────────────────────────────────────── */
        .auth-split {
            display: flex;
            width: 100%;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        /* Left branding panel */
        .auth-brand {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 60px 60px 60px 80px;
            background: linear-gradient(135deg,
                rgba(99,102,241,0.12) 0%,
                rgba(139,92,246,0.08) 100%);
            border-right: 1px solid rgba(255,255,255,0.08);
            position: relative;
            overflow: hidden;
        }

        .auth-brand::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(
                ellipse at 30% 50%,
                rgba(99,102,241,0.15) 0%,
                transparent 60%);
            pointer-events: none;
        }

        .brand-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2em;
            margin-bottom: 28px;
            box-shadow: 0 12px 40px rgba(99,102,241,0.55);
            animation: pulse-ring 3s ease-in-out infinite;
            position: relative;
            z-index: 1;
        }

        .brand-title {
            font-size: 2.4em;
            font-weight: 900;
            color: #fff;
            line-height: 1.15;
            letter-spacing: -0.03em;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }

        .brand-title span {
            background: linear-gradient(135deg, #6366f1, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-desc {
            color: rgba(255,255,255,0.45);
            font-size: 1em;
            line-height: 1.65;
            max-width: 380px;
            position: relative;
            z-index: 1;
        }

        .brand-features {
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            position: relative;
            z-index: 1;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.60);
            font-size: 0.9em;
        }

        .brand-feature-icon {
            width: 36px; height: 36px;
            background: rgba(99,102,241,0.15);
            border: 1px solid rgba(99,102,241,0.30);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1em;
            flex-shrink: 0;
        }

        /* Right form panel */
        .auth-form-panel {
            width: 480px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 60px;
            background: rgba(255,255,255,0.025);
        }

        .auth-form-wrap {
            width: 100%;
            max-width: 360px;
            animation: fadeInUp 0.55s ease both;
        }

        /* Form card */
        .login-card {
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(32px) saturate(180%);
            -webkit-backdrop-filter: blur(32px) saturate(180%);
            border: 1.5px solid rgba(255,255,255,0.12);
            border-radius: 28px;
            padding: 40px 36px;
            box-shadow: 0 24px 80px rgba(0,0,0,0.50),
                        inset 0 1px 0 rgba(255,255,255,0.15);
            position: relative;
            overflow: hidden;
        }

        /* Top edge highlight */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg,
                transparent,
                rgba(255,255,255,0.28),
                transparent);
        }

        .card-header {
            margin-bottom: 30px;
        }

        .card-header h2 {
            font-size: 1.55em;
            font-weight: 800;
            color: #fff;
            margin: 0 0 6px;
            letter-spacing: -0.025em;
        }

        .card-header p {
            color: rgba(255,255,255,0.42);
            font-size: 0.88em;
        }

        /* Form fields */
        .field-group { margin-bottom: 18px; }

        .field-label {
            display: block;
            margin-bottom: 8px;
            color: rgba(255,255,255,0.65);
            font-weight: 600;
            font-size: 0.80em;
            text-transform: uppercase;
            letter-spacing: 0.07em;
        }

        .field-box {
            position: relative;
        }

        .field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1em;
            opacity: 0.50;
            pointer-events: none;
        }

        .field-input {
            width: 100%;
            padding: 13px 18px 13px 44px;
            background: rgba(255,255,255,0.06);
            border: 1.5px solid rgba(255,255,255,0.12);
            border-radius: 13px;
            color: rgba(255,255,255,0.95);
            font-size: 0.95em;
            font-weight: 500;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            font-family: inherit;
            box-sizing: border-box;
        }

        .field-input::placeholder { color: rgba(255,255,255,0.22); }

        .field-input:focus {
            border-color: rgba(99,102,241,0.75);
            background: rgba(99,102,241,0.08);
            box-shadow: 0 0 0 4px rgba(99,102,241,0.18);
        }

        /* Error message */
        .error-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.35);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 0.88em;
        }

        /* Submit button */
        .btn-login {
            width: 100%;
            padding: 15px;
            margin-top: 6px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            border-radius: 14px;
            color: #fff;
            font-size: 0.97em;
            font-weight: 700;
            letter-spacing: 0.02em;
            cursor: pointer;
            box-shadow: 0 8px 28px rgba(99,102,241,0.50);
            transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
            position: relative;
            overflow: hidden;
            font-family: inherit;
        }

        .btn-login::after {
            content: '';
            position: absolute;
            top: 0; height: 100%; width: 60%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.13), transparent);
            left: -80%;
            transition: left 0.6s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 38px rgba(99,102,241,0.70);
        }

        .btn-login:hover::after { left: 140%; }
        .btn-login:active { transform: translateY(0) scale(0.98); }

        /* Footer */
        .card-footer {
            text-align: center;
            margin-top: 22px;
        }

        .card-footer a {
            color: rgba(255,255,255,0.35);
            text-decoration: none;
            font-size: 0.83em;
            transition: color 0.2s;
        }

        .card-footer a:hover { color: rgba(255,255,255,0.70); }

        /* ── Responsive ──────────────────────────────────────── */
        @media (max-width: 800px) {
            .auth-brand { display: none; }
            .auth-form-panel { width: 100%; padding: 30px 24px; }
            .auth-form-wrap { max-width: 400px; }
        }

        @media (prefers-reduced-motion: reduce) {
            .orb, .brand-icon { animation: none !important; }
            .auth-form-wrap { animation: none !important; }
        }
    </style>
</head>
<body>

<!-- Background orbs -->
<div class="orb orb-1" aria-hidden="true"></div>
<div class="orb orb-2" aria-hidden="true"></div>
<div class="orb orb-3" aria-hidden="true"></div>

<div class="auth-split">
    <!-- Branding panel -->
    <div class="auth-brand" aria-hidden="true">
        <div class="brand-icon">🅿️</div>
        <h1 class="brand-title">
            Parking Aid<br>
            <span>Sensor System</span>
        </h1>
        <p class="brand-desc">
            Precision parking sensor calibration and student performance analytics for driving instructors.
        </p>
        <div class="brand-features">
            <div class="brand-feature">
                <div class="brand-feature-icon">📡</div>
                <span>Real-time multi-sensor monitoring</span>
            </div>
            <div class="brand-feature">
                <div class="brand-feature-icon">📊</div>
                <span>Session analytics and PDF reports</span>
            </div>
            <div class="brand-feature">
                <div class="brand-feature-icon">👥</div>
                <span>Student management and grading</span>
            </div>
        </div>
    </div>

    <!-- Form panel -->
    <div class="auth-form-panel">
        <div class="auth-form-wrap">
            <div class="login-card" role="main">
                <div class="card-header">
                    <h2>Welcome back</h2>
                    <p>Sign in to your instructor account</p>
                </div>

                <?php if ($error): ?>
                    <div class="error-pill" role="alert" aria-live="polite">
                        🚫 <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" aria-label="Login form">
                    <div class="field-group">
                        <label class="field-label" for="username">Username</label>
                        <div class="field-box">
                            <span class="field-icon" aria-hidden="true">👤</span>
                            <input class="field-input" type="text" id="username" name="username"
                                   placeholder="Enter username" required
                                   autocomplete="username" aria-required="true">
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label" for="password">Password</label>
                        <div class="field-box">
                            <span class="field-icon" aria-hidden="true">🔒</span>
                            <input class="field-input" type="password" id="password" name="password"
                                   placeholder="Enter password" required
                                   autocomplete="current-password" aria-required="true">
                        </div>
                    </div>

                    <button type="submit" class="btn-login">Sign In →</button>
                </form>

                <div class="card-footer">
                    <a href="dashboard.php">← Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
