<?php
session_start();
$error = '';

if (isset($_SESSION['user'])) {
    header("Location: home.php");
    exit();
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username == "admin" && $password == "1234") {
        $_SESSION['user'] = $username;
        header("Location: home.php");
        exit();
    } else {
        $error = "wrong username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockBuddy - Secure Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #00796b; /* Main Green */
            --secondary-color: #004d40; /* Dark Green */
            --dark-bg: #1a1a1a; /* Dark background for the vibe */
            --text-light: #f5f5f5;
            --text-dark: #333;
            --input-border-color: #ccc;
            --up-color: #4caf50; /* Green for upward movement */
            --down-color: #f44336; /* Red for downward movement */
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #e8f5e9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden; /* Important for animation */
        }

        .login-container {
            width: 950px;
            max-width: 90%;
            height: 600px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
            display: flex;
            overflow: hidden;
            position: relative; /* For z-index of animations */
        }

        /* === Left Vibe Side === */
        .vibe-side {
            width: 50%;
            background-color: var(--dark-bg);
            color: var(--text-light);
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            position: relative;
            overflow: hidden;
            z-index: 1; /* Make sure text is above animation */
        }

        .vibe-side h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
            z-index: 2;
        }

        .vibe-side p {
            font-size: 1.2rem;
            font-weight: 300;
            z-index: 2;
            max-width: 350px;
        }

        /* === Animated Stock Graph Background === */
        .animated-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none; /* Allows clicks/interaction to pass through */
            z-index: 0; /* Keep it in the background */
        }

        .stock-element {
            position: absolute;
            opacity: 0;
            animation: stock-flow 15s linear infinite;
        }

        /* Up Arrow */
        .stock-element.up-arrow {
            font-size: 3rem;
            color: var(--up-color);
            transform: translateY(0) rotate(45deg); /* Initial position */
        }

        /* Down Arrow */
        .stock-element.down-arrow {
            font-size: 3rem;
            color: var(--down-color);
            transform: translateY(0) rotate(-45deg);
        }

        /* Candle Stick */
        .stock-element.candle {
            width: 10px;
            height: 50px;
            background-color: var(--up-color); /* Default candle color */
            border-radius: 2px;
        }
        .stock-element.candle.red {
            background-color: var(--down-color);
        }
        .stock-element.candle::before, .stock-element.candle::after {
            content: '';
            position: absolute;
            width: 2px;
            background-color: inherit;
        }
        .stock-element.candle::before { /* Top wick */
            height: 15px;
            top: -15px;
            left: 4px;
        }
        .stock-element.candle::after { /* Bottom wick */
            height: 15px;
            bottom: -15px;
            left: 4px;
        }


        @keyframes stock-flow {
            0% {
                transform: translateY(100vh) translateX(0) scale(0.5) rotate(0deg);
                opacity: 0;
            }
            20% { opacity: 1; }
            80% { opacity: 1; }
            100% {
                transform: translateY(-100px) translateX(var(--end-x)) scale(1.2) rotate(var(--end-rotate));
                opacity: 0;
            }
        }

        /* Delay and properties for different elements to make them appear randomly */
        .stock-element:nth-child(1) { left: 10%; --end-x: 20px; --end-rotate: 10deg; animation-delay: 0s; }
        .stock-element:nth-child(2) { left: 25%; --end-x: -30px; --end-rotate: -15deg; animation-delay: 3s; }
        .stock-element:nth-child(3) { left: 40%; --end-x: 10px; --end-rotate: 5deg; animation-delay: 6s; }
        .stock-element:nth-child(4) { left: 55%; --end-x: -20px; --end-rotate: -10deg; animation-delay: 9s; }
        .stock-element:nth-child(5) { left: 70%; --end-x: 5px; --end-rotate: 8deg; animation-delay: 12s; }
        .stock-element:nth-child(6) { left: 85%; --end-x: -15px; --end-rotate: -5deg; animation-delay: 1s; }
        .stock-element:nth-child(7) { left: 5%; --end-x: 25px; --end-rotate: 20deg; animation-delay: 4s; }
        .stock-element:nth-child(8) { left: 20%; --end-x: -10px; --end-rotate: -8deg; animation-delay: 7s; }
        .stock-element:nth-child(9) { left: 35%; --end-x: 15px; --end-rotate: 12deg; animation-delay: 10s; }
        .stock-element:nth-child(10) { left: 60%; --end-x: -5px; --end-rotate: -2deg; animation-delay: 2s; }


        /* === Right Form Side === */
        .form-side {
            width: 50%;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .form-side h2 {
            font-size: 2.2rem;
            color: var(--primary-color);
            margin-bottom: 40px;
            font-weight: 600;
        }

        .input-group {
            position: relative;
            margin-bottom: 30px;
            width: 100%;
        }

        .input-field {
            width: 100%;
            padding: 10px 0;
            font-size: 1rem;
            border: none;
            border-bottom: 2px solid var(--input-border-color);
            outline: none;
            background: transparent;
            color: var(--text-dark);
            transition: border-color 0.3s;
        }

        .input-label {
            position: absolute;
            top: 10px;
            left: 0;
            font-size: 1rem;
            color: #999;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            border-bottom-color: var(--primary-color);
        }
        
        .input-field:focus + .input-label,
        .input-field:valid + .input-label {
            top: -15px;
            left: 0;
            font-size: 0.8rem;
            color: var(--primary-color);
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 50px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(0, 121, 107, 0.3);
        }

        .login-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px rgba(0, 77, 64, 0.4);
        }

        .error-msg {
            color: #d32f2f;
            margin-top: 15px;
            font-weight: 500;
            height: 20px;
            text-align: center;
        }
        
        /* Responsive Design for mobile */
        @media(max-width: 900px) {
            .login-container {
                flex-direction: column;
                width: 95%;
                height: auto;
                max-width: 480px;
            }
            .vibe-side {
                width: 100%;
                height: 250px;
                justify-content: center;
                text-align: center;
                padding: 30px;
            }
            .form-side {
                width: 100%;
                padding: 40px;
            }
            .vibe-side h1 { font-size: 2.5rem; }
            .vibe-side p { font-size: 1rem; max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        
        <div class="vibe-side">
            <div class="animated-background">
                <div class="stock-element up-arrow">&#8593;</div>
                <div class="stock-element down-arrow">&#8595;</div>
                <div class="stock-element candle"></div>
                <div class="stock-element up-arrow">&#8593;</div>
                <div class="stock-element candle red"></div>
                <div class="stock-element down-arrow">&#8595;</div>
                <div class="stock-element candle"></div>
                <div class="stock-element up-arrow">&#8593;</div>
                <div class="stock-element down-arrow">&#8595;</div>
                <div class="stock-element candle red"></div>
            </div>
            <h1>StockBuddy</h1>
            <p>Track the Market. Seize the Opportunity.</p>
        </div>

        <div class="form-side">
            <h2>Secure Login</h2>
            <form method="post" action="">
                <div class="input-group">
                    <input type="text" id="username" name="username" class="input-field" required>
                    <label for="username" class="input-label">Username</label>
                </div>
                <div class="input-group">
                    <input type="password" id="password" name="password" class="input-field" required>
                    <label for="password" class="input-label">Password</label>
                </div>
                <button type="submit" name="login" class="login-btn">Login</button>
            </form>
            <p class="error-msg"><?php echo $error; ?></p>
        </div>

    </div>
</body>
</html>