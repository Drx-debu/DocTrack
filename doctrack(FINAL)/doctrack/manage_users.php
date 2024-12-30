<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "doctrack";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$table = 'user_info';
$columns = ['id', 'username', 'email'];
$sql = "SELECT * FROM $table";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $username = htmlspecialchars($_POST['username']);
        $email = htmlspecialchars($_POST['email']);
        $query = "UPDATE $table SET username=?, email=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssi', $username, $email, $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "User updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating user: " . $stmt->error;
        }
        header("Location: manage_users.php");
        exit;
    }
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $conn->begin_transaction();
        try {
            $query = "DELETE FROM review WHERE user_id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            if (!$stmt->execute()) {
                throw new Exception("Error deleting reviews: " . $stmt->error);
            }
            $query = "DELETE FROM $table WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            if (!$stmt->execute()) {
                throw new Exception("Error deleting user: " . $stmt->error);
            }
            $conn->commit();
            $_SESSION['success'] = "User and associated reviews deleted successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error'] = "Error deleting user: " . $e->getMessage();
        }    
        header("Location: manage_users.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="styles_manage_entries.css">
    <script>
        function populateForm(data) {
            document.getElementById('username').value = data.username;
            document.getElementById('email').value = data.email;
            document.getElementById('userId').value = data.id;
        }
        window.onload = function() {
            <?php if (isset($_SESSION['success'])): ?>
                alert("<?= $_SESSION['success']; ?>");
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                alert("<?= $_SESSION['error']; ?>");
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        }
    </script>
</head>
<body>
<header>
    <div class="header-logo">
        <img src="logo2.png" alt="DocTrack Logo">
    </div>
    <nav>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_users.php" class="active">Manage Users</a></li>
            <li><a href="manage_patients.php">Manage Patients</a></li>
            <li><a href="manage_doctors.php">Manage Doctors</a></li>
            <li><a href="manage_appointments.php">Manage Appointments</a></li>
            <li><a href="reviews.php">Manage Reviews</a></li>
        </ul>
    </nav>
    <div class="Log_In">
        <?php
        if (isset($_SESSION['admin_id'])) {
            echo '<a href="log_out.php"><button><strong>Log Out</strong></button></a>';
        } else {
            echo '<a href="main.php"><button><strong>Back to Home Page</strong></button></a>';
        }
        ?>
    </div>
</header>
<main>
    <h1>Manage Users</h1>
    <h2>Edit User</h2>
    <form method="POST" class="form">
        <input type="hidden" name="id" id="userId">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        <br><br>
        <button type="submit" name="edit"><strong>Update User</strong></button>
    </form>
    <br><hr><br>
    <h2>Existing Users</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']); ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td>
                        <button class="button-edit" onclick="populateForm(<?= htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']); ?>">
                            <button class="button-delete" id="delete" type="submit" name="delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>
<footer>
    <div class="footer-content">
        <div class="footer-left">
            <div class="header-logo">
                <img src="logo2.png" alt="DocTrack">
            </div>
        </div>
        <div>
            <p>||  © Since 2024 ||</p>
        </div>
        <div class="footer-right">
            <a href="#">Gmail</a> | <a href="#">Instagram</a>
            <br><br>
            <a href="#">Facebook</a> | <a href="#">LinkedIn</a>
        </div>
    </div>
</footer>
</body>
</html>