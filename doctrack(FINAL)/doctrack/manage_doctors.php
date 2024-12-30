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
$table = 'doctors';
$sql = "SELECT * FROM $table";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit'])) {
        $id = $_POST['doctor_id'];
        $name = htmlspecialchars($_POST['name']);
        $specialization = htmlspecialchars($_POST['specialization']);
        $qualification = htmlspecialchars($_POST['qualifications']);
        $email = htmlspecialchars($_POST['email']);
        $chamber_address = htmlspecialchars($_POST['chamber_address']);
        $fees = floatval($_POST['fees']);
        $availability = htmlspecialchars($_POST['availability']);
        $query = "UPDATE $table SET name=?, specialization=?, qualifications=?, email=?, chamber_address=?, fees=?, availability=? WHERE doctor_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssssdsi', $name, $specialization, $qualification, $email, $chamber_address, $fees, $availability, $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Doctor updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating doctor: " . $stmt->error;
        }
        header("Location: manage_doctors.php");
        exit;
    }
    if (isset($_POST['delete'])) {
        $id = $_POST['doctor_id'];
        $conn->begin_transaction();
        try {
            $query = "DELETE FROM $table WHERE doctor_id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);

            if (!$stmt->execute()) {
                throw new Exception("Error deleting doctor: " . $stmt->error);
            }
            $conn->commit();
            $_SESSION['success'] = "Doctor deleted successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error'] = "Error deleting doctor: " . $e->getMessage();
        }
        header("Location: manage_doctors.php");
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
        document.getElementById('doctorId').value = data.doctor_id;
        document.getElementById('name').value = data.name;
        document.getElementById('specialization').value = data.specialization;
        document.getElementById('qualifications').value = data.qualification;
        document.getElementById('email').value = data.email;
        document.getElementById('chamber_address').value = data.chamber_address;
        document.getElementById('fees').value = data.fees;
        document.getElementById('availabality').value = data.availabality;  
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
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="manage_patients.php">Manage Patients</a></li>
            <li><a href="manage_doctors.php" class="active">Manage Doctors</a></li>
            <li><a href="manage_appointments.php">Manage Appointments</a></li>
            <li><a href="manage_reviews.php">Manage Reviews</a></li>
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
    <h1>Manage Doctors</h1>
    <h2>Edit Doctor</h2>
    <form method="POST" class="form">
        <input type="hidden" name="doctor_id" id="doctorId">

        <label for="name">Doctor's Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="specialization">Specialization:</label>
        <input type="text" name="specialization" id="specialization" required>

        <label for="qualifications">Qualification:</label>
        <input type="text" name="qualifications" id="qualifications" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="chamber_address">Chamber Address:</label>
        <input type="text" name="chamber_address" id="chamber_address" required>

        <label for="fees">Fees:</label>
        <input type="number" name="fees" id="fees" step="0.01" required>

        <label for="availability">Availability:</label>
        <input type="text" name="availability" id="availability" required>

        <button type="submit" name="edit"><strong>Update Doctor</strong></button>
    </form>
    <br><hr><br>
    <h2>Existing Doctors</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Doctor ID</th>
                <th>Doctor Name</th>
                <th>Specialization</th>
                <th>Qualification</th>
                <th>Email</th>
                <th>Chamber Address</th>
                <th>Fees</th>
                <th>Availability</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['doctor_id']); ?></td>
                        <td><?= htmlspecialchars($row['name']); ?></td>
                        <td><?= htmlspecialchars($row['specialization']); ?></td>
                        <td><?= htmlspecialchars($row['qualifications']); ?></td>
                        <td><?= htmlspecialchars($row['email']); ?></td>
                        <td><?= htmlspecialchars($row['chamber_address']); ?></td>
                        <td><?= htmlspecialchars($row['fees']); ?></td>
                        <td><?= htmlspecialchars($row['availability']); ?></td>
                        <td>
                            <button class="button-edit" onclick="populateForm(<?= htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="doctor_id" value="<?= htmlspecialchars($row['doctor_id']); ?>">
                                <button class="button-delete" type="submit" name="delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No doctors found</td>
                </tr>
            <?php endif; ?>
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