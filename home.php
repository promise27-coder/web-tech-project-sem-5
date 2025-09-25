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
    <link rel="preconnect" href="https://fonts.gstatic" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #00796b;
            --secondary-color: #004d40;
            --background-color: #e8f5e9;
            --text-color: #333;
            --light-gray: #f1f7f8ff;
        }

        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            color: var(--text-color);
            background-image: linear-gradient(rgba(232, 245, 233, 0.7), rgba(232, 245, 233, 0.7)), url('images/home.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            overflow-x: hidden; 
        }

        nav {
            background: var(--primary-color);
            padding: 10px 20px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            align-items: center; 
        }
        nav a {
            color: white;
            margin: 5px 15px;
            text-decoration: none;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background 0.3s;
            font-size: 1rem; 
        }
        nav a:hover {
            background: var(--secondary-color);
        }

        /* 2. Live Stock Ticker CSS */
        .ticker-wrap {
            width: 100%;
            overflow: hidden;
            background-color: var(--secondary-color);
            padding: 10px 0;
            color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .ticker-move {
            display: inline-block;
            white-space: nowrap;
            animation: ticker-scroll 40s linear infinite;
        }
        .ticker-item {
            display: inline-block;
            padding: 0 2rem;
            font-size: 0.9rem;
        }
        .stock-up { color: #4caf50; }
        .stock-down { color: #f44336; }
        
        @keyframes ticker-scroll {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
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
        .hero-container h1 span {
            color: var(--secondary-color);
            font-weight: 700;
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
            transition: transform 0.3s, box-shadow 0.3s, background-color 0.3s, color 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px) scale(0.95); /* <<< AA LINE BADLI CHHE */
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            background-color: var(--primary-color);
            color: white;
        }
        .feature-card i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 20px;
            transition: color 0.3s;
        }
        .feature-card:hover i {
            color: white;
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
        
        /* 3. Footer CSS */
        footer {
            background-color: var(--secondary-color);
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }
        footer p {
            margin: 5px 0;
        }
        footer a {
            color: #4caf50;
            text-decoration: none;
            font-weight: bold;
        }

        /* 4. Local Video Section CSS */
        .video-container {
            margin: 50px auto 0 auto;
            max-width: 800px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            overflow: hidden; 
        }
        .video-container video {
            width: 100%;
            height: auto;
            display: block; 
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
        <a href="logout.php" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a>
    </nav>

    <div class="ticker-wrap">
        <div class="ticker-move" id="ticker-content">
            <div class="ticker-item">Loading live data...</div>
        </div>
    </div>

    <div class="hero-container">
        <h1>Welcome to <span>StockBuddy</span></h1>
        <p>Your simple and powerful tool for tracking live stock prices and trends with ease.</p>
        <a href="sharelist.php" class="cta-button">View Live Share List</a>
        
        <div class="video-container" data-aos="fade-up">
            <video autoplay loop muted>
                <source src="videos/video.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </div>

    <div class="features-container">
        <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
            <i class="fa-solid fa-bolt-lightning"></i>
            <h3>Live Price Tracking</h3>
            <p>Monitor real-time price fluctuations of your favorite stocks, updated every second.</p>
        </div>
        <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
            <i class="fa-solid fa-chart-line"></i>
            <h3>Visual Trend Analysis</h3>
            <p>Instantly visualize market trends with our clean and dynamic sparkline charts.</p>
        </div>
        <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
            <i class="fa-solid fa-computer-mouse"></i>
            <h3>Simple & Clean Interface</h3>
            <p>A clean, user-friendly design that makes stock tracking intuitive and effortless.</p>
        </div>
    </div>
    
    <footer>
        <p>Copyright &copy; 2025 StockBuddy. All Rights Reserved.</p>
        <p>Developed by <a href="#"> 𖹭 PROMIS VORA 𖹭</a></p>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // AOS Initialize
        AOS.init({
            duration: 800,
            once: true,
        });
    </script>
    
    <script>
        // Dummy Data (API કામ ન કરે તો આ દેખાડવા માટે)
        const dummyData = [
            { symbol: 'AAPL', price: '175.20', change: '+1.45' },
            { symbol: 'GOOGL', price: '140.80', change: '-0.25' },
            { symbol: 'MSFT', price: '380.10', change: '+3.10' },
            { symbol: 'TSLA', price: '250.60', change: '-2.70' },
            { symbol: 'AMZN', price: '135.90', change: '+0.80' },
        ];

        const tickerContent = document.getElementById('ticker-content');
        let html = '';

        dummyData.forEach(stock => {
            const changeClass = stock.change.startsWith('+') ? 'stock-up' : 'stock-down';
            html += `<div class="ticker-item">${stock.symbol}: $${stock.price} <span class="${changeClass}">${stock.change}</span></div>`;
        });
        
        // Ticker ને બે વાર દેખાડવા માટે જેથી સ્ક્રોલિંગ સ્મૂધ લાગે
        tickerContent.innerHTML = html + html;
    </script>

</body>
</html>