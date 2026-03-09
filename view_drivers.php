<?php
require 'session_check.php';
require 'db_connection.php';

$driver_id = isset($_GET['driver_id']) ? intval($_GET['driver_id']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Drivers - Sensor System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .complex-table { font-size:0.88em; }
        .complex-table th { vertical-align:top; }
        .sub-header { font-size:0.8em; color:#666; font-weight:normal; }
        .sensor-group { border-left:2px solid #ddd; }
        .badge-yes { color:#166534; font-weight:700; }
        .badge-no  { color:#991b1b; font-weight:700; }
    </style>
</head>
<body>
<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">
                <?php if ($driver_id): ?>
                    <a href="view_drivers.php" style="text-decoration:none;color:inherit;">Drivers</a> › Test History
                <?php else: ?>
                    Registered Drivers 👥
                <?php endif; ?>
            </div>
        </header>

        <section class="content-section">

            <?php if ($driver_id): ?>
                <!-- ── DRIVER TEST HISTORY ───────────────────── -->
                <?php
                $driver_sql = "SELECT * FROM drivers WHERE id = ?";
                $driver_result = sqlsrv_query($conn, $driver_sql, [$driver_id]);

                if ($driver_result === false || !sqlsrv_has_rows($driver_result)) {
                    echo "<div class='card'><p>Driver not found.</p><a href='view_drivers.php'>Back</a></div>";
                } else {
                    $driver = sqlsrv_fetch_array($driver_result, SQLSRV_FETCH_ASSOC);
                ?>
                <div class="card" style="overflow-x:auto;">
                    <h3>Test History for: <?= htmlspecialchars($driver['firstname'] . ' ' . $driver['lastname']) ?></h3>

                    <table class="complex-table">
                        <thead>
                            <tr>
                                <th rowspan="2">ID</th>
                                <th rowspan="2">Allowed<br>Err (cm)</th>

                                <!-- Main Sensor -->
                                <th colspan="4" class="sensor-group" style="background:#eff6ff;">🔵 Main Sensor</th>

                                <th rowspan="2">Date</th>
                            </tr>
                            <tr>
                                <!-- Main -->
                                <th class="sensor-group sub-header">Baseline<br>(cm)</th>
                                <th class="sub-header">Final<br>(cm)</th>
                                <th class="sub-header">Error<br>(cm)</th>
                                <th class="sub-header">Pass?</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $tests_sql    = "SELECT * FROM drivedata WHERE id = ? ORDER BY testid DESC";
                        $tests_result = sqlsrv_query($conn, $tests_sql, [$driver_id]);

                        if ($tests_result !== false && sqlsrv_has_rows($tests_result)) {
                            while ($t = sqlsrv_fetch_array($tests_result, SQLSRV_FETCH_ASSOC)) {

                                $passClass = function($val) {
                                    return strtolower($val) === 'yes' ? 'badge-yes' : 'badge-no';
                                };
                                $passLabel = function($val) {
                                    return strtolower($val) === 'yes' ? '✅ Yes' : '❌ No';
                                };

                                $dateStr = 'N/A';
                                if (!empty($t['created_at'])) {
                                    $dateStr = ($t['created_at'] instanceof DateTime)
                                        ? $t['created_at']->format('Y-m-d H:i')
                                        : $t['created_at'];
                                }

                                echo "<tr>";
                                echo "<td>{$t['testid']}</td>";
                                echo "<td>{$t['cm_err_allowed']} cm</td>";

                                // Main
                                echo "<td class='sensor-group'>{$t['calib_distance_M']}</td>";
                                echo "<td>{$t['final_distance_M']}</td>";
                                echo "<td>{$t['cm_err_computed_M']}</td>";
                                echo "<td class='{$passClass($t['within_err_allowed_M'])}'>{$passLabel($t['within_err_allowed_M'])}</td>";

                                echo "<td>{$dateStr}</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='15'>No tests recorded yet.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                    <br>
                    <button onclick="window.location.href='view_drivers.php'" class="action-btn" style="background:#666;">← Back to List</button>
                </div>
                <?php } ?>

            <?php else: ?>
                <!-- ── DRIVER LIST ───────────────────────────── -->
                <div class="card">
                    <h3>All Registered Drivers</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $result = sqlsrv_query($conn, "SELECT * FROM drivers");
                        if ($result !== false && sqlsrv_has_rows($result)) {
                            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>{$row['id']}</td>";
                                echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['lastname'])  . "</td>";
                                echo "<td>" . htmlspecialchars($row['email'])     . "</td>";
                                echo "<td>" . htmlspecialchars($row['phone'])     . "</td>";
                                echo "<td><a href='view_drivers.php?driver_id={$row['id']}' class='action-btn' style='padding:5px 10px;font-size:.9em;'>View History</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No drivers found. <a href='add_driver.php'>Add one now</a>.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </section>
    </main>
</div>
</body>
</html>
