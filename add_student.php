<?php
require 'session_check.php';
require 'db_connection.php';

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $instructor = $_POST['instructor'];

    $sql = "INSERT INTO Students (firstname, lastname, instructor) VALUES (?, ?, ?)";
    $params = array($firstname, $lastname, $instructor);

    $stmt = sqlsrv_query($conn, $sql, $params);

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
</head>
<body>

<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome">Add New Student 👤</div>
            <div class="profile">
                <span title="Notifications">🔔</span>
                <span title="Profile">👤</span>
            </div>
        </header>

        <section class="content-section">
            <div class="card" style="max-width: 600px;">
                <h3>Enter Student Details</h3>
                <?php if ($message): ?>
                    <div style="color: green; margin-bottom: 15px; background: #f0fdf4; padding: 10px; border-left: 4px solid #22c55e;">✅ <?php echo $message; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div style="color: red; margin-bottom: 15px; background: #fef2f2; padding: 10px; border-left: 4px solid #ef4444;">❌ <?php echo $error; ?></div>
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
                        <label for="instructor">Instructor Name</label>
                        <input type="text" id="instructor" name="instructor" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>
                    </div>
                    
                    <button type="submit" class="action-btn" style="margin-top: 10px;">Register Student</button>
                    <a href="students.php" style="margin-left: 15px; color: #666; text-decoration: none;">View All Students →</a>
                </form>
            </div>
        </section>
    </main>
</div>

</body>
</html>
