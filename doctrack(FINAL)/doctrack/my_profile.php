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
    echo "<script>
    alert('You must be logged in to access this page!');
    window.location.href = 'log_in.php';
    </script>";
    exit();
}
$user_id = $_SESSION['user_id'];
$sql = "SELECT u.id, u.username, u.email, p.phone, p.address, p.gender AS sex, p.dob, p.medical_history, p.profile_image
        FROM user_info u
        LEFT JOIN patient p ON u.id = p.patient_id
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_info = $result->fetch_assoc();
$stmt->close();
if (!$user_info) {
    header('Location: log_out.php');
    exit();
}
$sql_appointments = "SELECT COUNT(*) AS total_appointments FROM appointments WHERE user_id = ?";
$stmt = $conn->prepare($sql_appointments);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($total_appointments);
$stmt->fetch();
$stmt->close();
$sql_reviews = "SELECT COUNT(*) AS total_reviews FROM review WHERE user_id = ?";
$stmt = $conn->prepare($sql_reviews);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($total_reviews);
$stmt->fetch();
$stmt->close();
$phone = $user_info['phone'] ?? "Not provided";
$address = $user_info['address'] ?? "Not provided";
$sex = $user_info['sex'] ?? "Not provided";
$dob = $user_info['dob'] ?? "Not provided";
$medical_history = $user_info['medical_history'] ?? "Not provided";
$profile_picture = $user_info['profile_image'] ?? "default.jpg";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="style_my_profile.css">
    <link rel="icon" type="image/x-icon" href="icon.png">
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
            <li><a href="my_profile.php" class="active">My Profile</a></li>
            <li><a href="take_appointment.php">Take Appointment</a></li>
            <li><a href="quickmedi.php">QuickMedi</a></li>
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
    <h1>Dashboard</h1>
    <section>
        <div class="profile-section">
        <div class="profile-header">
        <img src="<?= htmlspecialchars($profile_picture) ?>" alt="User Avatar" class="profile-avatar">
            <h1>Welcome, <?= htmlspecialchars($user_info['username']) ?></h1>
            <p>Email: <strong><?= htmlspecialchars($user_info['email']) ?></strong></p>
        </div>
        <div class="profile-info">
            <h2>Your Details</h2>
            <table>
                <tr>
                    <th>User Id:</th>
                    <td><?= htmlspecialchars($user_info['id']) ?></td>
                </tr>
                <tr>
                    <th>Full Name:</th>
                    <td><?= htmlspecialchars($user_info['username']) ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><?= htmlspecialchars($user_info['email']) ?></td>
                </tr>
                <tr>
                    <th>Phone:</th>
                    <td><?= htmlspecialchars($phone) ?></td>
                </tr>
                <tr>
                    <th>Sex:</th>
                    <td><?= htmlspecialchars($sex) ?></td>
                </tr>
                <tr>
                    <th>Date of Birth:</th>
                    <td><?= htmlspecialchars($dob) ?></td>
                </tr>
                <tr>
                    <th>Address:</th>
                    <td><?= htmlspecialchars($address) ?></td>
                </tr>
                <tr>
                    <th>Medical History (if any):</th>
                    <td><?= htmlspecialchars($medical_history) ?></td>
                </tr>
            </table>
            <a href="edit_profile.php" class="btn-edit-profile">Edit Profile</a>
        </div>
    </div>
    </section>
    <br><hr><br>
    <section>
        <div class="activity-section">
        <h2>Your Activities</h2>
        <div class="activity-box">
            <div class="activity-item">
                <h3>Appointments</h3>
                <p>Total Appointments: <?= htmlspecialchars($total_appointments) ?></p>
                <a href="appointments.php" class="btn-view-details">View Details</a>
            </div>
            <div class="activity-item">
                <h3>Reviews</h3>
                <p>Total Reviews Given: <?= htmlspecialchars($total_reviews) ?></p>
                <a href="review.php" class="btn-view-details">View Reviews</a>
            </div>
            <div class="activity-item">
                <h3>QuickMedi</h3>
                <p>Need medicine recommendation?</p>
                <a href="quickmedi.php" class="btn-view-details">Go to QuickMedi</a>
            </div>
        </div>
    </div>
    </section>
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
            <a href="">Gmail</a> | <a href="">Instagram</a><br><br>
            <a href="">Facebook</a> | <a href="">LinkedIn</a>
        </div>
    </div>
</footer>
</body>
</html>