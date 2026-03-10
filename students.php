<?php
require 'session_check.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Sensor System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="theme-modern.css">
    <style>
        /* ── Table enhancements ──────────────────────────────── */
        .students-table-wrap {
            overflow-x: auto;
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9em;
        }

        .students-table thead tr {
            background: rgba(255,255,255,0.05);
        }

        .students-table th {
            padding: 13px 16px;
            text-align: left;
            color: rgba(255,255,255,0.45);
            font-weight: 700;
            font-size: 0.75em;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border-bottom: 1px solid rgba(255,255,255,0.10);
            white-space: nowrap;
        }

        .students-table tbody tr {
            transition: background 0.15s ease;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .students-table tbody tr:hover {
            background: rgba(99,102,241,0.06);
        }

        .students-table tbody tr:hover td {
            background: transparent;
        }

        .students-table td {
            padding: 14px 16px;
            color: rgba(255,255,255,0.88);
            vertical-align: middle;
            border-bottom: none;
        }

        /* ID badge */
        .id-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px; height: 32px;
            background: rgba(99,102,241,0.18);
            border: 1px solid rgba(99,102,241,0.35);
            border-radius: 8px;
            font-size: 0.82em;
            font-weight: 700;
            color: #a5b4fc;
        }

        /* Student name cell */
        .student-name {
            font-weight: 600;
            color: #fff;
        }

        .student-instructor {
            font-size: 0.82em;
            color: rgba(255,255,255,0.45);
        }

        /* Tests count badge */
        .tests-count {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(6,182,212,0.12);
            border: 1px solid rgba(6,182,212,0.30);
            color: #67e8f9;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.8em;
            font-weight: 700;
        }

        /* Action button pill */
        .btn-action-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff !important;
            border-radius: 10px;
            font-size: 0.82em;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(99,102,241,0.35);
            white-space: nowrap;
        }

        .btn-action-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99,102,241,0.55);
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255,255,255,0.40);
        }

        .empty-state-icon {
            font-size: 3em;
            margin-bottom: 12px;
            display: block;
        }

        .empty-state p {
            margin: 6px 0 0;
            font-size: 0.9em;
        }

        /* Search bar */
        .search-wrap {
            position: relative;
            max-width: 300px;
        }

        .search-wrap .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.50;
            pointer-events: none;
        }

        .search-input {
            padding: 9px 14px 9px 36px !important;
            background: rgba(255,255,255,0.06) !important;
            border: 1px solid rgba(255,255,255,0.12) !important;
            border-radius: 10px !important;
            font-size: 0.88em !important;
            color: rgba(255,255,255,0.90) !important;
            width: 100%;
        }

        .search-input::placeholder { color: rgba(255,255,255,0.30) !important; }

        .search-input:focus {
            border-color: rgba(99,102,241,0.60) !important;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15) !important;
            background: rgba(99,102,241,0.07) !important;
        }
    </style>
</head>
<body>

<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">Registered Students 👥</div>
            <div class="profile">
                <div style="width:34px;height:34px;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.13);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1em;cursor:pointer;" title="Notifications">🔔</div>
                <div style="width:34px;height:34px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:0.9em;" title="Profile">👤</div>
            </div>
        </header>

        <section class="content-section animate-in">
            <div class="glass-card-modern" style="padding:0; overflow:hidden;">
                <!-- Card header -->
                <div style="padding:22px 26px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.08); flex-wrap:wrap; gap:12px;">
                    <div>
                        <h3 style="margin:0; font-size:1.05em; font-weight:700; color:#fff;">All Students</h3>
                        <p style="margin:3px 0 0; font-size:0.82em; color:rgba(255,255,255,0.40);">Manage registered students and grades</p>
                    </div>
                    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                        <div class="search-wrap">
                            <span class="search-icon">🔍</span>
                            <input type="text" id="studentSearch" class="search-input"
                                   placeholder="Search students…"
                                   oninput="filterStudents(this.value)"
                                   aria-label="Search students">
                        </div>
                        <a href="add_student.php" class="btn-action-pill" aria-label="Add new student">
                            ＋ Add New
                        </a>
                    </div>
                </div>

                <!-- Table -->
                <div class="students-table-wrap">
                    <table class="students-table" id="studentsTable" role="table" aria-label="Students list">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Student</th>
                                <th scope="col">Instructor</th>
                                <th scope="col">Tests</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody id="studentsBody">
                            <?php
                            $sql    = "SELECT * FROM Students ORDER BY ID DESC";
                            $result = sqlsrv_query($conn, $sql);

                            if ($result !== false && sqlsrv_has_rows($result)) {
                                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                                    $fn = htmlspecialchars($row['firstname']);
                                    $ln = htmlspecialchars($row['lastname']);
                                    $ins = htmlspecialchars($row['instructor']);
                                    echo "<tr>";
                                    echo "<td><span class='id-badge'>{$row['ID']}</span></td>";
                                    echo "<td>
                                            <div class='student-name'>{$fn} {$ln}</div>
                                          </td>";
                                    echo "<td><span class='student-instructor'>🎓 {$ins}</span></td>";
                                    echo "<td><span class='tests-count'>🧪 {$row['tests_count']}</span></td>";

                                    $status = $row['pass_fail'];
                                    if (empty($status)) {
                                        echo "<td><span class='status-pill status-pending'>⏳ Pending</span></td>";
                                    } elseif (strtolower($status) == 'pass') {
                                        echo "<td><span class='status-pill status-pass'>✅ Pass</span></td>";
                                    } else {
                                        echo "<td><span class='status-pill status-fail'>❌ Fail</span></td>";
                                    }

                                    echo "<td><a href='student_detail.php?id={$row['ID']}' class='btn-action-pill' aria-label='View student {$fn} {$ln}'>👁 View</a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' style='padding:0;'>
                                        <div class='empty-state'>
                                            <span class='empty-state-icon'>👥</span>
                                            <strong style='color:rgba(255,255,255,0.65);'>No students yet</strong>
                                            <p><a href='add_student.php' style='color:#a5b4fc;'>Register the first student →</a></p>
                                        </div>
                                      </td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
function filterStudents(query) {
    const q = query.toLowerCase().trim();
    const rows = document.querySelectorAll('#studentsBody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = (q === '' || text.includes(q)) ? '' : 'none';
    });
}
</script>

</body>
</html>
