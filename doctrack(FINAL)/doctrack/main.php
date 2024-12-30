<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>DocTrack</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_home.css">
    <link rel="icon" type="image/x-icon" href="icon.png">
</head>
<body>
    <header>
        <div class="header-logo">
            <img src="logo2.png" alt="DocTrack Logo">
        </div>
        <nav>
            <ul>
                <li><a href="main.php" class="active">Home</a></li>
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
                echo '<a href="log_in.php"><button><strong>Log In</strong></button></a>';
            }
            ?>
        </div>
    </header>
    <marquee><strong>Welcome to DocTrack! Your trusted platform for finding top doctors, booking appointments, and getting quick medicine recommendations. 
        Stay healthy, stay safe!</strong></marquee>
    <main>
        <h1><u>DocTrack</u></h1>
        <section class="section">
            <div>
                <p><b>Your health is our utmost priority, and at DocTrack, we strive to make healthcare accessible and convenient for everyone. Our platform 
                    acts as your trusted companion, simplifying the process of finding top doctors within your locality. Whether you need a routine consultation 
                    or expert advice from a specialist, DocTrack connects you with verified professionals. With features like real-time doctor availability, 
                    detailed profiles, and patient reviews, you can make informed choices for your health. In emergencies, our comprehensive treatment guide equips 
                    you with actionable steps to manage critical situations until professional help arrives. DocTrack is here to make your healthcare journey 
                    seamless and stress-free.
                </b></p>
            </div>
        </section>
        <br><hr><br>
        <section>
        <div class="sec">
            <div class="left1">
            <p>DocTrack offers numerous benefits to simplify and enhance healthcare access. Key highlights includes:</p> 
            <ul> 
                <li><strong>Time-Saving Convenience:</strong> Avoid waiting in long queues at clinics or hospitals. DocTrack allows you to find doctors and 
                schedule appointments with just a few clicks, saving precious time.</li> 
                <li><strong>Wide Doctor Choices:</strong> Access a diverse network of skilled doctors, filterable by specialization, fees, ratings, and availability, 
                ensuring the right care for your needs.</li>  
                <li><strong>Cost Efficiency:</strong> Save on travel expenses and unnecessary clinic visits, making quality healthcare more affordable.</li> 
                <li><strong>Privacy and Comfort:</strong> DocTrack ensures a secure platform for patients to discuss sensitive health issues with confidence.</li> 
                <li><strong>24/7 Service:</strong> With round-the-clock availability, DocTrack provides healthcare assistance whenever you need it, including during 
                emergencies.</li> 
                <li><strong>Flexible Appointments:</strong> Book in-person appointments with doctors when preferred, offering a hybrid approach to healthcare access.</li> </ul>
            </div>
            <div class="right1">
                <br>
                <img src="pic1.png">
            </div>
        </div>
        </section>
        <br><hr><br>
        <section> 
        <div class="sec">
        <div class="left2">
                <br>
                <img src="pic2.jpg">
            </div>
            <div class="right2">
                <p>DocTrack redefines healthcare by offering a seamless platform that combines convenience, accessibility, and efficiency. It empowers users to 
                    find the best doctors near them based on qualifications, ratings, and availability, eliminating the stress of endless searches. Whether it’s 
                    booking a physical appointment or opting for a virtual consultation via video or voice calls, DocTrack adapts to your needs.
                    <br><br>
                    In today’s fast-paced world, simplicity and efficiency in healthcare are essential. DocTrack eliminates the complexities of traditional healthcare 
                    systems by offering an all-in-one solution for patients.
                    <br><br>
                    By cutting travel and wait times, DocTrack saves both time and costs, making healthcare more efficient. DocTrack is the ultimate solution for 
                    modern healthcare needs.
                    <div>
                    <a href="sign_up.php">
                        <button><strong>Register Now</strong></button>
                    </a>
                </div>
                </p>
            </div>
        </div>
        </section>
        <br><hr><br>
        <section>
            <div class="sec">
            <div class="left1">
                <p>The <strong>QuickMedi</strong> feature in DocTrack is designed to provide instant and reliable medicine recommendations based on your health 
                concerns. Users can input their symptoms or select common health problems from a predefined list to receive tailored medicine suggestions. 
                <br><br>
                What makes QuickMedi unique is its built-in database of medicines, enriched with essential details such as usage, dosage, and precautionary advice. 
                The feature emphasizes safety by offering suggestions only for non-critical conditions and urging professional consultation for severe or recurring 
                symptoms.
                <br><br>
                QuickMedi serves as a handy tool for quick relief while educating users about responsible medicine usage. Whether it’s a headache, cold, or minor 
                discomfort, QuickMedi empowers users with knowledge and convenience at their fingertips.
                </p> 
                <div>
                    <a href="quickmedi.php">
                        <button><strong>Take Medicine</strong></button>
                    </a>
                </div>
            </div>
            <div class="right1">
                <br>
                <img src="pic3.jpg">
            </div>
        </div>
        </section>
        <br><hr><br>
        <section>
        <div class="sec">
        <div class="left4">
                <br>
                <img src="pic4.jpg">
            </div>
            <div class="right4">
                <p>If you're experiencing health issues or just want a professional opinion, we highly encourage you to schedule an appointment with one of our 
                    top-rated doctors. Our Take Appointment page allows you to easily book consultations based on your symptoms, ensuring that you are connected 
                    to the most suitable healthcare professional for your needs. Whether you're dealing with a specific health concern or seeking a routine check-up, 
                    booking an appointment is a crucial step in receiving the best care possible.
                    <br><br>
                    By clicking the button below, you can access a list of doctors in your area, review their qualifications, fees, and availability, and choose the 
                    one that best fits your requirements. Don't wait for your health issues to escalate – get personalized medical attention today and take control 
                    of your well-being. Your health matters, and DocTrack makes it easy to get the care you deserve. Click below and schedule your appointment now!
                </p>
                    <div>
                        <a href="take_appointment.php">
                            <button><strong>Take Appointment</strong></button>
                        </a>
                    </div>
            </div>
        </div>
        </section>
        <br><hr><br>
        <section class="section">
            <div>
                <p><b>In conclusion, DocTrack is dedicated to making healthcare accessible, efficient, and personalized for everyone. Whether you're seeking immediate 
                    medical advice through our QuickMedi feature or booking a consultation with one of our expert doctors, we're here to help you manage your health 
                    with ease. Our platform offers a seamless experience, allowing you to connect with healthcare professionals at your convenience, ensuring that you 
                    receive the right treatment whenever you need it.
                    <br><br>
                    By choosing DocTrack, you're not just getting a service – you're gaining a reliable partner in your health journey. From managing appointments 
                    to receiving tailored medicine recommendations, we make sure that every step of your healthcare experience is simplified. Take charge of your 
                    well-being today and let DocTrack provide you with the tools to stay healthy, informed, and connected. Explore our features, book your appointment, 
                    and experience healthcare the way it should be – efficient and hassle-free.</b>
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