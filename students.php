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
</head>
<body>

<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">Registered Students 👥</div>
            <div class="profile">
                <span title="Notifications">🔔</span>
                <span title="Profile">👤</span>
            </div>
        </header>

        <section class="content-section">
            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                    <h3 style="margin:0;">All Students</h3>
                    <a href="add_student.php" class="action-btn" style="padding: 8px 16px; font-size: 0.9em;">+ Add New</a>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Instructor</th>
                            <th>Tests Count</th>
                            <th>Status (Pass/Fail)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM Students ORDER BY ID DESC";
                        $result = sqlsrv_query($conn, $sql);

                        if ($result !== false && sqlsrv_has_rows($result)) {
                            while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . $row['ID'] . "</td>";
                                echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['instructor']) . "</td>";
                                echo "<td>" . $row['tests_count'] . "</td>";
                                
                                $status = $row['pass_fail'];
                                if (empty($status)) {
                                    echo "<td><span style='color:#a1a1aa; font-weight:bold;'>Pending</span></td>";
                                } else if (strtolower($status) == 'pass') {
                                    echo "<td><span style='color:#166534; font-weight:bold;'>✅ Pass</span></td>";
                                } else {
                                    echo "<td><span style='color:#991b1b; font-weight:bold;'>❌ Fail</span></td>";
                                }

                                echo "<td><a href='student_detail.php?id=" . $row['ID'] . "' class='action-btn' style='padding: 5px 10px; font-size: 0.9em; background:#3b82f6;'>View / Grade</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No students found. <a href='add_student.php'>Add one now</a>.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

</body>
</html>
