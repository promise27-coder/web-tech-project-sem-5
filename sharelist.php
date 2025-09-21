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
            <tr>
                <td>Infosys</td>
                <td>INFY</td>
                <td id="price-infy">1500</td>
                <td id="change-infy" class="positive">+0.50%</td>
                <td><canvas id="chart-infy"></canvas></td>
            </tr>
            <tr>
                <td>ICICI Bank</td>
                <td>ICICIBANK</td>
                <td id="price-icici">950</td>
                <td id="change-icici" class="negative">-0.10%</td>
                <td><canvas id="chart-icici"></canvas></td>
            </tr>
            <tr>
                <td>Hindustan Unilever</td>
                <td>HINDUNILVR</td>
                <td id="price-hul">2550</td>
                <td id="change-hul" class="positive">+0.20%</td>
                <td><canvas id="chart-hul"></canvas></td>
            </tr>
            <tr>
                <td>State Bank of India</td>
                <td>SBIN</td>
                <td id="price-sbin">570</td>
                <td id="change-sbin" class="positive">+1.10%</td>
                <td><canvas id="chart-sbin"></canvas></td>
            </tr>
            <tr>
                <td>Bharti Airtel</td>
                <td>BHARTIARTL</td>
                <td id="price-airtel">880</td>
                <td id="change-airtel" class="negative">-0.45%</td>
                <td><canvas id="chart-airtel"></canvas></td>
            </tr>
            <tr>
                <td>ITC</td>
                <td>ITC</td>
                <td id="price-itc">440</td>
                <td id="change-itc" class="neutral">+0.00%</td>
                <td><canvas id="chart-itc"></canvas></td>
            </tr>
            <tr>
                <td>Larsen & Toubro</td>
                <td>LT</td>
                <td id="price-lt">2700</td>
                <td id="change-lt" class="positive">+0.75%</td>
                <td><canvas id="chart-lt"></canvas></td>
            </tr>
            <tr>
                <td>Bajaj Finance</td>
                <td>BAJFINANCE</td>
                <td id="price-bajaj">7200</td>
                <td id="change-bajaj" class="negative">-0.80%</td>
                <td><canvas id="chart-bajaj"></canvas></td>
            </tr>
            <tr>
                <td>Asian Paints</td>
                <td>ASIANPAINT</td>
                <td id="price-asian">3100</td>
                <td id="change-asian" class="positive">+0.30%</td>
                <td><canvas id="chart-asian"></canvas></td>
            </tr>
             <tr>
                <td>Tata Motors</td>
                <td>TATAMOTORS</td>
                <td id="price-tata">620</td>
                <td id="change-tata" class="negative">-1.20%</td>
                <td><canvas id="chart-tata"></canvas></td>
            </tr>
             <tr>
                <td>Wipro</td>
                <td>WIPRO</td>
                <td id="price-wipro">410</td>
                <td id="change-wipro" class="positive">+0.40%</td>
                <td><canvas id="chart-wipro"></canvas></td>
            </tr>
            <tr>
                <td>Axis Bank</td>
                <td>AXISBANK</td>
                <td id="price-axis">990</td>
                <td id="change-axis" class="negative">-0.15%</td>
                <td><canvas id="chart-axis"></canvas></td>
            </tr>
            <tr>
                <td>Kotak Mahindra Bank</td>
                <td>KOTAKBANK</td>
                <td id="price-kotak">1780</td>
                <td id="change-kotak" class="positive">+0.55%</td>
                <td><canvas id="chart-kotak"></canvas></td>
            </tr>
            <tr>
                <td>Maruti Suzuki</td>
                <td>MARUTI</td>
                <td id="price-maruti">10200</td>
                <td id="change-maruti" class="positive">+0.90%</td>
                <td><canvas id="chart-maruti"></canvas></td>
            </tr>
            <tr>
                <td>Sun Pharma</td>
                <td>SUNPHARMA</td>
                <td id="price-sun">1150</td>
                <td id="change-sun" class="negative">-0.25%</td>
                <td><canvas id="chart-sun"></canvas></td>
            </tr>
            <tr>
                <td>Titan Company</td>
                <td>TITAN</td>
                <td id="price-titan">3300</td>
                <td id="change-titan" class="positive">+1.50%</td>
                <td><canvas id="chart-titan"></canvas></td>
            </tr>
            <tr>
                <td>Nestle India</td>
                <td>NESTLEIND</td>
                <td id="price-nestle">24000</td>
                <td id="change-nestle" class="neutral">+0.05%</td>
                <td><canvas id="chart-nestle"></canvas></td>
            </tr>
            <tr>
                <td>Power Grid Corp</td>
                <td>POWERGRID</td>
                <td id="price-power">245</td>
                <td id="change-power" class="positive">+0.60%</td>
                <td><canvas id="chart-power"></canvas></td>
            </tr>
            <tr>
                <td>ONGC</td>
                <td>ONGC</td>
                <td id="price-ongc">200</td>
                <td id="change-ongc" class="negative">-0.95%</td>
                <td><canvas id="chart-ongc"></canvas></td>
            </tr>
            <tr>
                <td>Adani Ports</td>
                <td>ADANIPORTS</td>
                <td id="price-adani">800</td>
                <td id="change-adani" class="positive">+2.10%</td>
                <td><canvas id="chart-adani"></canvas></td>
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
        
        // --- Original charts ---
        createChart("reli", 2800);
        createChart("tcs", 3450);
        createChart("hdfc", 1650);

        // --- First batch of new charts ---
        createChart("infy", 1500);
        createChart("icici", 950);
        createChart("hul", 2550);
        createChart("sbin", 570);
        createChart("airtel", 880);
        createChart("itc", 440);
        createChart("lt", 2700);
        createChart("bajaj", 7200);
        createChart("asian", 3100);
        createChart("tata", 620);

        // --- Second batch of new charts ---
        createChart("wipro", 410);
        createChart("axis", 990);
        createChart("kotak", 1780);
        createChart("maruti", 10200);
        createChart("sun", 1150);
        createChart("titan", 3300);
        createChart("nestle", 24000);
        createChart("power", 245);
        createChart("ongc", 200);
        createChart("adani", 800);

        // --- Original intervals ---
        setInterval(() => updateStock("reli", 2800), 1000);
        setInterval(() => updateStock("tcs", 3450), 1000);
        setInterval(() => updateStock("hdfc", 1650), 1000);

        // --- First batch of new intervals ---
        setInterval(() => updateStock("infy", 1500), 1000);
        setInterval(() => updateStock("icici", 950), 1000);
        setInterval(() => updateStock("hul", 2550), 1000);
        setInterval(() => updateStock("sbin", 570), 1000);
        setInterval(() => updateStock("airtel", 880), 1000);
        setInterval(() => updateStock("itc", 440), 1000);
        setInterval(() => updateStock("lt", 2700), 1000);
        setInterval(() => updateStock("bajaj", 7200), 1000);
        setInterval(() => updateStock("asian", 3100), 1000);
        setInterval(() => updateStock("tata", 620), 1000);
        
        // --- Second batch of new intervals ---
        setInterval(() => updateStock("wipro", 410), 1000);
        setInterval(() => updateStock("axis", 990), 1000);
        setInterval(() => updateStock("kotak", 1780), 1000);
        setInterval(() => updateStock("maruti", 10200), 1000);
        setInterval(() => updateStock("sun", 1150), 1000);
        setInterval(() => updateStock("titan", 3300), 1000);
        setInterval(() => updateStock("nestle", 24000), 1000);
        setInterval(() => updateStock("power", 245), 1000);
        setInterval(() => updateStock("ongc", 200), 1000);
        setInterval(() => updateStock("adani", 800), 1000);
    </script>
</body>
</html>