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
$table = 'appointments';
$sql = "SELECT * FROM $table";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit'])) {
        $appointment_id = $_POST['appointment_id'];
        $patient_name = htmlspecialchars($_POST['patient_name']);
        $age = (int)htmlspecialchars($_POST['age']);
        $sex = htmlspecialchars($_POST['sex']);
        $contact_info = htmlspecialchars($_POST['contact_info']);
        $appointment_date = htmlspecialchars($_POST['appointment_date']);
        $appointment_time = htmlspecialchars($_POST['appointment_time']);
        $query = "UPDATE $table SET patient_name=?, age=?, sex=?, contact_info=?, appointment_date=?, appointment_time=? WHERE appointment_id=?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            $_SESSION['error'] = "Error preparing query: " . $conn->error;
            header("Location: manage_appointments.php");
            exit;
        }
        $stmt->bind_param('sissssi', $patient_name, $age, $sex, $contact_info, $appointment_date, $appointment_time, $appointment_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Appointment updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating appointment: " . $stmt->error;
        }
        header("Location: manage_appointments.php");
        exit;
    }
    if (isset($_POST['delete'])) {
        $appointment_id = $_POST['appointment_id'];
        $query = "DELETE FROM $table WHERE appointment_id=?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            $_SESSION['error'] = "Error preparing query: " . $conn->error;
            header("Location: manage_appointments.php");
            exit;
        }
        $stmt->bind_param('i', $appointment_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Appointment deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting appointment: " . $stmt->error;
        }
        header("Location: manage_appointments.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Apointments</title>
    <link rel="stylesheet" href="styles_manage_entries.css">
    <script>
        function populateForm(data) {
            document.getElementById('appointmentId').value = data.appointment_id;
            document.getElementById('patient_name').value = data.patient_name;
            document.getElementById('age').value = data.age;
            document.getElementById('sex').value = data.sex;
            document.getElementById('contact_info').value = data.contact_info;
            document.getElementById('appointment_date').value = data.appointment_date;
            document.getElementById('appointment_time').value = data.appointment_time;
        }
        window.onload = function () {
            <?php if (isset($_SESSION['success'])): ?>
                alert("<?= $_SESSION['success']; ?>");
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                alert("<?= $_SESSION['error']; ?>");
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        };
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
            <li><a href="manage_doctors.php">Manage Doctors</a></li>
            <li><a href="manage_appointments.php" class="active">Manage Appointments</a></li>
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
    <h1>Manage Appointments</h1>

    <h2>Edit Appointment</h2>
    <form method="POST" class="form">
    <input type="hidden" name="appointment_id" id="appointmentId">

    <label for="patient_name">Patient Name:</label>
    <input type="text" name="patient_name" id="patient_name" required pattern="[a-zA-Z\s]+" title="Only letters and spaces are allowed">

    <label for="age">Age:</label>
    <input type="number" name="age" id="age" min="0" required>

    <label for="sex">Sex:</label>
    <select name="sex" id="sex" required>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
    </select>

    <label for="contact_info">Contact Info:</label>
    <input type="text" name="contact_info" id="contact_info" required pattern="\d{10}" title="Enter a valid 10-digit phone number">

    <label for="appointment_date">Appointment Date:</label>
    <input type="date" name="appointment_date" id="appointment_date" required>

    <label for="appointment_time">Appointment Time:</label>
    <input type="time" name="appointment_time" id="appointment_time" required>

    <button type="submit" name="edit"><strong>Update Appointment</strong></button>
</form>
    <h2>Existing Appointments</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Appointment ID</th>
                <th>User ID</th>
                <th>Doctor ID</th>
                <th>Patient Name</th>
                <th>Age</th>
                <th>Sex</th>
                <th>Contact Info</th>
                <th>Date</th>
                <th>Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['appointment_id']); ?></td>
                    <td><?= htmlspecialchars($row['user_id']); ?></td>
                    <td><?= htmlspecialchars($row['doctor_id']); ?></td>
                    <td><?= htmlspecialchars($row['patient_name']); ?></td>
                    <td><?= htmlspecialchars($row['age']); ?></td>
                    <td><?= htmlspecialchars($row['sex']); ?></td>
                    <td><?= htmlspecialchars($row['contact_info']); ?></td>
                    <td><?= htmlspecialchars($row['appointment_date']); ?></td>
                    <td><?= htmlspecialchars($row['appointment_time']); ?></td>
                    <td>
                        <button class="button-edit" onclick="populateForm(<?= htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this appointment?');">
                            <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($row['appointment_id']); ?>">
                            <button class="button-delete" type="submit" name="delete">Delete</button>
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