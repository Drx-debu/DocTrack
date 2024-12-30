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
if (!isset($_SESSION['user_id'])) {
    header('Location: log_in.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$sql = "
SELECT 
    u.id AS user_id, 
    u.username AS user_name, 
    u.email AS user_email, 
    p.patient_id, 
    p.full_name AS patient_name, 
    p.email AS patient_email, 
    p.phone, 
    p.gender, 
    p.dob, 
    p.address, 
    p.medical_history, 
    p.profile_image
FROM 
    user_info u
LEFT JOIN 
    patient p 
ON 
    u.id = p.patient_id
WHERE 
    u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $medical_history = $_POST['medical_history'];
    $upload_dir = 'uploads/profile_images/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $profile_image = $patient['profile_image'];
    if (!empty($_FILES['profile_picture']['tmp_name'])) {
        $file_ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_types)) {
            $profile_image = $upload_dir . $user_id . '.' . $file_ext;
            if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_image)) {
                die('Failed to upload profile picture.');
            }
        } else {
            die('Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.');
        }
    }
    if ($patient['patient_id']) {
        $sql = "UPDATE patient 
                SET full_name = ?, email = ?, phone = ?, gender = ?, dob = ?, address = ?, medical_history = ?, profile_image = ? 
                WHERE patient_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssssi', $full_name, $patient['user_email'], $phone, $gender, $dob, $address, $medical_history, $profile_image, $user_id);
    } else {
        $sql = "INSERT INTO patient (patient_id, full_name, email, phone, gender, dob, address, medical_history, profile_image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('issssssss', $user_id, $full_name, $patient['user_email'], $phone, $gender, $dob, $address, $medical_history, $profile_image);
    }
    $stmt->execute();
    $stmt->close();
    echo "<script>
    alert('Profile Updated Successfully');
    window.location.href = 'my_profile.php';
    </script>";
    exit();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles_edit_profile.css">
</head>
<body>
<header>
    <div class="header-logo">
        <img src="logo2.png" alt="DocTrack Logo">
    </div>
    <nav>
        <ul>
            <li><a href="main.php">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="my_profile.php">My Profile</a></li>
            <li><a href="take_appointment.php">Take Appointment</a></li>
            <li><a href="quickmedi.php" class="active">QuickMedi</a></li>
            <li><a href="review.php">Reviews/Feedback</a></li>
        </ul>
    </nav>
    <div class="Log_In">
        <a href="log_out.php">
            <button><strong>Log Out</strong></button>
        </a>
    </div>
</header>
<marquee><strong>Welcome to DocTrack! Your trusted platform for finding top doctors, booking appointments, and getting quick medicine recommendations. 
    Stay healthy, stay safe!</strong></marquee>
<main>
    <h1>Edit Your Profile</h1>
    <form method="POST" enctype="multipart/form-data" class="edit-profile-form">
        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($patient['full_name'] ?? '') ?>" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($patient['patient_email'] ?? $patient['user_email'] ?? '') ?>" readonly>
        <label for="phone">Phone:</label>
        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($patient['phone'] ?? '') ?>" required>
        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="">Select</option>
            <option value="Male" <?= (isset($patient['gender']) && $patient['gender'] === 'Male') ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= (isset($patient['gender']) && $patient['gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
        </select>
        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($patient['dob'] ?? '') ?>" required>
        <label for="address">Address:</label>
        <textarea id="address" name="address" required><?= htmlspecialchars($patient['address'] ?? '') ?></textarea>
        <label for="medical_history">Medical History:</label>
        <textarea id="medical_history" name="medical_history"><?= htmlspecialchars($patient['medical_history'] ?? '') ?></textarea>
        <label for="profile_picture">Profile Picture:</label>
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
        <?php if (!empty($patient['profile_image'])): ?>
            <p>Current Picture:</p>
            <img src="<?= htmlspecialchars($patient['profile_image']) ?>" alt="Profile Picture" class="current-profile-picture">
        <?php endif; ?>
        <br>
        <div class="checkbox">
            <input type="checkbox" required>
            <label>I agree that i have entered correct information</label>
        </div>
        <button type="submit">Save Changes</button>
    </form>
</main>
<footer>
    <div class="footer-content">
        <div class="footer-left">
            <img src="logo2.png" alt="DocTrack">
        </div>
        <div>
            <p>||  © Since 2024  ||</p>
        </div>
        <div class="footer-right">
            <a href="#">Gmail</a> |
            <a href="#">Instagram</a><br><br>
            <a href="#">Facebook</a> |
            <a href="#">LinkedIn</a>
        </div>
    </div>
</footer>
</body>
</html>