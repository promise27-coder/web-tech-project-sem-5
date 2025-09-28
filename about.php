<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$feedback_message = '';
$message_class = '';

// Check if the feedback form is submitted
if (isset($_POST['submit_feedback'])) {
    // Include the database connection file
    include 'db_connect.php';

    // Get form data and sanitize it
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Server-side validation
    if (!empty($name) && !empty($email) && !empty($message)) {
        // Prepare an insert statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            $feedback_message = "Thank you for your feedback, " . htmlspecialchars($name) . "!";
            $message_class = 'feedback-success';
        } else {
            $feedback_message = "Error: Something went wrong. Please try again.";
            $message_class = 'feedback-error';
        }
        $stmt->close();
    } else {
        $feedback_message = "Please fill out all fields.";
        $message_class = 'feedback-error';
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockBuddy - About Us</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --primary-color: #00796b;
            --secondary-color: #004d40;
            --background-color: #e8f5e9;
            --text-color: #333;
            --light-gray: #f1f8f5;
            --success-color: #2e7d32;
            --error-color: #d32f2f;
        }

        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: var(--background-color);
            margin: 0;
            color: var(--text-color);
        }

        /* <<< NAV CSS SUDHARI NAKHYO CHHE >>> */
        nav {
            background: var(--primary-color);
            padding: 10px 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            position: absolute;
            left: 40px;
        }

        .nav-links {
            display: flex;
            align-items: center;
        }

        .nav-links a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .nav-links a:hover {
            background: var(--secondary-color);
        }

        .nav-right {
            display: flex;
            align-items: center;
            position: absolute;
            right: 40px;
        }

        .logout-icon {
            color: white;
            font-size: 1.6rem;
            text-decoration: none;
            transition: transform 0.2s ease;
        }

        .logout-icon:hover {
            transform: scale(1.1);
        }

        .content-container {
            width: 90%;
            max-width: 900px;
            margin: 40px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            padding: 40px 50px;
            box-sizing: border-box;
        }

        .hero {
            text-align: center;
            margin-bottom: 30px;
        }

        .hero h1 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin: 0;
        }

        .feedback-message {
            text-align: center;
            font-weight: 600;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .feedback-success {
            color: var(--success-color);
            background-color: #d4edda;
        }

        .feedback-error {
            color: var(--error-color);
            background-color: #f8d7da;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 30px;
            align-items: start;
        }

        .info-section {
            padding: 25px;
            border-radius: 8px;
            background: var(--light-gray);
        }

        .info-section h2 {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            color: var(--secondary-color);
            margin: 0 0 15px 0;
        }

        .info-section h2 i {
            margin-right: 12px;
            font-size: 1.3rem;
        }

        .info-section p {
            margin: 0;
            line-height: 1.7;
        }

        #mission-section {
            grid-column: 1 / -1;
            text-align: center;
        }

        #contact-info a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        #contact-info .contact-details {
            margin-top: 15px;
        }

        .contact-link {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .contact-link i {
            color: var(--primary-color);
            margin-right: 12px;
            font-size: 1.2rem;
            width: 20px;
            text-align: center;
        }

        .feedback-form input,
        .feedback-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        .feedback-form button {
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: background 0.3s;
        }

        .feedback-form button:hover {
            background: var(--secondary-color);
        }

        @media(max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <nav>
        <a href="home.php" class="logo">StockBuddy</a>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="sharelist.php">Share List</a>
            <a href="portfolio.php">Portfolio</a>
            <a href="about.php">About Us</a>
        </div>
        <div class="nav-right">
            <a href="logout.php" class="logout-icon" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a>
        </div>
    </nav>
    <div class="content-container">
        <div class="hero">
            <h1>About StockBuddy</h1>
        </div>
        <?php if (!empty($feedback_message)): ?>
            <p class="feedback-message <?php echo $message_class; ?>"><i class="fa-solid fa-circle-info"></i>
                <?php echo $feedback_message; ?></p>
        <?php endif; ?>
        <div class="info-grid">
            <div class="info-section" id="mission-section">
                <h2><i class="fa-solid fa-bullseye"></i> Our Mission</h2>
                <p>To demystify the stock market for beginners, offering a clean interface where users can monitor live
                    stock prices and trends effortlessly.</p>
            </div>
            <div class="info-section">
                <h2><i class="fa-solid fa-user-graduate"></i> Created By</h2>
                <p>Promise Vora (Indus University)</p>
                <p>_IU2341050117_</p>
            </div>
            <div class="info-section">
                <h2><i class="fa-solid fa-code"></i> Technology Stack</h2>
                <p>HTML, CSS, <br> JavaScript, PHP</p>
            </div>
            <div class="info-section" id="contact-info">
                <h2><i class="fa-solid fa-comments"></i> Contact Us</h2>
                <p>Got a question or suggestion? <br> We'd love to hear from you.<br> Reach out to us..<br><b>Email</b>
                </p>
                <div class="contact-details">
                    <div class="contact-link"><i class="fa-solid fa-envelope-open-text"></i><a
                            href="mailto:stockbuddy@example.com">stockbuddy@gmail.com</a></div>
                    <p><b>CO. NO.:</b><br></p>
                    <div class="contact-link"><i class="fa-solid fa-phone-volume"></i><a href="tel:+919876543210">+91
                            9876543210</a></div>
                </div>
            </div>
            <div class="info-section">
                <h2><i class="fa-solid fa-paper-plane"></i> Send Feedback</h2>
                <form class="feedback-form" method="post">
                    <input type="text" name="name" placeholder="Your Name" required>
                    <input type="email" name="email" placeholder="Your Email" required>
                    <textarea name="message" placeholder="Your Message..." required></textarea>
                    <button type="submit" name="submit_feedback">Submit</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>