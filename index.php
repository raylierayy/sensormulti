<?php
// Optional: Show a glass loading screen before redirect
$show_loading = false; // Set to true if you want a loading animation

if (!$show_loading) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading...</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .loading-container {
            text-align: center;
            background: rgba(255,255,255,0.14);
            backdrop-filter: blur(20px) saturate(140%);
            border: 1px solid rgba(255,255,255,0.28);
            border-radius: 32px;
            padding: 60px 80px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.25);
        }

        .loading-icon {
            font-size: 5em;
            margin-bottom: 20px;
            animation: pulse 1.5s ease-in-out infinite;
        }

        .loading-text {
            color: rgba(255,255,255,0.95);
            font-size: 1.3em;
            font-weight: 600;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.7; }
        }
    </style>
    <script>
        // Redirect after 1 second
        setTimeout(function() {
            window.location.href = 'dashboard.php';
        }, 1000);
    </script>
</head>
<body>
    <div class="loading-container">
        <div class="loading-icon">🅿️</div>
        <div class="loading-text">Loading Parking Aid System...</div>
    </div>
</body>
</html>
