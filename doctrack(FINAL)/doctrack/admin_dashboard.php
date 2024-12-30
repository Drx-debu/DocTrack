<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo "<script>
        alert('Unauthorized access! Please log in as an admin.');
        window.location.href = 'admin_login.php';
    </script>";
    exit();
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "doctrack";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$admin_id = $_SESSION['admin_id'];
$sql = "SELECT * FROM admin_info WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$sql_users = "SELECT id, username, email FROM user_info"; 
$result_users = $conn->query($sql_users);
$users = $result_users->fetch_all(MYSQLI_ASSOC);
$sql_doctors = "SELECT doctor_id, name, specialization, qualifications, fees, chamber_address FROM doctors"; 
$result_doctors = $conn->query($sql_doctors);
$doctors = $result_doctors->fetch_all(MYSQLI_ASSOC);
$sql_medicine = "SELECT medicine_name, category, symptoms, precautions, side_effects, usage_instruction FROM medicine"; 
$result_medicine = $conn->query($sql_medicine);
$medicine = $result_medicine->fetch_all(MYSQLI_ASSOC);
$sql_patient = "SELECT patient_id, full_name, email, phone, gender, dob, medical_history, address FROM patient"; 
$result_patient = $conn->query($sql_patient);
$patient = $result_patient->fetch_all(MYSQLI_ASSOC);
$sql_appointments = "SELECT appointment_id, user_id, doctor_id, patient_name, age, sex, appointment_date, appointment_time, reason FROM appointments"; 
$result_appointments = $conn->query($sql_appointments);
$appointments = $result_appointments->fetch_all(MYSQLI_ASSOC);
$sql_review = "SELECT user_id, full_name, rating, review_text FROM review"; 
$result_review = $conn->query($sql_review);
$review = $result_review->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocTrack - Admin</title>
    <link rel="stylesheet" href="style_admin_dashboard.css">
    <link rel="icon" type="image/x-icon" href="icon.png">
