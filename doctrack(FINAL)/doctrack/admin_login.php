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
    $input_email = strtolower(trim($_POST['email']));
    $input_password = trim($_POST['password']);
    if (!filter_var($input_email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
        alert('Invalid email format. Please try again.');
        </script>";
        exit();
    }
    $sql = "SELECT admin_id, email, password FROM admin_info WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $input_email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($input_password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $user['admin_id'];
            echo "<script>
                alert('Login Successful');
                window.location.href = 'admin_dashboard.php';
                </script>";
            exit();
        } else {
            echo "<script>
            alert('Invalid email or password. Please try again.');
            </script>";
        }
    } else {
        echo "<script>
        alert('Invalid email or password. Please try again.');
        </script>";
    }
    $stmt->close();
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DocTrack</title>
    <link rel="stylesheet" href="style_admin_login.css">
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
    <main class="login-section">
        <div class="login-container">
            <h1>Administrative Login</h1>
            <br>
            <section>
            <form action="admin_login.php" method="post" class="form">
            <h2>Login to DocTrack</h2>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                <br><br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <br><br><br><br>
                <button type="submit"><b>Log In</b></button>
            </form>
            </section>
        </div>
    </main>
    <footer>
        <div class="footer-content">
            <div class="footer-left">
                <div class="header-logo">
                    <img src="logo2.png" alt="DocTrack">
                </div>
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
</body>
</html>