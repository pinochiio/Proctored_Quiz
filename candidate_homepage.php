<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if session is not set
    header("Location: login.php");
    exit();
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizEye | Home</title>
    <link rel="stylesheet" href="./assets/css/candidate_homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">
                <h1>Quiz<span>Eye</span></h1>
            </div>
            <div class="navbar-toggle" id="navbar-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <nav>
                <ul id="nav-links">
                    <li><a href="#features">Features</a></li>
                    <li><a href="#live-tournaments">Live Tournaments</a></li>
                    <li><a href="#quiz-types">Quiz Types</a></li>
                    <li><a href="#testimonials">Testimonials</a></li>
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="support.php">Contact Us</a></li>
                    <li><a href="logout.php" class="btn login-btn">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h2>Welcome to QuizEye</h2>
            <p>Where knowledge meets fun! Join now and test your skills.</p>
            <a href="#quiz-types" class="btn start-btn">Get Back to Quizzes</a>
        </div>
        <div class="hero-image">
            <img src="https://png.pngtree.com/png-vector/20220620/ourmid/pngtree-quiz-show-game-trivia-podium-png-image_5132650.png" alt="Quiz Hero">
        </div>
    </section>

    <section id="features" class="features-section">
        <h2>Why QuizEye?</h2>
        <div class="features-grid">
            <div class="feature">
                <i class="fas fa-brain"></i>
                <h3>Challenging Quizzes</h3>
                <p>Test your knowledge with quizzes curated by experts across multiple domains.</p>
            </div>
            <div class="feature">
                <i class="fas fa-stopwatch"></i>
                <h3>Timed Quizzes</h3>
                <p>Beat the clock with time-bound quizzes that test your speed and accuracy.</p>
            </div>
            <div class="feature">
                <i class="fas fa-user-shield"></i>
                <h3>Anti-Cheat System</h3>
                <p>Our platform ensures a fair play environment with face recognition and live proctoring.</p>
            </div>
            <div class="feature">
                <i class="fas fa-trophy"></i>
                <h3>Global Leaderboard</h3>
                <p>Compete with quiz-takers around the world and climb to the top of the leaderboard.</p>
            </div>
        </div>
    </section>

    <section id="live-tournaments" class="live-tournaments-section">
        <div class="confetti"></div> <!-- Confetti container -->
        <h2>🎉 Join Our Live Tournaments! 🎉</h2>
        <p>Put your skills to the test and compete against others in real-time.</p>
        <div class="tournament-content">
            <p>Compete with players around the world and win exciting prizes!</p>
            <a href="live-tournament.html" class="btn tournament-btn">Join Live Tournament</a>
        </div>
    </section>

    <section id="quiz-types" class="quiz-types-section" style="align-items:left;">
    <h2>All Quizzes</h2>
    <div class="quiz-categories" id="quiz-categories" style="align-items:left;">
        <?php
        // Connect to the database
        $conn = new mysqli("localhost", "root", "", "proctored_quiz");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch the logged-in candidate ID from the session
        $candidate_id = $_SESSION['user_id'];

        // SQL query to fetch quizzes assigned to the candidate
        $sql = "
            SELECT 
                q.quiz_id, 
                q.quiz_description, 
                q.start_time, 
                q.end_time, 
                q.quiz_duration,
                COUNT(quest.question_id) AS total_questions,
                SUM(quest.question_marks) AS total_marks
            FROM 
                quiz_candidates qc
            INNER JOIN 
                quizzes q ON qc.quiz_id = q.quiz_id
            LEFT JOIN 
                questions quest ON q.quiz_id = quest.quiz_id
            WHERE 
                qc.candidate_id = ?
            GROUP BY 
                q.quiz_id
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $candidate_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Get the current time
        $current_time = new DateTime();

        // Generate HTML for the quiz types
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Convert quiz start and end times to DateTime objects
                $timezone = new DateTimeZone('Asia/Kolkata'); 

// Get the current time in the specified time zone
$current_time = new DateTime("now", $timezone);

// Get the quiz end time from the database and set the correct time zone
$quiz_end_time = new DateTime($row['end_time'], $timezone); // Assuming 'end_time' is stored in UTC or your desired time zone
$quiz_duration = $row['quiz_duration']; // Duration in minutes