</head>
<body>
    <header>
        <div class="header-logo">
            <img src="logo2.png" alt="DocTrack Logo">
        </div>
        <nav>
            <ul>
                <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
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
                echo '<a href="main.php"><button><strong>Back to Home page</strong></button></a>';
            }
            ?>
    </header>
    <main>
    <h1>Welcome, <?php echo htmlspecialchars($admin['username']); ?></h1>
    <section class="dashboard-overview">
        <div class="overview-item">
            <h2>User Details</h2>
            <table class="table" id="userTable">
                <thead>
                    <tr>
                        <th>User Id</th>
                        <th>User Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $index => $user): ?>
                        <tr class="<?= $index >= 10 ? 'hidden-row' : ''; ?>">
                            <td><?= htmlspecialchars($user['id']); ?></td>
                            <td><?= htmlspecialchars($user['username']); ?></td>
                            <td><?= htmlspecialchars($user['email']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button id="showMoreUserTableBtn" onclick="showMoreRows('userTable')">Show More</button>
        </div>
        <br><hr><br>
        <div class="overview-item">
            <h2>Doctor Details</h2>
            <table class="table" id="doctorTable">
                <thead>
                    <tr>
                        <th>Doctor Id</th>
                        <th>Doctor Name</th>
                        <th>Specialization</th>
                        <th>Qualification</th>
                        <th>Fees</th>
                        <th>Chamber Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($doctors as $index => $doc): ?>
                        <tr class="<?= $index >= 10 ? 'hidden-row' : ''; ?>">
                            <td><?= htmlspecialchars($doc['doctor_id']); ?></td>
                            <td><?= htmlspecialchars($doc['name']); ?></td>
                            <td><?= htmlspecialchars($doc['specialization']); ?></td>
                            <td><?= htmlspecialchars($doc['qualifications']); ?></td>
                            <td><?= htmlspecialchars($doc['fees']); ?></td>
                            <td><?= htmlspecialchars($doc['chamber_address']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button id="showMoreDoctorTableBtn" onclick="showMoreRows('doctorTable')">Show More</button>
        </div>
        <br><hr><br>
        <div class="overview-item">
            <h2>Medicine Details</h2>
            <table class="table" id="medicineTable">
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Category</th>
                        <th>Symptoms</th>
                        <th>Precautions</th>
                        <th>Side Effects</th>
                        <th>Usage Instruction</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medicine as $index => $med): ?>
                        <tr class="<?= $index >= 10 ? 'hidden-row' : ''; ?>">
                            <td><?= htmlspecialchars($med['medicine_name']); ?></td>
                            <td><?= htmlspecialchars($med['category']); ?></td>
                            <td><?= htmlspecialchars($med['symptoms']); ?></td>
                            <td><?= htmlspecialchars($med['precautions']); ?></td>
                            <td><?= htmlspecialchars($med['side_effects']); ?></td>
                            <td><?= htmlspecialchars($med['usage_instruction']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button id="showMoreMedicineTableBtn" onclick="showMoreRows('medicineTable')">Show More</button>
        </div>
        <br><hr><br>
        <div class="overview-item">
            <h2>Patient Details</h2>
            <table class="table" id="patientTable">
                <thead>
                    <tr>
                        <th>Patient Id</th>
                        <th>Patient Name</th>
                        <th>Sex</th>
                        <th>DOB</th>
                        <th>Medical History</th>
                        <th>Address</th>
                        <th>Email</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patient as $index => $pat): ?>
                        <tr class="<?= $index >= 10 ? 'hidden-row' : ''; ?>">
                            <td><?= htmlspecialchars($pat['patient_id']); ?></td>
                            <td><?= htmlspecialchars($pat['full_name']); ?></td>
                            <td><?= htmlspecialchars($pat['gender']); ?></td>
                            <td><?= htmlspecialchars($pat['dob']); ?></td>
                            <td><?= htmlspecialchars($pat['medical_history']); ?></td>
                            <td><?= htmlspecialchars($pat['address']); ?></td>
                            <td><?= htmlspecialchars($pat['email']); ?></td>
                            <td><?= htmlspecialchars($pat['phone']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button id="showMorePatientTableBtn" onclick="showMoreRows('patientTable')">Show More</button>
        </div>
        <br><hr><br>
        <div class="overview-item">
            <h2>Appointment Details</h2>
            <table class="table" id="appointmentTable">
                <thead>
                    <tr>
                        <th>Appointment Id</th>
                        <th>User Id</th>
                        <th>Doctor Id</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Sex</th>
                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $index => $apt): ?>
                        <tr class="<?= $index >= 10 ? 'hidden-row' : ''; ?>">
                            <td><?= htmlspecialchars($apt['appointment_id']); ?></td>
                            <td><?= htmlspecialchars($apt['user_id']); ?></td>
                            <td><?= htmlspecialchars($apt['doctor_id']); ?></td>
                            <td><?= htmlspecialchars($apt['patient_name']); ?></td>
                            <td><?= htmlspecialchars($apt['age']); ?></td>
                            <td><?= htmlspecialchars($apt['sex']); ?></td>
                            <td><?= htmlspecialchars($apt['appointment_date']); ?></td>
                            <td><?= htmlspecialchars($apt['appointment_time']); ?></td>
                            <td><?= htmlspecialchars($apt['reason']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button id="showMoreAppointmentTableBtn" onclick="showMoreRows('appointmentTable')">Show More</button>
        </div>
        <br><hr><br>
        <div class="overview-item">
            <h2>Reviews</h2>
            <table class="table" id="reviewTable">
                <thead>
                    <tr>
                        <th>User Id</th>
                        <th>Name</th>
                        <th>Rating</th>
                        <th>Review</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($review as $index => $rev): ?>
                        <tr class="<?= $index >= 10 ? 'hidden-row' : ''; ?>">
                            <td><?= htmlspecialchars($rev['user_id']); ?></td>
                            <td><?= htmlspecialchars($rev['full_name']); ?></td>
                            <td><?= htmlspecialchars($rev['rating']); ?></td>
                            <td><?= htmlspecialchars($rev['review_text']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button id="showMoreReviewTableBtn" onclick="showMoreRows('reviewTable')">Show More</button>
        </div>
    </section>
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
    <script src="admin_dashboard.js"></script>
</body>
</html>