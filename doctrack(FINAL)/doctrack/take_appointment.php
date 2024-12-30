<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "doctrack";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$doctors = [];
$success_message = "";
$error_message = "";
if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('You must be logged in to access this page!');
        window.location.href = 'log_in.php';
    </script>";
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['search_doctors'])) {
        $specialization = isset($_POST['specialization']) ? trim($_POST['specialization']) : '';
        $custom_symptom = isset($_POST['symptom']) ? trim($_POST['symptom']) : '';
        if (!empty($specialization) && $specialization !== 'Others') {
            $sql = "SELECT * FROM doctors WHERE specialization LIKE ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $search_term = '%' . $specialization . '%';
                $stmt->bind_param("s", $search_term);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $doctors[] = $row;
                    }
                } else {
                    $error_message = "No doctors found for the selected specialization.";
                }
                $stmt->close();
            } else {
                $error_message = "Failed to prepare the query.";
            }
        } elseif ($specialization === 'Others' && !empty($custom_symptom)) {
            $sql = "SELECT * FROM doctors WHERE specialization LIKE ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $search_term = '%' . $custom_symptom . '%';
                $stmt->bind_param("s", $search_term);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $doctors[] = $row;
                    }
                } else {
                    $error_message = "No doctors found for the entered symptom.";
                }
                $stmt->close();
            } else {
                $error_message = "Failed to prepare the query.";
            }
        } else {
            $error_message = "Please select a specialization or enter a symptom.";
        }
    }
    if (isset($_POST['book_appointment'])) {
        $user_id = $_SESSION['user_id'];
        $doctor_id = $_POST['doctor_id'] ?? '';
        $patient_name = $_POST['patient_name'] ?? '';
        $contact_info = $_POST['contact_info'] ?? '';
        $sex = $_POST['sex'] ?? '';
        $age = intval($_POST['age'] ?? 0);
        $appointment_date = $_POST['appointment_date'] ?? '';
        $appointment_time = $_POST['appointment_time'] ?? '';
        $reason = $_POST['reason'] ?? '';
        if ($doctor_id && $patient_name && $contact_info && $sex && $age && $appointment_date && $appointment_time && $reason) {
            if (strtotime($appointment_date) < strtotime('today')) {
                $error_message = "The appointment date cannot be in the past.";
            } else {
                $sql_check = "SELECT * FROM appointments WHERE user_id = ? AND doctor_id = ? AND appointment_date = ? AND appointment_time = ?";
                $stmt_check = $conn->prepare($sql_check);
                $stmt_check->bind_param("iiss", $user_id, $doctor_id, $appointment_date, $appointment_time);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();
                if ($result_check->num_rows > 0) {
                    $error_message = "You have already booked an appointment with this doctor for the selected date and time.";
                } else {
                    $sql = "INSERT INTO appointments (user_id, patient_name, age, sex, contact_info, doctor_id, appointment_date, appointment_time, reason) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param(
                            "isississs",
                            $user_id,
                            $patient_name,
                            $age,
                            $sex,
                            $contact_info,
                            $doctor_id,
                            $appointment_date,
                            $appointment_time,
                            $reason
                        );
                        if ($stmt->execute()) {
                            $success_message = "Appointment successfully booked!";
                        } else {
                            $error_message = "Failed to book the appointment.";
                        }
                        $stmt->close();
                    } else {
                        $error_message = "Failed to prepare the appointment query.";
                    }
                }
                $stmt_check->close();
            }
        } else {
            $error_message = "Please fill out all fields in the appointment form.";
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocTrack</title>
    <link rel="stylesheet" href="style_appointment.css">
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
            <li><a href="my_profile.php">My Profile</a></li>
            <li><a href="take_appointment.php" class="active">Take Appointment</a></li>
            <li><a href="quickmedi.php">QuickMedi</a></li>
            <li><a href="review.php">Reviews/Feedback</a></li>
        </ul>
    </nav>
    <div class="Log_In">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="log_out.php"><button><strong>Log Out</strong></button></a>
        <?php else: ?>
            <a href="log_in.php"><button><strong>Log In</strong></button></a>
        <?php endif; ?>
    </div>
</header>
<marquee><strong>Welcome to DocTrack! Your trusted platform for finding top doctors, booking appointments, and getting quick medicine recommendations. 
    Stay healthy, stay safe!</strong></marquee>
<main>
    <h1>Book an Appointment</h1>
    <br>
    <section class="section1">
        <div class="left1">
            <p><b>
                At DocTrack, we prioritize your health and time, making healthcare appointments both convenient and efficient. With just a few simple steps, you 
                can schedule a visit to the doctor without any stress. The process begins with completing a user-friendly form, where you’ll enter your health concerns, 
                preferred appointment date, and location. Based on this information, DocTrack matches you with the best-suited healthcare professional, taking into 
                account their specialization, expertise, and availability.
                <br><br>
                Whether you're seeking a routine check-up or specialized treatment, DocTrack ensures that booking an appointment is quick, simple, and tailored to 
                your specific needs. Our platform streamlines the entire process, so you don't have to waste time searching for a doctor or dealing with complicated 
                booking systems. By offering you the convenience of booking directly online, we remove the usual hassle and help you save time.            
            </b></p>
        </div>
        <div class="right1">
            <img src="dr.jpg">
        </div>
    </section>
    <br><hr><br>
    <form action="take_appointment.php" method="POST">
        <h2>Find Doctors</h2>
    <label for="specialization">Select Doctor by Specialization (select 'Others', if not found in list):</label>
    <select id="specialization" name="specialization">
        <option value="" disabled selected>Select a category</option>
        <option value="Cardiologist">Cardiologist</option>
        <option value="Dentist">Dentist</option>
        <option value="ENT Specialist">ENT Specialist</option>
        <option value="Pediatrician">Pediatrician</option>
        <option value="Gynecologist">Gynecologist</option>
        <option value="Psychiatrist">Psychiatrist</option>
        <option value="Oncologist">Oncologist</option>
        <option value="Others">Others</option>
    </select>
    <br><br><br><br>
    <label>Search Doctor by Specialization (IF selected 'Others' in the list):</label>
    <textarea name="symptom" rows="4" placeholder="Enter the symptom if you choose 'Others'..."></textarea>
    <button type="submit" name="search_doctors">Search Doctors</button>
    </form>
    <?php if ($error_message): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message); ?></p>
    <?php elseif ($success_message): ?>
        <p style="color: green;"><?= htmlspecialchars($success_message); ?></p>
    <?php endif; ?>
    <?php if (!empty($doctors)): ?>
        <h2>Doctors Found</h2>
        <section>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Specialization</th>
                    <th>Qualifications</th>
                    <th>Email</th>
                    <th>Chamber Address</th>
                    <th>Fees</th>
                    <th>Availability</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($doctors as $doctor): ?>
                    <tr>
                        <td><?= htmlspecialchars($doctor['name']); ?></td>
                        <td><?= htmlspecialchars($doctor['specialization']); ?></td>
                        <td><?= htmlspecialchars($doctor['qualifications']); ?></td>
                        <td><?= htmlspecialchars($doctor['email']); ?></td>
                        <td><?= htmlspecialchars($doctor['chamber_address']); ?></td>
                        <td><?= htmlspecialchars($doctor['fees']); ?></td>
                        <td><?= htmlspecialchars($doctor['availability']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </section>
        <br><hr><br>
        <div>
        <form action="take_appointment.php" method="POST">
            <h2>Book an Appointment</h2>
            <label for="doctor_id">Doctor:</label>
            <select name="doctor_id" required>
                <option value="" disabled selected>Select a doctor</option>
                <?php foreach ($doctors as $doctor): ?>
                    <option value="<?= $doctor['doctor_id']; ?>"><?= htmlspecialchars($doctor['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <label for="patient_name">Your Name:</label>
            <input type="text" name="patient_name" required>
            <label for="contact_info">Contact Information:</label>
            <input type="text" name="contact_info" required>
            <label for="sex">Gender:</label>
            <select name="sex" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <label for="age">Age:</label>
            <input type="number" name="age" required>
            <label for="appointment_date">Date:</label>
            <input type="date" name="appointment_date" required>
            <label for="appointment_time">Time:</label>
            <input type="time" name="appointment_time" required>
            <label for="reason">Reason:</label>
            <textarea name="reason" required></textarea>
            <button type="submit" name="book_appointment">Book Appointment</button>
        </form>
    <?php endif; ?>
    </div>
    <br><hr><br>
    <section class="section3">
        <div class="div3">
        <h1>Precautions</h1>
                <p>
                    <b>1. Accurate Information:</b> Always provide correct and complete information regarding your health concerns, allergies, or medications. This 
                        ensures the doctor can provide the best possible care.<br><br>
                    <b>2. Health Emergencies:</b> For urgent or life-threatening health issues, please seek immediate medical help at an emergency department or call 
                        emergency services instead of waiting for an online consultation.<br><br>
                    <b>3. Privacy and Security:</b> Your personal and health data is valuable. Ensure you're on a secure network when filling in your details.<br><br>
                    <b>4. Follow-Up:</b> After your appointment, follow any advice or prescriptions carefully. Book a follow-up appointment if needed.<br><br>
                    <b>5. Emergency Situations:</b> In case of severe symptoms or emergencies, avoid using QuickMedi and seek immediate medical help.<br><br>
                    <b>6. Consultation Limitations:</b> While DocTrack helps connect you to doctors, remember that online consultations or recommendations cannot fully 
                        replace in-person medical evaluations for serious or complex health conditions. Always seek in-person care for critical issues, diagnostic 
                        testing, or if you experience any symptoms worsening after consultation.<br><br>
                </p>
        </div>
    </section>
    </main>
    <footer>
        <div class="footer-content">
            <div class="footer-left">
                <img src="logo2.png">
            </div>
            <div>
                <p>|| © Since 2024 ||</p>
            </div>
            <div class="footer-right">
                <a href="" >Gmail</a>
                |
                <a href="">Instagram</a>
                <br><br>
                <a href="">Facebook</a>
                |
                <a href="">LinkedIn</a>
            </div>   
        </div>
    </footer>
</body>
</html>