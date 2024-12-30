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
        alert('You must be logged in to leave a review!');
        window.location.href = 'log_in.php';
        </script>";
    exit();
}
$user_id = $_SESSION['user_id'];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $rating = intval($_POST['rating']);
    $review_text = htmlspecialchars($_POST['feedback']);
    if (!empty($full_name) && filter_var($email, FILTER_VALIDATE_EMAIL) && $rating > 0 && $rating <= 5 && !empty($review_text)) {
        $stmt = $conn->prepare("INSERT INTO review (user_id, full_name, rating, review_text) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $user_id, $full_name, $rating, $review_text);
        if ($stmt->execute()) {
            echo "<script>alert('Thank you for your feedback!');</script>";
        } else {
            echo "<script>alert('Error: Could not save your feedback.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Please fill out the form correctly.');</script>";
    }
}
$top_reviews_query = "
    SELECT full_name, rating, review_text 
    FROM review
    ORDER BY rating DESC, review_id DESC
    LIMIT 10";
$top_reviews = $conn->query($top_reviews_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DocTrack</title>
  <link rel="stylesheet" href="styles_review.css">
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
                <li><a href="review.php" class="active">Reviews/Feedback</a></li>
            </ul>
        </nav>
        <div class="Log_In">
        <?php
            if (isset($_SESSION['user_id'])) {
                echo '<a href="log_out.php"><button><strong>Log Out</strong></button></a>';
            } else {
                echo '<a href="log_in.php"><button><strong>Log In</strong></button></a>';
            }
            ?>
        </div>
    </header>
    <marquee><strong>Welcome to DocTrack! Your trusted platform for finding top doctors, booking appointments, and getting quick medicine recommendations. 
        Stay healthy, stay safe!</strong></marquee>
    <main>
        <h1><u>Feedback</u></h1>
        <section class="form">
            <div class="container">
                <h1>Feedback & Reviews</h1>
                <form action="#" method="POST" class="feedback-form">
                    <label for="name">Name:</label>
                    <input type="text" id="name" placeholder="Enter your full name" name="name" required>
                    <label for="email">Email:</label>
                    <input type="email" id="email" placeholder="Enter your email" name="email" required>
                    <label for="rating">Rating:</label>
                    <div class="rating">
                        <input type="radio" id="star5" name="rating" value="5">
                        <label for="star5" title="5 stars">★</label>
                        <input type="radio" id="star4" name="rating" value="4">
                        <label for="star4" title="4 stars">★</label>
                        <input type="radio" id="star3" name="rating" value="3">
                        <label for="star3" title="3 stars">★</label>
                        <input type="radio" id="star2" name="rating" value="2">
                        <label for="star2" title="2 stars">★</label>
                        <input type="radio" id="star1" name="rating" value="1">
                        <label for="star1" title="1 star">★</label>
                    </div>
                    <label for="feedback">Feedback:</label>
                    <textarea id="feedback" placeholder="Write your review" name="feedback" rows="5" required></textarea>
                    <br>
                    <button type="submit"><strong>Submit Feedback</strong></button>
                </form>
            </div>
            <div class="right2">
                <h2>Why Your Opinion Matters to Us</h2>
                <p>
                    At DocTrack, your feedback serves as the heartbeat of our innovation. We understand that every experience you have with our platform is unique, 
                    and your insights guide us in creating a service that exceeds your expectations. This space is dedicated to hearing your thoughts, suggestions, 
                    and even your criticisms because every piece of feedback matters. When you share your review, you help us in three critical ways:
                    <ul>
                    <li><strong>Improvement</strong>: Constructive criticism is a gift. It helps us identify areas that need enhancement and ensures that future 
                    users enjoy a seamless experience.</li>
                    <li><strong>Inspiration</strong>: Your positive experiences motivate us to keep pushing boundaries and striving for excellence in the healthcare 
                    space.</li>
                    <li><strong>Community Building</strong>: Honest reviews foster trust and encourage others to join our growing community, ultimately making 
                    healthcare more accessible to everyone.</li>
                    </ul>
                    How does your feedback impact others? Reviews like yours help patients make informed decisions, ensuring they choose the right doctor or service 
                    that fits their needs. Similarly, our QuickMedi feature evolves based on your comments, offering better recommendations every time.
                    
                    We encourage you to be open and detailed when submitting your feedback. Did you find the platform user-friendly? Was your appointment scheduling 
                    effortless? How effective were the medicine suggestions? Share everything — from what you loved to what could be better.
                </p>
            </div>
        </section>
        <br><hr><br>
        <section>
        <h2>Top Reviews</h2>
        <div class="review-container">
        <?php if ($top_reviews->num_rows > 0): ?>
            <?php while ($row = $top_reviews->fetch_assoc()): ?>
                <div class="review-card">
                    <div class="review-header">
                        <h3><?= htmlspecialchars($row['full_name']) ?></h3>
                        <span class="rating"><?= str_repeat("★", intval($row['rating'])) ?></span>
                    </div>
                    <p class="review-text"><?= htmlspecialchars($row['review_text']) ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-reviews">No reviews yet. Be the first to leave a review!</p>
        <?php endif; ?>
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
                <a href="">Gmail</a>
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
<?php $conn->close(); ?>