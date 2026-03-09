<style>
.sidebar {
  position: fixed;
  width: 260px;
  height: 100vh;
  background: rgba(5, 8, 22, 0.92);
  backdrop-filter: blur(24px) saturate(180%);
  -webkit-backdrop-filter: blur(24px) saturate(180%);
  border-right: 1px solid rgba(255,255,255,0.08);
  padding: 0;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
  z-index: 1000;
  box-shadow: 4px 0 24px rgba(0,0,0,0.4);
}

.sidebar-logo {
  padding: 28px 20px 20px;
  border-bottom: 1px solid rgba(255,255,255,0.08);
  margin-bottom: 8px;
}

.sidebar-logo-icon {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4em;
  margin-bottom: 10px;
  box-shadow: 0 8px 24px rgba(102,126,234,0.4);
}

.sidebar-logo h2 {
  color: rgba(255,255,255,0.95);
  font-size: 1em;
  font-weight: 700;
  margin: 0;
  letter-spacing: -0.01em;
}

.sidebar-logo p {
  color: rgba(255,255,255,0.45);
  font-size: 0.78em;
  margin: 2px 0 0;
}

.sidebar nav {
  flex: 1;
  padding: 8px 12px;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.sidebar-section-label {
  color: rgba(255,255,255,0.3);
  font-size: 0.7em;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  padding: 12px 8px 6px;
  margin-top: 4px;
}

.sidebar nav a, .sidebar nav form button {
  display: flex;
  align-items: center;
  gap: 12px;
  color: rgba(255,255,255,0.65);
  text-decoration: none;
  padding: 11px 14px;
  border-radius: 12px;
  transition: all 0.2s cubic-bezier(0.4,0,0.2,1);
  cursor: pointer;
  border: none;
  background: transparent;
  text-align: left;
  font-size: 0.9em;
  font-weight: 500;
  width: 100%;
  position: relative;
  overflow: hidden;
}

.sidebar nav a:hover, .sidebar nav form button:hover {
  background: rgba(255,255,255,0.08);
  color: rgba(255,255,255,0.95);
  transform: translateX(3px);
}

.sidebar nav a.active {
  background: linear-gradient(135deg, rgba(102,126,234,0.25), rgba(118,75,162,0.25));
  color: #fff;
  border: 1px solid rgba(102,126,234,0.3);
  box-shadow: 0 0 16px rgba(102,126,234,0.2);
}

.sidebar-nav-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95em;
  flex-shrink: 0;
  background: rgba(255,255,255,0.08);
  transition: all 0.2s;
}

.sidebar nav a:hover .sidebar-nav-icon,
.sidebar nav a.active .sidebar-nav-icon {
  background: rgba(102,126,234,0.3);
}

.sidebar-logout {
  padding: 16px 12px;
  border-top: 1px solid rgba(255,255,255,0.08);
}

.sidebar .logout-btn {
  display: flex !important;
  align-items: center;
  gap: 12px;
  background: rgba(239,68,68,0.12) !important;
  color: rgba(239,68,68,0.9) !important;
  border: 1px solid rgba(239,68,68,0.25) !important;
  border-radius: 12px !important;
  padding: 11px 14px !important;
  width: 100% !important;
  font-weight: 600;
  font-size: 0.9em;
  transition: all 0.2s !important;
  cursor: pointer;
}

.sidebar .logout-btn:hover {
  background: rgba(239,68,68,0.25) !important;
  color: #fff !important;
  transform: none !important;
  border-color: rgba(239,68,68,0.5) !important;
}
</style>

<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">🅿️</div>
        <h2>Parking Aid</h2>
        <p>Admin Dashboard</p>
    </div>
    
    <nav>
        <span class="sidebar-section-label">Navigation</span>
        <a href="dashboard.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'class="active"' : ''; ?>>
            <span class="sidebar-nav-icon">📊</span>
            Dashboard
        </a>
        
        <span class="sidebar-section-label">Students</span>
        <a href="add_student.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'add_student.php') ? 'class="active"' : ''; ?>>
            <span class="sidebar-nav-icon">➕</span>
            Add Student
        </a>
        <a href="students.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'students.php') ? 'class="active"' : ''; ?>>
            <span class="sidebar-nav-icon">👥</span>
            Manage Students
        </a>
        
        <span class="sidebar-section-label">Testing</span>
        <a href="calibrate.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'calibrate.php') ? 'class="active"' : ''; ?>>
            <span class="sidebar-nav-icon">🚗</span>
            Conduct Test
        </a>
    </nav>
    
    <div class="sidebar-logout">
        <form method="POST" action="logout.php">
            <button type="submit" class="logout-btn">
                <span style="font-size:1em;">🚪</span>
                Logout
            </button>
        </form>
    </div>
</aside>
