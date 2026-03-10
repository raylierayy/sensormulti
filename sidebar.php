<style>
/* ── Sidebar override: uses design tokens from style.css ── */
.sidebar {
    position: fixed;
    width: 260px;
    height: 100vh;
    background: linear-gradient(180deg,
        rgba(10, 14, 26, 0.97) 0%,
        rgba(18, 24, 42, 0.97) 100%);
    backdrop-filter: blur(24px) saturate(180%);
    -webkit-backdrop-filter: blur(24px) saturate(180%);
    border-right: 1px solid rgba(255, 255, 255, 0.07);
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 4px 0 32px rgba(0, 0, 0, 0.45);
    padding: 0;
}

.sidebar-logo {
    padding: 26px 20px 18px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    display: flex;
    align-items: center;
    gap: 12px;
}

.sidebar-logo-icon {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3em;
    flex-shrink: 0;
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45);
}

.sidebar-logo-text h2 {
    color: rgba(255, 255, 255, 0.95);
    font-size: 0.95em;
    font-weight: 700;
    margin: 0;
    letter-spacing: -0.01em;
    border: none;
    padding: 0;
    text-align: left;
}

.sidebar-logo-text p {
    color: rgba(255, 255, 255, 0.40);
    font-size: 0.73em;
    margin: 2px 0 0;
    font-weight: 500;
}

.sidebar nav {
    flex: 1;
    padding: 10px 12px;
    display: flex;
    flex-direction: column;
    gap: 1px;
}

.sidebar-section-label {
    color: rgba(255, 255, 255, 0.25);
    font-size: 0.68em;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    padding: 14px 8px 6px;
}

.sidebar nav a,
.sidebar nav form button {
    display: flex;
    align-items: center;
    gap: 11px;
    color: rgba(255, 255, 255, 0.58);
    text-decoration: none;
    padding: 10px 12px;
    border-radius: 12px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    border: none;
    background: transparent;
    text-align: left;
    font-size: 0.875em;
    font-weight: 500;
    width: 100%;
    position: relative;
    overflow: hidden;
    font-family: 'Inter', -apple-system, sans-serif;
}

.sidebar nav a:hover,
.sidebar nav form button:hover {
    background: rgba(255, 255, 255, 0.07);
    color: rgba(255, 255, 255, 0.92);
    transform: translateX(3px);
}

.sidebar nav a.active {
    background: linear-gradient(135deg,
        rgba(99, 102, 241, 0.22),
        rgba(139, 92, 246, 0.18));
    color: #fff;
    border: 1px solid rgba(99, 102, 241, 0.32);
    box-shadow: 0 0 18px rgba(99, 102, 241, 0.18);
    font-weight: 600;
}

