<?php
require 'session_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sensor System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="theme-modern.css">
    <script>document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'dark');</script>
    <style>
        /* ── Hero card ────────────────────────────────────────── */
        .hero-card {
            padding: 32px 36px;
            background: linear-gradient(135deg,
                rgba(99,102,241,0.14) 0%,
                rgba(139,92,246,0.10) 100%);
            border-color: rgba(99,102,241,0.28);
        }

        .hero-stat {
            text-align: center;
            padding: 18px 26px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 16px;
            min-width: 120px;
        }

        .hero-stat-value {
            font-size: 2em;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 4px;
        }

        .hero-stat-label {
            font-size: 0.72em;
            color: rgba(255,255,255,0.40);
            text-transform: uppercase;
            letter-spacing: 0.07em;
            font-weight: 700;
        }

        /* ── Quick action cards ──────────────────────────────── */
        .action-card {
            padding: 28px;
            cursor: pointer;
            text-decoration: none;
            display: block;
            transition: transform 0.25s cubic-bezier(0.4,0,0.2,1),
                        box-shadow 0.25s cubic-bezier(0.4,0,0.2,1);
        }

        .action-card:hover {
            transform: translateY(-6px);
        }

        .action-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5em;
            margin-bottom: 18px;
        }

        .action-card h3 {
            color: #fff;
            margin: 0 0 8px;
            font-size: 1.05em;
            font-weight: 700;
        }

        .action-card p {
            color: rgba(255,255,255,0.48);
            margin: 0 0 20px;
            font-size: 0.85em;
            line-height: 1.55;
        }

        .action-cta {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 10px;
            font-size: 0.82em;
            font-weight: 700;
            color: #fff;
        }

        /* ── Status bar ───────────────────────────────────────── */
        .status-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 26px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .status-dot {
            width: 10px; height: 10px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(16,185,129,0.6);
            animation: status-pulse 2s ease-in-out infinite;
        }

        @keyframes status-pulse {
            0%  { box-shadow: 0 0 0 0 rgba(16,185,129,0.60); }
            70% { box-shadow: 0 0 0 8px rgba(16,185,129,0.00); }
            100%{ box-shadow: 0 0 0 0 rgba(16,185,129,0.00); }
        }

        /* ── Counter animation ───────────────────────────────── */
        .counter { display: inline-block; }

        /* ── Light Mode Overrides ─────────────────────────────── */
        [data-theme="light"] .hero-stat {
            background: rgba(99,102,241,0.06);
            border-color: rgba(99,102,241,0.15);
        }

        [data-theme="light"] .hero-stat-label {
            color: #6b7280;
        }

        [data-theme="light"] .action-card h3 {
            color: #111827;
        }

        [data-theme="light"] .action-card p {
            color: #6b7280;
        }

        [data-theme="light"] .hero-card h1 {
            color: #111827 !important;
        }

        [data-theme="light"] .hero-card p {
            color: #4b5563 !important;
        }

        /* Status bar inline text overrides */
        [data-theme="light"] .status-bar > div > div > div {
            color: #374151 !important;
        }
    </style>
</head>
<body>

