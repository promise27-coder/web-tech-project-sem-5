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
    <style>
        body { font-family: Arial, sans-serif; background: #e8f5e9; margin: 0; }
        nav {
            background: #00796b;
            padding: 13px 15px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
        nav a {
            color: white;
            margin: 5px 10px;
            text-decoration: none;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background 0.3s;
        }
        nav a:hover { background: #004d40; }
        h1 { text-align: center; margin-top: 20px; font-size: 1.8rem; }
        .table-container {
            width: 90%;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 500px;
        }
        th, td {
            padding: 22px;
            text-align: left;
            font-size: 0.95rem;
            vertical-align: middle;
        }
        th { background: #00796b; color: white; }
        tr:nth-child(even) { background: #f1f8f5; }
        .positive { color: green; font-weight: bold; }
        .negative { color: red; font-weight: bold; }
        .neutral { color: #fbc02d; font-weight: bold; }
        canvas { width: 100px !important; height: 30px !important; }
        p { text-align: center; font-style: italic; margin: 20px; font-size: 8px; }
        
        @media(max-width: 768px) {
            nav { flex-direction: column; align-items: center; }
            h1 { font-size: 1.5rem; }
            th, td { padding: 8px; font-size: 0.85rem; }
            .sparkline { width: 50px; height: 18px; }
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
    <h1>Live Stock Prices</h1>

    <div class="table-container">
        <table id="stockTable">
            <tr>
                <th>Stock Name</th>
                <th>Symbol</th>
                <th>Price (₹)</th>
                <th>Change %</th>
                <th>Trend</th>
            </tr>
            <tr>
                <td>Reliance</td>
                <td>RELI</td>
                <td id="price-reli">2800</td>
                <td id="change-reli" class="positive">+0.85%</td>
                <td><canvas id="chart-reli"></canvas></td>
            </tr>
            <tr>
                <td>TCS</td>
                <td>TCS</td>
                <td id="price-tcs">3450</td>
                <td id="change-tcs" class="negative">-0.25%</td>
                <td><canvas id="chart-tcs"></canvas></td>
            </tr>
            <tr>
                <td>HDFC Bank</td>
                <td>HDFCBANK</td>
                <td id="price-hdfc">1650</td>
                <td id="change-hdfc" class="positive">+0.15%</td>
                <td><canvas id="chart-hdfc"></canvas></td>
            </tr>
        </table>
    </div>

    <p>*Prices shown are demo only*</p>

    <script>
        let charts = {};

        function createChart(id, basePrice) {
            let ctx = document.getElementById("chart-" + id).getContext("2d");
            charts[id] = new Chart(ctx, {
                type: "line",
                data: {
                    labels: [""],
                    datasets: [{
                        label: id,
                        data: [basePrice],
                        borderColor: "#0288d1",
                        borderWidth: 2,
                        fill: false,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { x: { display: false }, y: { display: false } }
                }
            });
        }

        function updateStock(id, basePrice) {
            let priceEl = document.getElementById("price-" + id);
            let changeEl = document.getElementById("change-" + id);
            let oldPrice = parseFloat(priceEl.innerText);

            let newPrice = oldPrice + (Math.random() * 20 - 10);
            let change = ((newPrice - basePrice) / basePrice * 100).toFixed(2);
            
            priceEl.innerText = newPrice.toFixed(2);
            if (change > 0) {
                changeEl.innerText = "+" + change + "%";
                changeEl.className = "positive";
            } else if (change < 0) {
                changeEl.innerText = change + "%";
                changeEl.className = "negative";
            } else {
                changeEl.innerText = change + "%";
                changeEl.className = "neutral";
            }
            
            let lineColor = "#fbc02d";
            if (change > 0) {
                lineColor = "green";
            } else if (change < 0) {
                lineColor = "red";
            }
            charts[id].data.datasets[0].borderColor = lineColor;
            
            charts[id].data.labels.push("");
            charts[id].data.datasets[0].data.push(newPrice);
            charts[id].update();
        }
        
        createChart("reli", 2800);
        createChart("tcs", 3450);
        createChart("hdfc", 1650);

        setInterval(() => updateStock("reli", 2800), 1000);
        setInterval(() => updateStock("tcs", 3450), 1000);
        setInterval(() => updateStock("hdfc", 1650), 1000);
    </script>
</body>
</html>