/* Active left indicator */
.sidebar nav a.active::before {
    content: '';
    position: absolute;
    left: 0; top: 20%; bottom: 20%;
    width: 3px;
    background: linear-gradient(180deg, #6366f1, #8b5cf6);
    border-radius: 0 3px 3px 0;
}

.sidebar-nav-icon {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9em;
    flex-shrink: 0;
    background: rgba(255, 255, 255, 0.07);
    transition: background 0.2s;
}

.sidebar nav a:hover .sidebar-nav-icon,
.sidebar nav a.active .sidebar-nav-icon {
    background: rgba(99, 102, 241, 0.25);
}

.sidebar-logout {
    padding: 14px 12px;
    border-top: 1px solid rgba(255, 255, 255, 0.07);
}

.sidebar .logout-btn {
    display: flex !important;
    align-items: center;
    gap: 11px;
    background: rgba(239, 68, 68, 0.10) !important;
    color: rgba(239, 68, 68, 0.85) !important;
    border: 1px solid rgba(239, 68, 68, 0.22) !important;
    border-radius: 12px !important;
    padding: 10px 12px !important;
    width: 100% !important;
    font-weight: 600;
    font-size: 0.875em;
    cursor: pointer;
    font-family: 'Inter', -apple-system, sans-serif;
    transition: all 0.2s ease !important;
}

.sidebar .logout-btn:hover {
    background: rgba(239, 68, 68, 0.22) !important;
    color: #fff !important;
    border-color: rgba(239, 68, 68, 0.50) !important;
    transform: none !important;
}

/* User info at bottom of nav (above logout) */
.sidebar-user {
    padding: 12px;
    border-top: 1px solid rgba(255,255,255,0.07);
    display: flex;
    align-items: center;
    gap: 10px;
}

.sidebar-user-avatar {
    width: 34px; height: 34px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1em;
    flex-shrink: 0;
}

.sidebar-user-info {
    flex: 1;
    min-width: 0;
}

.sidebar-user-name {
    color: rgba(255,255,255,0.85);
    font-size: 0.82em;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sidebar-user-role {
    color: rgba(255,255,255,0.35);
    font-size: 0.72em;
    margin-top: 1px;
}

/* ── Light Mode Overrides ── */
[data-theme="light"] .sidebar {
    background: rgba(255, 255, 255, 0.70) !important;
    backdrop-filter: blur(24px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(24px) saturate(180%) !important;
    border-right-color: rgba(99, 102, 241, 0.15) !important;
    box-shadow: 4px 0 32px rgba(0, 0, 0, 0.08) !important;
}

[data-theme="light"] .sidebar-logo {
    border-bottom-color: rgba(0, 0, 0, 0.08) !important;
}

[data-theme="light"] .sidebar-logo-text h2 {
    color: #111827 !important;
}

[data-theme="light"] .sidebar-logo-text p {
    color: #6b7280 !important;
}

[data-theme="light"] .sidebar-section-label {
    color: #9ca3af !important;
}

[data-theme="light"] .sidebar nav a,
[data-theme="light"] .sidebar nav form button {
    color: #4b5563 !important;
}

[data-theme="light"] .sidebar nav a:hover,
[data-theme="light"] .sidebar nav form button:hover {
    background: rgba(99, 102, 241, 0.08) !important;
    color: #111827 !important;
}

[data-theme="light"] .sidebar nav a.active {
    color: #4f46e5 !important;
}

[data-theme="light"] .sidebar-nav-icon {
    background: rgba(0, 0, 0, 0.06) !important;
}

[data-theme="light"] .sidebar nav a:hover .sidebar-nav-icon,
[data-theme="light"] .sidebar nav a.active .sidebar-nav-icon {
    background: rgba(99, 102, 241, 0.15) !important;
}

[data-theme="light"] .sidebar-logout {
    border-top-color: rgba(0, 0, 0, 0.08) !important;
}

[data-theme="light"] .sidebar-user {
    border-top-color: rgba(0, 0, 0, 0.08) !important;
}

[data-theme="light"] .sidebar-user-name {
    color: #111827 !important;
}

[data-theme="light"] .sidebar-user-role {
    color: #6b7280 !important;
}
</style>

<aside class="sidebar" role="navigation" aria-label="Main navigation">
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon" aria-hidden="true">🅿️</div>
        <div class="sidebar-logo-text">
            <h2>Parking Aid</h2>
            <p>Admin Dashboard</p>
        </div>
    </div>

    <nav>
        <span class="sidebar-section-label">Navigation</span>
        <a href="dashboard.php"
           <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'class="active" aria-current="page"' : ''; ?>
           aria-label="Dashboard">
            <span class="sidebar-nav-icon" aria-hidden="true">📊</span>
            Dashboard
        </a>

        <span class="sidebar-section-label">Students</span>
        <a href="add_student.php"
           <?php echo (basename($_SERVER['PHP_SELF']) == 'add_student.php') ? 'class="active" aria-current="page"' : ''; ?>
           aria-label="Add new student">
            <span class="sidebar-nav-icon" aria-hidden="true">➕</span>
            Add Student
        </a>
        <a href="students.php"
           <?php echo (basename($_SERVER['PHP_SELF']) == 'students.php') ? 'class="active" aria-current="page"' : ''; ?>
           aria-label="Manage students">
            <span class="sidebar-nav-icon" aria-hidden="true">👥</span>
            Manage Students
        </a>

        <span class="sidebar-section-label">Testing</span>
        <a href="calibrate.php"
           <?php echo (basename($_SERVER['PHP_SELF']) == 'calibrate.php') ? 'class="active" aria-current="page"' : ''; ?>
           aria-label="Conduct parking test">
            <span class="sidebar-nav-icon" aria-hidden="true">🚗</span>
            Conduct Test
        </a>
    </nav>

    <?php if (isset($_SESSION['username'])): ?>
    <div class="sidebar-user">
        <div class="sidebar-user-avatar" aria-hidden="true">👤</div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name">
                <?php echo htmlspecialchars($_SESSION['username']); ?>
            </div>
            <div class="sidebar-user-role">Instructor</div>
        </div>
    </div>
    <?php endif; ?>

    <div class="sidebar-logout">
        <form method="POST" action="logout.php">
            <button type="submit" class="logout-btn" aria-label="Logout">
                <span aria-hidden="true" style="font-size:1em;">🚪</span>
                Logout
            </button>
        </form>
    </div>
</aside>
