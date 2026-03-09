<?php
require 'session_check.php';
require 'db_connection.php';

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    $sql = "INSERT INTO drivers (firstname, lastname, phone, email) VALUES (?, ?, ?, ?)";
    $params = array($firstname, $lastname, $phone, $email);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        $message = "New driver added successfully!";
    } else {
        $error = "Error adding driver: " . print_r(sqlsrv_errors(), true);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Driver - Sensor System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">Add New Driver 👤</div>
            <div class="profile">
                <span title="Notifications">🔔</span>
                <span title="Profile">👤</span>
            </div>
        </header>

        <section class="content-section">
            <div class="card">
                <h3>Enter Driver Details</h3>
                <?php if ($message): ?>
                    <div style="color: green; margin-bottom: 15px;"><?php echo $message; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div style="color: red; margin-bottom: 15px;"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="firstname">First Name</label>
                        <input type="text" id="firstname" name="firstname" required>
                    </div>
                    <div class="form-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" id="lastname" name="lastname" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <button type="submit" class="action-btn">Add Driver</button>
                    <a href="dashboard.php" style="margin-left: 10px; color: #666; text-decoration: none;">Cancel</a>
                </form>
            </div>
        </section>
    </main>
</div>

</body>
</html>
