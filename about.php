<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$feedback_message = '';
// Check if the feedback form was submitted
if (isset($_POST['submit_feedback'])) {
    // In a real application, you would save this to a database or send an email.
    // For this project, we'll just prepare a success message.
    $name = htmlspecialchars($_POST['name']); // Use htmlspecialchars for security
    $feedback_message = "Thank you for your feedback, " . $name . "!";
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
        }
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: var(--background-color);
            margin: 0;
            color: var(--text-color);
        }
        nav {
            background: var(--primary-color);
            padding: 10px 20px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        nav a {
            color: white;
            margin: 5px 10px;
            text-decoration: none;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background 0.3s;
        }
        nav a:hover {
            background: var(--secondary-color);
        }
        .content-container {
            width: 90%;
            max-width: 900px;
            margin: 40px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            padding: 40px 50px;
            box-sizing: border-box;
        }
        .hero {
            text-align: center;
            margin-bottom: 40px;
        }
        .hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 30px;
            align-items: start; /* Align items to the top */
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
            font-weight: 600;
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
            transition: color 0.3s;
        }
        #contact-info a:hover {
            color: var(--secondary-color);
        }
        #contact-info .contact-details {
             margin-top: 15px;
        }

        /* --- New Contact Link Styles --- */
        .contact-link {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }
        .contact-link:last-child {
            margin-bottom: 0;
        }
        .contact-link i {
            color: var(--primary-color);
            margin-right: 12px;
            font-size: 1.2rem;
            width: 20px; /* Ensures alignment */
            text-align: center;
        }
        .contact-link a {
            font-size: 1rem;
        }

        /* --- Feedback Form Styles --- */
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
        .feedback-form textarea {
            resize: vertical;
            min-height: 80px;
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
        .feedback-success {
            text-align: center;
            font-weight: 600;
            color: var(--success-color);
            margin-top: 20px;
        }

        @media(max-width: 768px) {
            .content-container {
                width: 95%;
                padding: 30px 25px;
            }
            .hero h1 { font-size: 2rem; }
            .info-grid {
                grid-template-columns: 1fr; /* Stack columns on mobile */
            }
        }
    </style>
</head>
<body>

    <nav>
        <a href="home.php">Home</a>
        <a href="sharelist.php">Share List</a>
        <a href="about.php">About Us</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="content-container">
        <div class="hero">
            <h1>About StockBuddy</h1>
        </div>

        <!-- Display feedback success message here -->
        <?php if (!empty($feedback_message)): ?>
            <p class="feedback-success"><i class="fa-solid fa-check-circle"></i> <?php echo $feedback_message; ?></p>
        <?php endif; ?>

        <div class="info-grid">
            <div class="info-section" id="mission-section">
                <h2><i class="fa-solid fa-bullseye"></i> Our Mission</h2>
                <p>To demystify the stock market for beginners, offering a clean interface where users can monitor live stock prices and trends effortlessly.</p>
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
                <p>
                    Got a question or suggestion? <br> We'd love to hear from you.<br> Reach out to us..<br><b>Email</b> 
                </p>

                <div class="contact-details">
                    <div class="contact-link">
                        <i class="fa-solid fa-envelope-open-text"></i>
                        <a href="mailto:stockbuddy@example.com">stockbuddy@gmail.com</a>
                    </div> 
                <p>
                    <b>CO. NO.:</b>
                    <br>  
                </p>
                     <div class="contact-link">
                        <i class="fa-solid fa-phone-volume"></i>
                        <a href="tel:+919876543210">+91 9876543210</a>
                    </div>
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

