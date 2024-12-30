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
$medicines = [];
$formVisible = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formVisible = true;
    $symptom = "";
    if (!empty($_POST['symptom']) && $_POST['symptom'] !== "Other") {
        $symptom = trim($_POST['symptom']);
    } elseif (!empty($_POST['symptoms'])) {
        $symptom = trim($_POST['symptoms']);
    }
    if (!empty($symptom)) {
        $stmt = $conn->prepare("SELECT medicine_name, symptoms, category, usage_instruction, side_effects, price FROM medicine WHERE symptoms LIKE ?");
        $symptomWithWildcard = "%" . $symptom . "%";
        $stmt->bind_param("s", $symptomWithWildcard);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $medicines[] = $row;
        }
        $stmt->close();
    } else {
        echo "<script>alert('Please enter a symptom or select one from the list.');</script>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DocTrack - QuickMedi</title>
    <link rel="stylesheet" href="style_quickmedi.css">
    <link rel="icon" type="image/x-icon" href="icon.png">
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const formSection = document.getElementById("formSection");
            const getStartedButton = document.getElementById("getStartedButton");
            <?php if ($formVisible): ?>
                formSection.style.display = "block";
                getStartedButton.style.display = "none";
            <?php else: ?>
                formSection.style.display = "none";
            <?php endif; ?>
            getStartedButton.addEventListener("click", () => {
                formSection.style.display = "block";
                getStartedButton.style.display = "none";
            });
        });
    </script>
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
        <h1>QuickMedi</h1>
        <section class="intro">
        <p><b>Welcome to the QuickMedi page, a convenient and reliable tool designed to provide personalized 
                medicine recommendations based on your health concerns. Whether you're dealing with common ailments 
                or facing more specific symptoms, QuickMedi helps you find the right over-the-counter medications 
                or treatment options tailored to your needs. By entering your symptoms or describing the problems 
                you're experiencing, our system instantly processes the information to suggest appropriate medicines 
                that can offer relief.<br><br>
                On this page, you can either input your symptoms directly or choose from a list of common health 
                problems to quickly get a suitable prescription. If you're unsure about your condition, we've made 
                it easier by listing some typical health issues, such as headaches, fever, stomachaches, and colds. 
                Simply select the one that best matches how you're feeling, and QuickMedi will recommend the top 
                medications available.<br><br>            
                However, if your symptoms are more specific or not listed in the provided options, you can use the 
                "Other" option to manually enter your symptoms. This allows you to receive a more targeted suggestion 
                that fits your unique situation. The system ensures that the recommendations are accurate and 
                well-suited to your health condition, making it a quick solution for self-treatment.<br>            
                In addition to suggesting medicines, QuickMedi also provides valuable information about the prescribed 
                drugs, including dosage guidelines, potential side effects, and precautions to take. This helps ensure 
                that you are fully informed before using any medication, empowering you to take care of your health 
                confidently and safely. With easy-to-understand instructions, the platform helps you manage minor 
                health issues before seeking professional medical advice, ensuring that you are on the right path 
                to recovery.<br><br>            
                QuickMedi is especially beneficial for those seeking immediate assistance without the need to visit 
                a pharmacy or doctor. By allowing you to access safe medication advice from the comfort of your home, 
                it saves time and effort, offering a hassle-free way to address health problems. The tool is ideal 
                for those needing quick solutions for non-emergency medical concerns, enabling you to make informed 
                choices about your health.<br><br>            
                As part of our mission to support better healthcare, QuickMedi integrates seamlessly into the overall 
                doctor appointment system, allowing you to consult doctors or healthcare professionals if the medicines 
                provided do not fully address your health concerns. In case of recurring symptoms or the need for 
                specialized medical attention, the page directs you to book an appointment with a qualified doctor, 
                ensuring you get the proper care when necessary.<br><br>            
                The goal of QuickMedi is to make healthcare accessible and efficient. It puts power in your hands, 
                allowing you to take control of your minor health concerns without any delay. This simple and 
                user-friendly interface guarantees that you can quickly get the medication suggestions you need with 
                minimal effort, ensuring your well-being is prioritized at every step. With QuickMedi, you're always 
                one step closer to feeling better, no matter the situation.</b></p>
        </section>
        <div class="get_started">
            <button id="getStartedButton"><strong>Get Started</strong></button>
        </div>
        <div id="formSection">
            <form method="POST" action="quickmedi.php">
                <label for="symptoms">Enter your symptoms or health concerns:</label>
                <textarea id="symptoms" name="symptoms" rows="4" placeholder="Describe your symptoms here..."></textarea>
                <p>Or choose from common health problems:</p>
                <select name="symptom">
                    <option value="" disabled selected>Select a problem</option>
                    <option value="Fever">Fever</option>
                    <option value="Vomit">Vomit</option>
                    <option value="Headache">Headache</option>
                    <option value="Stomach Pain">Stomach Pain</option>
                    <option value="Other">Other</option>
                </select>
                <div class="checkbox">
                    <input type="checkbox" required>
                    <label>Note: Recommendations are only for minor symptoms. Always consult a doctor if symptoms persist.</label>
                </div>
                <button type="submit"><strong>Submit</strong></button>
            </form>
            <section>
            <?php if (!empty($medicines)): ?>
                <h2>Medicine Recommendations</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Medicine Name</th>
                            <th>Symptoms</th>
                            <th>Category</th>
                            <th>Dosage</th>
                            <th>Side Effects</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($medicines as $med): ?>
                            <tr>
                                <td><?= htmlspecialchars($med['medicine_name']); ?></td>
                                <td><?= htmlspecialchars($med['symptoms']); ?></td>
                                <td><?= htmlspecialchars($med['category']); ?></td>
                                <td><?= htmlspecialchars($med['usage_instruction']); ?></td>
                                <td><?= htmlspecialchars($med['side_effects']); ?></td>
                                <td><?= htmlspecialchars($med['price']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <p>No medicines found for the entered symptom.</p>
            <?php endif; ?>
        </section>
            <section>
                <h1>Precautions</h1>
                <p>
                    <b>1. Consult a Doctor:</b> The medicine suggestions provided by QuickMedi are based on common 
                    health concerns and symptoms but should not replace a doctor's consultation. Always seek 
                    professional medical advice for accurate diagnosis and treatment.<br><br>
                    <b>2. Avoid Self-Medication:</b> Do not use medicines suggested here for serious or recurring 
                    health issues without proper medical guidance.<br><br>
                    <b>3. Allergies & Conditions:</b> Ensure that you are not allergic to the suggested medicines 
                    and that they do not conflict with any pre-existing health conditions or medications you are 
                    currently taking.<br><br>
                    <b>4. Dosage Instructions:</b> Follow the recommended dosage provided by a healthcare professional 
                    or as indicated on the medicine packaging.<br><br>
                    <b>5. Emergency Situations:</b> In case of severe symptoms or emergencies, avoid using QuickMedi 
                    and seek immediate medical help.<br><br>
                    <b>6. Not for Children:</b> QuickMedi is not intended for use in diagnosing or prescribing medications 
                    for children. Consult a pediatrician for child-specific health concerns.<br><br>
                </p>
            </section>
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