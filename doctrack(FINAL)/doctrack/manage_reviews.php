<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "doctrack";
define('REVIEW_TABLE', 'review');
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM " . REVIEW_TABLE;
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit'])) {
        $review_id = $_POST['review_id'];
        $user_id = htmlspecialchars($_POST['user_id']);
        $full_name = htmlspecialchars($_POST['full_name']);
        $rating = (int)htmlspecialchars($_POST['rating']);
        $review_text = htmlspecialchars($_POST['review_text']);
        if ($rating < 1 || $rating > 5) {
            $_SESSION['error'] = "Rating must be between 1 and 5.";
            header("Location: manage_reviews.php");
            exit;
        }
        $query = "UPDATE " . REVIEW_TABLE . " SET user_id=?, full_name=?, rating=?, review_text=? WHERE review_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('isssi', $user_id, $full_name, $rating, $review_text, $review_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Review updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating review: " . $stmt->error;
        }
        header("Location: manage_reviews.php");
        exit;
    }
    if (isset($_POST['delete'])) {
        $review_id = $_POST['review_id'];
        $query = "DELETE FROM " . REVIEW_TABLE . " WHERE review_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $review_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Review deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting review: " . $stmt->error;
        }
        header("Location: manage_reviews.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews</title>
    <link rel="stylesheet" href="styles_manage_entries.css">
    <script>
        function populateForm(data) {
            document.getElementById('reviewId').value = data.review_id;
            document.getElementById('user_id').value = data.user_id;
            document.getElementById('full_name').value = data.full_name;
            document.getElementById('rating').value = data.rating;
            document.getElementById('review_text').value = data.review_text;
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
            <li><a href="manage_doctors.php">Manage Doctors</a></li>
            <li><a href="manage_appointments.php">Manage Appointments</a></li>
            <li><a href="manage_reviews.php" class="active">Manage Reviews</a></li>
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
    <h1>Manage Reviews</h1>
    <h2>Edit Reviews</h2>
<form method="POST" class="form">
    <input type="hidden" name="review_id" id="reviewId">

    <label for="user_id">User ID:</label>
    <input type="number" name="user_id" id="user_id" value="" readonly>

    <label for="full_name">Full Name:</label>
    <input type="text" name="full_name" id="full_name" required>

    <label for="rating">rating:</label>
    <input type="number" name="rating" id="rating" required>

    <label for="review_text">Review Text:</label>
    <input type="text" name="review_text" id="review_text" required>

    <button type="submit" name="edit"><strong>Update Appointment</strong></button>
</form>
<br><hr><br>
<h2>Existing Reviews</h2>
<table class="table">
    <thead>
        <tr>
            <th>Review ID</th>
            <th>User ID</th>
            <th>Full Name</th>
            <th>Rating</th>
            <th>Review Text</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['review_id']); ?></td>
                <td><?= htmlspecialchars($row['user_id']); ?></td>
                <td><?= htmlspecialchars($row['full_name']); ?></td>
                <td><?= htmlspecialchars($row['rating']); ?></td>
                <td><?= htmlspecialchars($row['review_text']); ?></td>
                <td>
                    <button class="button-edit" onclick="populateForm(<?= htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="review_id" value="<?= htmlspecialchars($row['review_id']); ?>">
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