<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <!-- Decorative orbs -->
        <div class="orb orb-purple" style="position:fixed;top:4%;right:8%;width:380px;height:380px;z-index:0;pointer-events:none;" aria-hidden="true"></div>
        <div class="orb orb-cyan"   style="position:fixed;bottom:8%;left:14%;width:320px;height:320px;z-index:0;pointer-events:none;" aria-hidden="true"></div>

        <header class="topbar" style="position:relative;z-index:1;">
            <div class="welcome" style="display:flex;align-items:center;gap:10px;">
                <span aria-hidden="true">👋</span>
                Welcome back,
                <span style="background:linear-gradient(135deg,#6366f1,#06b6d4);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-weight:800;">
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
            </div>
            <div class="profile">
                <button id="themeToggle" class="theme-toggle-btn" title="Switch to Light Mode" aria-label="Toggle theme">☀️</button>
                <div style="width:36px;height:36px;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1em;cursor:pointer;" title="Notifications" aria-label="Notifications">🔔</div>
                <div style="width:36px;height:36px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1em;cursor:pointer;box-shadow:0 4px 12px rgba(99,102,241,0.45);" title="Profile" aria-label="Profile">👤</div>
            </div>
        </header>

        <section class="dashboard" style="position:relative;z-index:1;">

            <!-- ── Hero ──────────────────────────────────────────── -->
            <div class="glass-card-modern hero-card animate-in">
                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;">
                    <div>
                        <h1 style="font-size:1.9em;font-weight:900;color:#fff;margin:0 0 8px;letter-spacing:-0.025em;line-height:1.2;">
                            Parking Aid
                            <span style="background:linear-gradient(135deg,#6366f1,#06b6d4);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
                                Sensor System
                            </span>
                        </h1>
                        <p style="color:rgba(255,255,255,0.48);margin:0;font-size:0.92em;">
                            Real-time parking sensor monitoring &amp; student management
                        </p>
                    </div>
                    <div style="display:flex;gap:14px;flex-wrap:wrap;">
                        <div class="hero-stat">
                            <div class="hero-stat-value" style="color:#6366f1;">
                                <span class="counter" data-target="3">0</span>
                            </div>
                            <div class="hero-stat-label">Sensors</div>
                        </div>
                        <div class="hero-stat">
                            <div class="hero-stat-value" style="color:#10b981;">Live</div>
                            <div class="hero-stat-label">Status</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Quick actions bento grid ──────────────────── -->
            <div class="bento-grid" style="grid-template-columns:repeat(3,1fr);" role="list">

                <!-- Add Student -->
                <a href="add_student.php"
                   class="glass-card-modern action-card animate-in hover-lift"
                   style="animation-delay:60ms;"
                   role="listitem"
                   aria-label="Add a new student">
                    <div class="action-icon"
                         style="background:linear-gradient(135deg,#6366f1,#8b5cf6);
                                box-shadow:0 8px 22px rgba(99,102,241,0.45);">➕</div>
                    <h3>Add New Student</h3>
                    <p>Register a new student for parking assessment tests.</p>
                    <span class="action-cta"
                          style="background:linear-gradient(135deg,#6366f1,#8b5cf6);
                                 box-shadow:0 4px 12px rgba(99,102,241,0.38);">
                        Get Started →
                    </span>
                </a>

                <!-- Manage Students -->
                <a href="students.php"
                   class="glass-card-modern action-card animate-in hover-lift"
                   style="animation-delay:120ms;"
                   role="listitem"
                   aria-label="Manage all students">
                    <div class="action-icon"
                         style="background:linear-gradient(135deg,#06b6d4,#0891b2);
                                box-shadow:0 8px 22px rgba(6,182,212,0.45);">👥</div>
                    <h3>Manage Students</h3>
                    <p>View scores, assign grades, and review test histories.</p>
                    <span class="action-cta"
                          style="background:linear-gradient(135deg,#06b6d4,#0891b2);
                                 box-shadow:0 4px 12px rgba(6,182,212,0.38);">
                        View All →
                    </span>
                </a>

                <!-- Start Test -->
                <a href="calibrate.php"
                   class="glass-card-modern action-card animate-in hover-lift"
                   style="animation-delay:180ms;
                          background:linear-gradient(135deg,rgba(16,185,129,0.10) 0%,rgba(6,182,212,0.07) 100%);
                          border-color:rgba(16,185,129,0.28);"
                   role="listitem"
                   aria-label="Start a new parking test">
                    <div class="action-icon"
                         style="background:linear-gradient(135deg,#10b981,#06b6d4);
                                box-shadow:0 8px 22px rgba(16,185,129,0.45);">🚗</div>
                    <h3>Start New Test</h3>
                    <p>Configure sensors and conduct a live parking test session.</p>
                    <span class="action-cta"
                          style="background:linear-gradient(135deg,#10b981,#06b6d4);
                                 box-shadow:0 4px 12px rgba(16,185,129,0.38);">
                        Start Now →
                    </span>
                </a>

            </div>

            <!-- ── System status ──────────────────────────────── -->
            <div class="glass-card-modern animate-in" style="animation-delay:240ms;">
                <div class="status-bar">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div class="status-dot" role="status" aria-label="System online"></div>
                        <div>
                            <div style="font-weight:700;color:#fff;font-size:0.95em;">ℹ️ System Status</div>
                            <div style="font-size:0.82em;color:rgba(255,255,255,0.42);margin-top:2px;">
                                All systems operational. Select an action above to begin.
                            </div>
                        </div>
                    </div>
                    <span style="background:rgba(16,185,129,0.15);color:#4ade80;border:1px solid rgba(74,222,128,0.30);padding:5px 14px;border-radius:999px;font-size:0.78em;font-weight:700;">
                        ● Online
                    </span>
                </div>
            </div>

        </section>
    </main>
</div>

<script>
/* Animated counters — clean up timers when the page is hidden/unloaded */
(function () {
    const timers = [];

    document.querySelectorAll('.counter').forEach(el => {
        const target = parseInt(el.dataset.target, 10);
        let current = 0;
        const step = Math.max(1, Math.floor(target / 20));
        const id = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = current;
            if (current >= target) clearInterval(id);
        }, 60);
        timers.push(id);
    });

    /* Clean up on page hide/unload to prevent memory leaks */
    function clearAll() { timers.forEach(id => clearInterval(id)); }
    window.addEventListener('pagehide', clearAll);
    window.addEventListener('beforeunload', clearAll);
})();

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
