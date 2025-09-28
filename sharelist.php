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
    <title>StockBuddy - Share List</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: #e8f5e9;
            margin: 0;
        }

        /* --- NAV CSS UPDATED FOR NEW LAYOUT --- */
        nav {
            background: #00796b;
            padding: 10px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
        }

        /* Centered Navigation Links */
        .nav-links {
            display: flex;
            justify-content: center;
            /* Absolute positioning for perfect centering */
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
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
            background: #004d40;
        }

        /* Right side icons container */
        .nav-right {
            display: flex;
            align-items: center;
            gap: 25px; /* Space between icons */
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

        .menu-toggle {
            display: none; /* Hidden on Desktop */
            background: none;
            border: none;
            color: white;
            font-size: 1.8rem;
            cursor: pointer;
        }

        /* --- MEDIA QUERY FOR RESPONSIVENESS --- */
        @media (max-width: 768px) {
            .nav-links {
                display: none; /* Hide centered links on mobile view */
                flex-direction: column;
                position: absolute;
                top: 100%; /* Position dropdown below nav */
                left: 0;
                width: 100%;
                background-color: #00796b;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transform: none; /* Reset transform for dropdown */
                z-index: 100;
            }

            .nav-links.active {
                display: flex; /* Show dropdown when active */
            }

            .nav-links a {
                width: 100%;
                box-sizing: border-box;
                padding: 15px 40px;
                text-align: center;
                margin: 0;
                border-top: 1px solid #006054;
            }

            .menu-toggle {
                display: block; /* Show hamburger icon */
            }
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            color: #004d40;
        }

        .table-container {
            width: 90%;
            margin: 20px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 18px 22px; text-align: left; border-bottom: 1px solid #eef2f7; }
        th { background: #00796b; color: white; font-size: 0.9rem; text-transform: uppercase; }
        tbody tr { cursor: pointer; transition: background-color 0.2s ease-in-out; }
        tbody tr:hover { background-color: #f1f8f5; }
        .positive { color: #2e7d32; font-weight: bold; }
        .negative { color: #c62828; font-weight: bold; }
        .neutral { color: #757575; font-weight: bold; }
        canvas.sparkline { width: 100px !important; height: 30px !important; }
        .footer-note { text-align: center; font-style: italic; margin: 20px; font-size: 0.8rem; color: #666; }
    </style>
</head>

<body>
    <nav>
        <a href="home.php" class="logo"><i class="fa-solid fa-chart-pie"></i> StockBuddy</a>
        
        <div class="nav-links" id="nav-links">
            <a href="home.php">Home</a>
            <a href="sharelist.php">Share List</a>
            <a href="portfolio.php">Portfolio</a>
            <a href="about.php">About Us</a>
        </div>
<div class="nav-right">
    <button class="menu-toggle" id="menu-toggle" aria-label="Toggle navigation">
        <i class="fa-solid fa-bars"></i>
    </button>
    <a href="logout.php" class="logout-icon" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a>
</div>
    </nav>
    
    <h1>Live Stock Prices</h1>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Stock Name</th>
                    <th>Symbol</th>
                    <th>Price (₹)</th>
                    <th>Change %</th>
                    <th>Trend</th>
                </tr>
            </thead>
            <tbody id="stock-table-body">
                <?php
                include 'db_connect.php';
                $sql = "SELECT stock_name, symbol, base_price FROM stocks";
                $result = $conn->query($sql);
                $stocks_data_for_js = [];
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $stocks_data_for_js[] = $row;
                        $symbol_lower = strtolower($row["symbol"]);
                        echo "<tr onclick=\"window.location.href='stock_details.php?symbol=" . $row["symbol"] . "'\">";
                        echo "<td>" . htmlspecialchars($row["stock_name"]) . "</td>";
                        echo "<td>" . $row["symbol"] . "</td>";
                        echo "<td id='price-" . $symbol_lower . "'>" . $row["base_price"] . "</td>";
                        echo "<td id='change-" . $symbol_lower . "' class='neutral'>0.00%</td>";
                        echo "<td><canvas class='sparkline' id='chart-" . $symbol_lower . "'></canvas></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No stocks found</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
    <p class="footer-note">*All prices are for demonstration purposes only. Click on any stock for more details.</p>
    
    <script>
        // --- SCRIPT FOR RESPONSIVE HAMBURGER MENU ---
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const navLinks = document.getElementById('nav-links');

            if (menuToggle && navLinks) {
                menuToggle.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                });
            }
        });

        // --- EXISTING SCRIPT FOR STOCKS ---
        let charts = {};
        const dataPointLimit = 20;

        function createChart(id, basePrice) {
            const ctx = document.getElementById("chart-" + id).getContext("2d");
            charts[id] = new Chart(ctx, {
                type: "line", data: { labels: Array(dataPointLimit).fill(''), datasets: [{ data: [basePrice], borderColor: "#757575", borderWidth: 2, pointRadius: 0, tension: 0.4 }] },
                options: { plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { display: false } } }
            });
        }

        function updateStock(id, basePrice) {
            const priceEl = document.getElementById("price-" + id);
            const changeEl = document.getElementById("change-" + id);
            if (!priceEl || !changeEl || !charts[id]) return;
            
            const chart = charts[id];
            const oldPrice = parseFloat(priceEl.innerText);
            const newPrice = oldPrice + (Math.random() * (oldPrice * 0.01) - (oldPrice * 0.005));
            const change = ((newPrice - basePrice) / basePrice) * 100;
            
            priceEl.innerText = newPrice.toFixed(2);
            let changeClass = "neutral", changeText = change.toFixed(2) + "%", borderColor = "#757575";
            if (change > 0) { changeClass = "positive"; changeText = "+" + change.toFixed(2) + "%"; borderColor = "#2e7d32"; } 
            else if (change < 0) { changeClass = "negative"; borderColor = "#c62828"; }
            
            changeEl.innerText = changeText;
            changeEl.className = changeClass;
            
            const chartData = chart.data.datasets[0].data;
            chartData.push(newPrice);
            if (chartData.length > dataPointLimit) chartData.shift();
            
            chart.data.datasets[0].borderColor = borderColor;
            chart.update('none');
        }
        
        <?php
        foreach ($stocks_data_for_js as $stock) {
            $symbol_lower = strtolower($stock["symbol"]);
            $base_price = $stock["base_price"];
            echo "createChart('{$symbol_lower}', {$base_price});\n";
            echo "setInterval(() => updateStock('{$symbol_lower}', {$base_price}), 2000);\n";
        }
        ?>
    </script>
</body>

</html>