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
$table = 'patient';
$sql = "SELECT * FROM $table";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit'])) {
        $patient_id = $_POST['patient_id'];
        $full_name = htmlspecialchars($_POST['full_name']);
        $email = htmlspecialchars($_POST['email']);
        $phone = htmlspecialchars($_POST['phone']);
        $gender = htmlspecialchars($_POST['gender']);
        $dob = htmlspecialchars($_POST['dob']);
        $address = htmlspecialchars($_POST['address']);
        $medical_history = htmlspecialchars($_POST['medical_history']);
        $profile_image = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024;
            if (!in_array($_FILES['profile_image']['type'], $allowed_types)) {
                $_SESSION['error'] = "Invalid image type. Only JPG, PNG, and GIF are allowed.";
                header("Location: manage_patients.php");
                exit;
            }
            if ($_FILES['profile_image']['size'] > $max_size) {
                $_SESSION['error'] = "File size exceeds 2MB limit.";
                header("Location: manage_patients.php");
                exit;
            }
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $file_name = basename($_FILES['profile_image']['name']);
            $target_file = $upload_dir . uniqid() . '_' . $file_name;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $profile_image = $target_file;
            } else {
                $_SESSION['error'] = "Error uploading the profile image.";
                header("Location: manage_patients.php");
                exit;
            }
        } else {
            $query = "SELECT profile_image FROM $table WHERE patient_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $patient_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $profile_image = $result->fetch_assoc()['profile_image'];
        }
        $query = "UPDATE $table SET profile_image = ?, full_name = ?, email = ?, phone = ?, gender = ?, dob = ?, address = ?, medical_history = ? WHERE patient_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssssssssi', $profile_image, $full_name, $email, $phone, $gender, $dob, $address, $medical_history, $patient_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Patient updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating patient: " . $stmt->error;
        }
        header("Location: manage_patients.php");
        exit;
    }
    if (isset($_POST['delete'])) {
        $patient_id = $_POST['patient_id'];
        $query = "DELETE FROM $table WHERE patient_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $patient_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Patient deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting patient: " . $stmt->error;
        }
        header("Location: manage_patients.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Patients</title>
    <link rel="stylesheet" href="styles_manage_entries.css">
    <script>
        function previewImage(input) {
            const file = input.files[0];
            if (file) {
                document.getElementById('profilePreview').src = URL.createObjectURL(file);
            }
        }
        function populateForm(data) {
            document.getElementById('patientId').value = data.patient_id;
            document.getElementById('profilePreview').src = data.profile_image || '';
            document.getElementById('full_name').value = data.full_name;
            document.getElementById('email').value = data.email;
            document.getElementById('phone').value = data.phone;
            document.getElementById('gender').value = data.gender;
            document.getElementById('dob').value = data.dob;
            document.getElementById('address').value = data.address;
            document.getElementById('medical_history').value = data.medical_history;
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
            <li><a href="manage_patients.php" class="active">Manage Patients</a></li>
            <li><a href="manage_doctors.php">Manage Doctors</a></li>
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
    <h1>Manage Patients</h1>
    <h2>Edit Patient</h2>
    <form method="POST" class="form" enctype="multipart/form-data">
        <input type="hidden" name="patient_id" id="patientId">

        <label for="profile_image">Profile Image:</label>
        <img id="profilePreview" src="" alt="No Image" style="width: 100px; height: 100px; display: block; margin-bottom: 10px;">
        <input type="file" name="profile_image" id="profile_image" accept="image/*" onchange="previewImage(this)">

        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" id="full_name" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="phone">Phone:</label>
        <input type="tel" name="phone" id="phone" required pattern="[0-9]{10}">

        <label for="gender">Gender:</label>
        <select name="gender" id="gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label for="dob">Date of Birth:</label>
        <input type="date" name="dob" id="dob" required>

        <label for="address">Address:</label>
        <input type="text" name="address" id="address" required>

        <label for="medical_history">Medical History:</label>
        <textarea name="medical_history" id="medical_history" rows="4" required></textarea>

        <button type="submit" name="edit"><strong>Update Patient</strong></button>
    </form>
    <h2>Existing Patients</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Patient ID</th>
                <th>Profile Image</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>DOB</th>
                <th>Address</th>
                <th>Medical History</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['patient_id']); ?></td>
                    <td><img src="<?= htmlspecialchars($row['profile_image']); ?>" alt="Profile Image" style="width: 50px; height: 50px;"></td>
                    <td><?= htmlspecialchars($row['full_name']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td><?= htmlspecialchars($row['phone']); ?></td>
                    <td><?= htmlspecialchars($row['gender']); ?></td>
                    <td><?= htmlspecialchars($row['dob']); ?></td>
                    <td><?= htmlspecialchars($row['address']); ?></td>
                    <td><?= htmlspecialchars($row['medical_history']); ?></td>
                    <td>
                        <button class="button-edit" onclick="populateForm(<?= htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="patient_id" value="<?= htmlspecialchars($row['patient_id']); ?>">
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