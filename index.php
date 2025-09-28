<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to StockBuddy - Track Stocks with Ease</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #00796b;
            --secondary-color: #004d40;
            --text-color: #f5f5f5;
            --dark-bg: #121212;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark-bg);
            color: var(--text-color);
            overflow-x: hidden;
        }
        .main-container {
            min-height: 100vh;
            background-image: linear-gradient(rgba(18, 18, 18, 0.85), rgba(18, 18, 18, 0.85)), url('images/home.png');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
        }
        header {
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: absolute;
            width: 100%;
            top: 0;
            z-index: 10;
        }
        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }
        .logo i {
            margin-right: 8px;
        }
        .nav-buttons a {
            text-decoration: none;
            color: white;
            padding: 10px 22px;
            border-radius: 50px;
            font-weight: 600;
            margin-left: 10px;
            transition: all 0.3s ease;
        }
        .nav-buttons .btn-login {
            border: 2px solid var(--primary-color);
        }
        .nav-buttons .btn-login:hover {
            background-color: var(--primary-color);
        }
        .nav-buttons .btn-register {
            background-color: var(--primary-color);
        }
        .nav-buttons .btn-register:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .hero-section {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 120px 30px 50px;
        }
        .hero-content {
            max-width: 800px;
        }
        .hero-content h1 {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
        }
        .hero-content h1 span {
            color: var(--primary-color);
        }
        .hero-content p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto 30px auto;
            color: rgba(255, 255, 255, 0.8);
        }
        .hero-content .cta-button {
            background: var(--primary-color);
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
            box-shadow: 0 4px 20px rgba(0, 121, 107, 0.4);
        }
        .hero-content .cta-button:hover {
            background: var(--secondary-color);
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(0, 77, 64, 0.5);
        }
        
        .features-section {
            padding: 80px 30px;
            background-color: #1c1c1c;
        }
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 50px;
            font-weight: 700;
        }
        .features-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* <<< AA NAVO CSS CODE ADD KARYO CHHE >>> */
        .feature-link {
            text-decoration: none;
            color: inherit;
        }

        .feature-card {
            background: #2a2a2a;
            padding: 40px 30px;
            border-radius: 12px;
            text-align: center;
            width: 300px;
            border: 1px solid #333;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%; /* Added for equal height */
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        }
        .feature-card i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        .feature-card h3 {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .feature-card p {
            color: rgba(255, 255, 255, 0.7);
        }
        
        footer {
            background-color: var(--dark-bg);
            color: #888;
            text-align: center;
            padding: 25px;
            border-top: 1px solid #333;
        }

        @media(max-width: 768px) {
            header { padding: 20px; }
            .hero-content h1 { font-size: 2.5rem; }
            .hero-content p { font-size: 1rem; }
            .nav-buttons .btn-login { display: none; }
        }
    </style>
</head>
<body>

    <div class="main-container">
        <header>
            <a href="index.php" class="logo"><i class="fa-solid fa-chart-pie"></i>StockBuddy</a>
            <div class="nav-buttons">
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-register">Register</a>
            </div>
        </header>

        <section class="hero-section">
            <div class="hero-content" data-aos="fade-up">
                <h1>Track the Market, <span>Seize the Opportunity.</span></h1>
                <p>StockBuddy offers a clean, simple, and powerful interface to monitor real-time stock prices and trends effortlessly. Your journey to smart investing starts here.</p>
                <a href="login.php" class="cta-button">Get Started for Free</a>
            </div>
        </section>
    </div>

    <section class="features-section">
        <h2 class="section-title" data-aos="fade-up">Why Choose StockBuddy?</h2>
        <div class="features-container">

            <a href="login.php" class="feature-link" data-aos="zoom-in" data-aos-delay="100">
                <div class="feature-card">
                    <i class="fa-solid fa-bolt-lightning"></i>
                    <h3>Live Price Tracking</h3>
                    <p>Monitor real-time price fluctuations of your favorite stocks, updated every second.</p>
                </div>
            </a>

            <a href="login.php" class="feature-link" data-aos="zoom-in" data-aos-delay="200">
                <div class="feature-card">
                    <i class="fa-solid fa-chart-line"></i>
                    <h3>Visual Trend Analysis</h3>
                    <p>Instantly visualize market trends with our clean and dynamic sparkline charts.</p>
                </div>
            </a>

            <a href="login.php" class="feature-link" data-aos="zoom-in" data-aos-delay="300">
                <div class="feature-card">
                    <i class="fa-solid fa-computer-mouse"></i>
                    <h3>Simple & Clean Interface</h3>
                    <p>A clean, user-friendly design that makes stock tracking intuitive and effortless.</p>
                </div>
            </a>
            
        </div>
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> StockBuddy. All Rights Reserved. Devloped By 𖹭 PROMIS VORA 𖹭 </p>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 600, /* Animation speed thodi vadhari chhe */
            once: true,
        });
    </script>
</body>
</html>