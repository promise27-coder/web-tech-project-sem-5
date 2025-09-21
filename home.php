<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockBuddy - Home</title>
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
            --light-gray: #f1f7f8ff;
        }
            .hero-container h1 span {
                color: #c62330ff;
                background-color: #0c6b2f98; 
                font-family: Georgia, serif;
                padding: 5px 12px;       
                border-radius: 0px;  
            }     
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            color: var(--text-color);

            /* Background Image Properties */
            /* IMPORTANT: Replace 'background.jpg' with your image's actual filename */
            background-image: linear-gradient(rgba(232, 245, 233, 0.7), rgba(232, 245, 233, 0.7)), url('images/home.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
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
        .hero-container {
            text-align: center;
            padding: 80px 20px;
        }
        .hero-container h1 {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0 0 15px 0;
        }
        .hero-container p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto 30px auto;
            line-height: 1.6;
        }
        .cta-button {
            background: var(--primary-color);
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: background 0.3s, transform 0.3s;
            box-shadow: 0 4px 15px rgba(0, 121, 107, 0.4);
        }
        .cta-button:hover {
            background: var(--secondary-color);
            transform: translateY(-3px);
        }
        .features-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            padding: 40px 20px;
            flex-wrap: wrap;
        }
        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            text-align: center;
            width: 250px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
        }
        .feature-card i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        .feature-card h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0 0 10px 0;
        }
        .feature-card p {
            margin: 0;
            line-height: 1.6;
            font-size: 0.95rem;
        }
        @media(max-width: 768px) {
            .hero-container h1 { font-size: 2.2rem; }
            .hero-container p { font-size: 1rem; }
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

    <div class="hero-container">
        <h1>Welcome to <span>StockBuddy</span></h1>
        <p>Your simple and powerful tool for tracking live stock prices and trends with ease.</p>
        <a href="sharelist.php" class="cta-button">View Live Share List</a>
    </div>

    <div class="features-container">
        <div class="feature-card">
            <i class="fa-solid fa-bolt-lightning"></i>
            <h3>Live Price Tracking</h3>
            <p>Monitor real-time price fluctuations of your favorite stocks, updated every second.</p>
        </div>
        <div class="feature-card">
            <i class="fa-solid fa-chart-line"></i>
            <h3>Visual Trend Analysis</h3>
            <p>Instantly visualize market trends with our clean and dynamic sparkline charts.</p>
        </div>
        <div class="feature-card">
            <i class="fa-solid fa-computer-mouse"></i>
            <h3>Simple & Clean Interface</h3>
            <p>A clean, user-friendly design that makes stock tracking intuitive and effortless.</p>
        </div>
    </div>

</body>
</html>