// First, check if the current time is before the quiz end time
if ($current_time->getTimestamp() < $quiz_end_time->getTimestamp()) {
    

    // Convert quiz duration to seconds (1 minute = 60 seconds)
    $quiz_duration_in_seconds = $quiz_duration * 60;

    // Add quiz duration (in seconds) to current time
    $time_until_quiz_end = $current_time->getTimestamp() + $quiz_duration_in_seconds;

    // Subtract that from the quiz's end time
    $time_remaining = $quiz_end_time->getTimestamp() - $time_until_quiz_end;

    // Check if quiz is accessible based on the remaining time
    if ($time_remaining > 0) {
        // The candidate can take the quiz
        $error_message = null;
    } else {
        // The candidate cannot take the quiz, not enough time left
        $error_message = "You don't have enough time left to complete this quiz.";
    }
} else {
    // Quiz has already ended
    $error_message = "Quiz is no longer available to take.";
}              

                echo '<div class="quiz-type">';
                echo '<h3 style="color:#fa574e;">' . htmlspecialchars($row['quiz_description']) . '</h3>';
                echo '<p style="margin-bottom:10px;"><strong>Start Time: </strong>' . htmlspecialchars($row['start_time']) . '</p>';
                echo '<p style="margin-bottom:10px;"><strong>End Time: </strong>' . htmlspecialchars($row['end_time']) . '</p>';
                echo '<p style="margin-bottom:10px;"><strong>Quiz Duration: </strong>' . htmlspecialchars($row['quiz_duration']) . ' minutes</p>';
                echo '<p style="margin-bottom:10px;"><strong>Total Questions: </strong>' . htmlspecialchars($row['total_questions']) . '</p>';
                echo '<p style="margin-bottom:13px;"><strong>Total Marks: </strong>' . htmlspecialchars($row['total_marks']) . '</p>';

                if ($error_message) {
                    echo '<div style="color: red; font-weight: bold; margin-bottom: 10px;">' . $error_message . '</div>';
                } else {
                    echo '<a href="instruction_page.php?quiz_id=' . htmlspecialchars($row['quiz_id']) . '&quiz_duration=' . htmlspecialchars($row['quiz_duration']) . '&total_questions=' . htmlspecialchars($row['total_questions']) . '" class="btn">Take Quiz</a>';
                }

                echo '</div>';
            }
        } else {
            echo '<div style="
            text-align: center !important; 
            font-size: 18px; 
            align-items:center !important;
            background-color: white; 
            padding: 20px; 
            border-radius: 8px;
            border: 1px solid #d8000c; 
            margin: 20px auto; 
            max-width: 600px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        ">
            No quizzes assigned to you at the moment.
        </div>';
        }

        $stmt->close();
        $conn->close();
        ?>
    </div>
</section>


    <section id="testimonials" class="testimonials-slider-section">
        <h2>What Our Users Say</h2>
        <div class="slider">
            <div class="testimonial">
                <p>"QuizEye has transformed the way I learn. The quizzes are engaging and informative!"</p>
                <h4>- Taylor</h4>
            </div>
            <div class="testimonial">
                <p>"I love the competitive aspect of the live tournaments. It keeps me motivated!"</p>
                <h4>- Alex</h4>
            </div>
            <div class="testimonial">
                <p>"The UI/UX of the platform is smooth, and the anti-cheat system is a game changer!"</p>
                <h4>- Sarah</h4>
            </div>
        </div>
    </section>

    <section id="faq" class="faq-section">
        <h2>Frequently Asked Questions</h2>
        <div class="faq-container">
            <div class="faq-item">
                <h3>How do I join a quiz?</h3>
                <p>Simply log in, navigate to the "Quizzes" section, and click on "Take Quiz" to begin.</p>
            </div>
            <div class="faq-item">
                <h3>Is there a time limit for quizzes?</h3>
                <p>Yes, quizzes are timed. You need to finish them within the allocated time.</p>
            </div>
            <div class="faq-item">
                <h3>How can I view my results?</h3>
                <p>Your results will be available after you complete the quiz, and they will be posted to your dashboard.</p>
            </div>
        </div>
    </section>

    <footer id="contact">
        <div class="footer-container">
            <div class="contact-info">
                <p>Contact us: quizeye@example.com</p>
                <p>Follow us on social media: <a href="#" target="_blank">Facebook</a>, <a href="#" target="_blank">Twitter</a>, <a href="#" target="_blank">Instagram</a></p>
            </div>
        </div>
    </footer>

    <script src="./assets/js/candidate_homepage.js"></script>
</body>
</html>