<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="style_about.css">
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
                <li><a href="about.php" class="active">About</a></li>
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
                echo '<a href="log_in.php"><button><strong>Log In</strong></button></a>';
            }
            ?>
    </div>
    </header>
    <marquee><strong>Welcome to DocTrack! Your trusted platform for finding top doctors, booking appointments, and getting quick medicine recommendations. 
        Stay healthy, stay safe!</strong></marquee>
    <main>
    <h1>About Us</h1>
        <section>
            <div class="sec">
            <p>Welcome to <strong>DocTrack</strong>, your trusted partner in accessing quality healthcare from the 
            comfort of your home. Our mission is to bridge the gap between patients and healthcare professionals, 
            ensuring that everyone has access to the medical advice they need, whenever they need it.</p>
        </div>
        </section>
        <br><hr><br>
        <section>
        <div class="sec">
            <h2>Our Mission</h2>
            <p>Our mission is simple yet profound: to make healthcare accessible, convenient, and affordable for 
            everyone. We understand that navigating the healthcare system can be overwhelming, and we are here to 
            simplify that process. Our goal is to empower patients with the information and resources they need to 
            make informed decisions about their health.</p>
        </div>
        </section>
        <br><hr><br>
        <section>
        <div class="sec">
            <h2>Who We Are</h2>
            <p>At <strong>DocTrack</strong>, we are a passionate team of healthcare professionals, technology 
            enthusiasts, and customer service experts dedicated to transforming the way healthcare is delivered. 
            Our platform is designed to provide a seamless online experience that connects patients with licensed 
            doctors who can offer consultations, advice, and treatment options in real time.</p>
        </div>
        </section>
        <br><hr><br>
        <section>
        <div class="sec">
            <h2>Our Values</h2>
            <ul>
                <li><strong>Compassion:</strong> We approach every interaction with empathy and understanding. Our 
                team is dedicated to creating a supportive environment where patients feel heard and valued.</li>
                <li><strong>Integrity:</strong> We operate with transparency and honesty in all our dealings. Our 
                commitment to ethical practices ensures that you receive reliable information and quality care.</li>
                <li><strong>Innovation:</strong> In an ever-evolving digital landscape, we embrace innovation to 
                enhance our services. We continuously seek out new technologies and methodologies to improve the 
                patient experience and outcomes.</li>
            </ul>
        </div>
        </section>
        <br><hr><br>
        <section>
        <div class="sec">
            <div class="team-section">
            <h2>Meet Our Team</h2>
            <div class="team-container">
                <div class="team-member">
                    <img src="21.jpg">
                    <h3>Soumyadip Tarafdar</h3>
                    <p>Front-end Developer, Database Manager</p>
                </div>
                <div class="team-member">
                    <img src="22.jpg">
                    <h3>Debdutta Mukherjee</h3>
                    <p>Back-end Developer</p>
                </div>
                <div class="team-member">
                    <img src="23.jpg">
                    <h3>Anannya Nag</h3>
                    <p>Developer</p>
                </div>
                <div class="team-member">
                    <img src="24.jpg">
                    <h3>Abir Das</h3>
                    <p>Developer</p>
                </div>
                <div class="team-member">
                    <img src="25.jpg">
                    <h3>Archak Mallick</h3>
                    <p>Developer</p>
                </div>
                <div class="team-member">
                    <img src="26.jpg">
                    <h3>Depayan Ghosh</h3>
                    <p>Developer</p>
                </div>
            </div>
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