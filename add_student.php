<?php
require 'session_check.php';
require 'db_connection.php';

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname  = $_POST['firstname'];
    $lastname   = $_POST['lastname'];
    $instructor = $_POST['instructor'];

    $sql    = "INSERT INTO Students (firstname, lastname, instructor) VALUES (?, ?, ?)";
    $params = array($firstname, $lastname, $instructor);
    $stmt   = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        $message = "New student added successfully!";
    } else {
        $error = "Error adding student: " . print_r(sqlsrv_errors(), true);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - Sensor System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="theme-modern.css">
    <script>document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'dark');</script>
    <style>
        /* ── Centered form card ─────────────────────────────── */
        .form-center-card {
            max-width: 580px;
            width: 100%;
            margin: 0 auto;
            padding: 36px 40px;
        }

        .form-center-card h3 {
            font-size: 1.3em;
            font-weight: 800;
            color: #fff;
            margin: 0 0 6px;
            letter-spacing: -0.02em;
        }

        .form-center-card .card-subtitle {
            color: rgba(255,255,255,0.45);
            font-size: 0.88em;
            margin-bottom: 28px;
        }

        /* ── Input with icon ────────────────────────────────── */
        .field-wrap {
            position: relative;
            margin-bottom: 20px;
        }

        .field-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: rgba(255,255,255,0.70);
            font-size: 0.82em;
            text-transform: uppercase;
            letter-spacing: 0.07em;
        }

        .field-inner {
            position: relative;
        }

        .field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.05em;
            pointer-events: none;
            opacity: 0.55;
            z-index: 1;
        }

        .field-inner input {
            padding-left: 44px !important;
            border-radius: 14px !important;
            transition: all 0.2s ease;
        }

        .field-inner input:focus {
            border-color: rgba(99,102,241,0.80) !important;
            background: rgba(99,102,241,0.08) !important;
            box-shadow: 0 0 0 4px rgba(99,102,241,0.18) !important;
        }

        .field-inner input:focus + .field-focus-line {
            transform: scaleX(1);
        }

        .field-readonly .field-inner input {
            opacity: 0.75;
            cursor: default;
        }

        /* ── Submit button ──────────────────────────────────── */
        .btn-submit-form {
            width: 100%;
            padding: 15px;
            margin-top: 8px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
            border-radius: 14px;
            color: #fff;
            font-size: 1em;
            font-weight: 700;
            letter-spacing: 0.02em;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(99,102,241,0.45);
            transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
            font-family: 'Inter', sans-serif;
            position: relative;
            overflow: hidden;
        }

        .btn-submit-form::before {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: left 0.5s;
        }

        .btn-submit-form:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 36px rgba(99,102,241,0.65);
        }

        .btn-submit-form:hover::before { left: 100%; }
        .btn-submit-form:active { transform: translateY(0) scale(0.98); }

        .form-footer-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: rgba(255,255,255,0.40);
            text-decoration: none;
            font-size: 0.875em;
            transition: color 0.2s;
        }

        .form-footer-link:hover { color: rgba(255,255,255,0.75); }

        /* ── Divider ─────────────────────────────────────────── */
        .form-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.14), transparent);
            margin: 24px 0;
        }

        /* ── Light Mode Overrides ─────────────────────────────── */
        [data-theme="light"] .form-center-card h3 {
            color: #111827;
        }
        [data-theme="light"] .form-center-card .card-subtitle {
            color: #6b7280;
        }
        [data-theme="light"] .field-label {
            color: #4b5563;
        }
        [data-theme="light"] .form-footer-link {
            color: #6b7280;
        }
        [data-theme="light"] .form-footer-link:hover {
            color: #374151;
        }
        [data-theme="light"] .form-divider {
            background: linear-gradient(90deg, transparent, rgba(99,102,241,0.20), transparent);
        }
    </style>
</head>
<body>

<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">
                <a href="students.php">Students</a> › Add New Student
            </div>
            <div class="profile">
                <button id="themeToggle" class="theme-toggle-btn" title="Switch to Light Mode" aria-label="Toggle theme">☀️</button>
                <div style="width:34px;height:34px;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.13);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1em;cursor:pointer;" title="Notifications">🔔</div>
                <div style="width:34px;height:34px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:0.9em;" title="Profile">👤</div>
            </div>
        </header>

        <section class="content-section animate-in">
            <div class="glass-card-modern form-center-card">
                <!-- Card header -->
                <div style="display:flex; align-items:center; gap:14px; margin-bottom:24px;">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4em;box-shadow:0 8px 20px rgba(99,102,241,0.45);flex-shrink:0;">👤</div>
                    <div>
                        <h3 style="font-size:1.25em;font-weight:800;color:#fff;margin:0 0 4px;letter-spacing:-0.02em;">Register New Student</h3>
                        <p style="color:rgba(255,255,255,0.45);font-size:0.85em;margin:0;">Fill in the student details below to enroll them.</p>
                    </div>
                </div>

                <?php if ($message): ?>
                    <div class="msg-success">✅ <?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="msg-error">❌ <?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="" autocomplete="off">
                    <div class="field-wrap">
                        <label class="field-label" for="firstname">First Name</label>
                        <div class="field-inner">
                            <span class="field-icon">✏️</span>
                            <input type="text" id="firstname" name="firstname"
                                   placeholder="e.g. Juan" required
                                   aria-required="true">
                        </div>
                    </div>

                    <div class="field-wrap">
                        <label class="field-label" for="lastname">Last Name</label>
                        <div class="field-inner">
                            <span class="field-icon">✏️</span>
                            <input type="text" id="lastname" name="lastname"
                                   placeholder="e.g. dela Cruz" required
                                   aria-required="true">
                        </div>
                    </div>

                    <div class="form-divider"></div>

                    <div class="field-wrap field-readonly">
                        <label class="field-label" for="instructor">Instructor</label>
                        <div class="field-inner">
                            <span class="field-icon">🎓</span>
                            <input type="text" id="instructor" name="instructor"
                                   value="<?php echo htmlspecialchars($_SESSION['username']); ?>"
                                   required aria-required="true">
                        </div>
                    </div>

                    <button type="submit" class="btn-submit-form" aria-label="Register student">
                        Register Student →
                    </button>
                </form>

                <a href="students.php" class="form-footer-link" aria-label="View all students">
                    ← View all students
                </a>
            </div>
        </section>
    </main>
</div>

<script>
/* Theme toggle */
(function () {
    const html = document.documentElement;
    const saved = localStorage.getItem('theme') || 'dark';
    html.setAttribute('data-theme', saved);
    const btn = document.getElementById('themeToggle');
    if (btn) {
        updateThemeIcon(btn, saved);
        btn.addEventListener('click', function () {
            const cur = html.getAttribute('data-theme');
            const next = cur === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            updateThemeIcon(btn, next);
        });
    }
    function updateThemeIcon(btn, theme) {
        btn.textContent = theme === 'dark' ? '☀️' : '🌙';
        btn.title = theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';
    }
})();
</script>

</body>
</html>
