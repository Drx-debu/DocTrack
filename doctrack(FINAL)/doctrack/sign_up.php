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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = strtolower(trim($_POST['email']));
    $username = trim($_POST['username']);
    $plain_password = trim($_POST['password']);
    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
    $check_sql = "SELECT * FROM user_info WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows > 0) 
    {
        echo "<script>
            alert('Error: The email is already in use. Please use another email.');
            window.history.back();
            </script>";
    } 
    else 
    {
        $sql = "INSERT INTO user_info (email, username, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $email, $username, $hashed_password);

        if ($stmt->execute()) 
        {
            echo "<script>
                alert('Sign Up Successful. You are now ready for login.');
                window.location.href = 'log_in.php';
                </script>";
            exit();
        } 
        else 
        {
            echo "<script>
                alert('Error: " . $stmt->error . "');
                </script>";
        }
        $stmt->close();
    }
    $check_stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocTrack</title>
    <link rel="stylesheet" href="style_sign_up.css">
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
                <li><a href="take_appointment.php">Take Appointment</a></li>
                <li><a href="quickmedi.php">QuickMedi</a></li>
                <li><a href="review.php">Reviews/Feedback</a></li>
            </ul>
        </nav>
        <div class="Log_In">
        <?php
            if (isset($_SESSION['user_id'])) {
                echo '<a href="log_out.php"><button><strong>Log Out</strong></button></a>';
            } else {
                echo '<a href="main.php"><button><strong>Back to Home page</strong></button></a>';
            }
            ?>
        </div>
    </header>
    <main class="sign-up-section">
        <div class="sign-up-info">
            <br><br><br><br>
            <h1>Join DocTrack Today!</h1>
            <p>Sign up to manage your health appointments, track symptoms, and receive reliable medical advice with ease.</p>
            <ul>
                <li>Book appointments with top doctors</li>
                <li>Access your medical history and prescriptions</li>
                <li>Get tailored medicine suggestions via QuickMedi</li>
                <li>Track your ongoing treatments and progress</li>
            </ul>
            <p>Don’t wait—create your account and take control of your health!</p>
        </div>
        <div class="sign-up-container">
            <h1>Create Your Account</h1>
            <form action="sign_up.php" method="POST" onsubmit="return validateForm();">
                <label for="username">Full Name</label>
                <input type="text" id="username" name="username" placeholder="Enter your full name" required>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm your password" required>
                <button type="submit"><b>Sign Up</b></button>
            </form>
            <p>Already have an account? <a href="log_in.php">Log In</a></p>
        </div>
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
                <a href="">Gmail</a> | <a href="">Instagram</a>
                <br><br>
                <a href="">Facebook</a> | <a href="">LinkedIn</a>
            </div>
        </div>
    </footer>
    <script src="sign_up.js"></script>
</body>
</html>