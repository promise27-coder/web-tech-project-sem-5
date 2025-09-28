<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['symbol'])) {
    header("Location: sharelist.php");
    exit();
}

include 'db_connect.php';
$symbol = $_GET['symbol'];

// <<< AA CODE MISSING HATO, HAVE ADD KARI DIDHO CHHE >>>
// Fetch stock details from DB
$stmt = $conn->prepare("SELECT stock_name, symbol, base_price FROM stocks WHERE symbol = ?");
$stmt->bind_param("s", $symbol);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    // If stock not found, redirect back
    header("Location: sharelist.php");
    exit();
}
$stock = $result->fetch_assoc();
$stmt->close();
$conn->close();
// <<< AHIN SUDHI NO CODE MISSING HATO >>>


// --- DEMO DATA GENERATION FOR MULTIPLE TIMEFRAMES ---
$base_price = $stock['base_price'];
$datasets = [];
$timeframes = ['1D' => 24, '5D' => 120, '1M' => 30, 'All' => 90]; // Points for each timeframe

foreach ($timeframes as $frame => $points) {
    $data = [];
    $price_point = $base_price * (1 + (rand(-1000, 1000) / 10000));
    for ($i = 0; $i < $points; $i++) {
        $data[] = round($price_point, 2);
        $price_point *= (1 + (rand(-150, 150) / (($frame === '1D') ? 20000 : 10000))); // 1D mate ochhi volatility
    }
    $datasets[$frame] = $data;
}

$current_price = end($datasets['All']);
$change_percent = (($current_price - $base_price) / $base_price) * 100;
$change_value = $current_price - $base_price;

// Biji details mate demo data
$open = $base_price * (1 + (rand(-100, 100) / 10000));
$high = max($open, $current_price) * (1 + (rand(0, 50) / 10000));
$low = min($open, $current_price) * (1 - (rand(0, 50) / 10000));
$market_cap = $base_price * rand(500000, 15000000);
$volume = rand(10000000, 150000000);
$pe_ratio = rand(1500, 8000) / 100;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($stock['stock_name']); ?> Details - StockBuddy</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        :root {
            --primary-color: #00796b;
            --secondary-color: #004d40;
            --positive: #2e7d32;
            --negative: #c62828;
            --buy: #2e7d32;
            --sell: #c62828;
        }

        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            color: #333;
        }

        nav {
            background: var(--primary-color);
            padding: 13px 15px;
            display: flex;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        nav a {
            color: white;
            margin: 5px 15px;
            text-decoration: none;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background 0.3s;
        }

        nav a:hover {
            background: var(--secondary-color);
        }

        nav {
            position: relative;
            justify-content: center;
        }

        .logout-icon {
            position: absolute;
            right: 40px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.6rem;
            transition: transform 0.2s ease;
        }

        .logout-icon:hover {
            transform: translateY(-50%) scale(1.1);
            background: none;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .stock-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .stock-header h1 {
            margin: 0;
            font-size: 2.5rem;
            line-height: 1.2;
        }

        .stock-header p {
            margin: 5px 0 0;
            color: #777;
            font-size: 1rem;
        }

        .price-info {
            text-align: right;
            flex-shrink: 0;
        }

        .price-info .current-price {
            font-size: 2.5rem;
            font-weight: 700;
        }

        .price-info .change {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .positive {
            color: var(--positive);
        }

        .negative {
            color: var(--negative);
        }

        .action-buttons {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }

        .action-buttons button {
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }

        .action-buttons button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .action-buttons button:active {
            transform: translateY(0);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }

        .btn-buy {
            background: linear-gradient(45deg, #2e7d32, #4caf50);
        }

        .btn-sell {
            background: linear-gradient(45deg, #c62828, #f44336);
        }

        .action-buttons button i {
            margin-right: 8px;
        }

        .chart-controls {
            margin-bottom: 15px;
        }

        .timeframe-btn {
            background: #e0e0e0;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            margin-right: 5px;
            transition: background-color 0.2s;
        }

        .timeframe-btn:hover {
            background: #ccc;
        }

        .timeframe-btn.active {
            background: var(--primary-color);
            color: white;
        }

        #chart-container {
            height: 400px;
            margin-bottom: 30px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }

        .detail-item {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .detail-item .label {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .detail-item .value {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #fefefe;
            padding: 25px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .modal-content h3 {
            margin-top: 0;
        }

        .modal-content input {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        #toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 2000;
        }

        .toast {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            color: white;
            min-width: 250px;
            font-weight: 500;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .toast.success {
            background-color: var(--positive);
        }

        .toast.error {
            background-color: var(--negative);
        }
    </style>
</head>

<body>
    <div id="toast-container"></div>
    <nav>
        <a href="home.php">Home</a>
        <a href="sharelist.php">Share List</a>
        <a href="portfolio.php">Portfolio</a>
        <a href="about.php">About Us</a>
        <a href="logout.php" class="logout-icon" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a>
    </nav>

    <div class="container">
        <div class="stock-header">
            <div>
                <h1><?php echo htmlspecialchars($stock['stock_name']); ?></h1>
                <p><?php echo $stock['symbol']; ?> - Demo Market Data</p>
                <div class="action-buttons">
                    <button class="btn-buy">Buy</button>
                    <button class="btn-sell">Sell</button>
                </div>
            </div>
            <div class="price-info">
                <div class="current-price" id="current-price">₹<?php echo number_format($current_price, 2); ?></div>
                <div class="change <?php echo ($change_percent >= 0) ? 'positive' : 'negative'; ?>" id="price-change">
                    <?php echo ($change_value >= 0 ? '+' : '') . number_format($change_value, 2); ?>
                    (<?php echo number_format($change_percent, 2); ?>%)
                </div>
            </div>
        </div>
        <div class="chart-controls">
            <button class="timeframe-btn" data-frame="1D">1D</button>
            <button class="timeframe-btn" data-frame="5D">5D</button>
            <button class="timeframe-btn" data-frame="1M">1M</button>
            <button class="timeframe-btn active" data-frame="All">All</button>
        </div>
        <div id="chart-container">
            <canvas id="stock-chart"></canvas>
        </div>
        <div class="details-grid">
            <div class="detail-item">
                <div class="label">Open</div>
                <div class="value">₹<?php echo number_format($open, 2); ?></div>
            </div>
            <div class="detail-item">
                <div class="label">High</div>
                <div class="value">₹<?php echo number_format($high, 2); ?></div>
            </div>
            <div class="detail-item">
                <div class="label">Low</div>
                <div class="value">₹<?php echo number_format($low, 2); ?></div>
            </div>
            <div class="detail-item">
                <div class="label">Prev. Close</div>
                <div class="value">₹<?php echo number_format($base_price, 2); ?></div>
            </div>
            <div class="detail-item">
                <div class="label">Volume</div>
                <div class="value"><?php echo number_format($volume); ?></div>
            </div>
            <div class="detail-item">
                <div class="label">Market Cap</div>
                <div class="value">₹<?php echo number_format($market_cap / 10000000, 2); ?> Cr</div>
            </div>
            <div class="detail-item">
                <div class="label">P/E Ratio</div>
                <div class="value"><?php echo number_format($pe_ratio, 2); ?></div>
            </div>
        </div>
    </div>

    <div id="transaction-modal" class="modal">
        <div class="modal-content">
            <h3 id="modal-title">Buy Stock</h3>
            <p>Current Price: <strong id="modal-price"></strong></p>
            <input type="number" id="quantity-input" placeholder="Enter Quantity" min="1">
            <div class="modal-buttons">
                <button id="confirm-transaction-btn" class="btn-buy">Confirm</button>
                <button id="cancel-transaction-btn" style="background-color:#6c757d;">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('stock-chart').getContext('2d');
        const allChartData = <?php echo json_encode($datasets); ?>;
        const basePrice = <?php echo $base_price; ?>;
        const stockSymbol = '<?php echo $stock['symbol']; ?>';
        let currentPrice = <?php echo $current_price; ?>;

        const stockChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: Array(allChartData['All'].length).fill(''),
                datasets: [{
                    label: stockSymbol + ' Price',
                    data: allChartData['All'],
                    borderColor: '<?php echo ($change_percent >= 0) ? "rgba(46, 125, 50, 1)" : "rgba(198, 40, 40, 1)"; ?>',
                    backgroundColor: '<?php echo ($change_percent >= 0) ? "rgba(46, 125, 50, 0.1)" : "rgba(198, 40, 40, 0.1)"; ?>',
                    borderWidth: 2,
                    pointRadius: 0,
                    tension: 0.2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: false } }
            }
        });

        // REAL-TIME CHART UPDATE SCRIPT
        setInterval(function () {
            const changeFactor = (Math.random() - 0.5) * 0.005;
            currentPrice = currentPrice * (1 + changeFactor);

            const priceChange = currentPrice - basePrice;
            const percentChange = (priceChange / basePrice) * 100;
            document.getElementById('current-price').innerText = '₹' + currentPrice.toFixed(2);
            const changeEl = document.getElementById('price-change');
            changeEl.innerText = (priceChange >= 0 ? '+' : '') + priceChange.toFixed(2) + ` (${percentChange.toFixed(2)}%)`;
            changeEl.className = 'change ' + (priceChange >= 0 ? 'positive' : 'negative');

            const activeTimeframe = document.querySelector('.timeframe-btn.active').dataset.frame;
            if (activeTimeframe === '1D') {
                stockChart.data.datasets[0].data.push(currentPrice);
                stockChart.data.labels.push('');
                if (stockChart.data.datasets[0].data.length > 24) {
                    stockChart.data.datasets[0].data.shift();
                    stockChart.data.labels.shift();
                }
                stockChart.update('none');
            }
        }, 2000);

        // TIME-FRAME BUTTONS SCRIPT
        const timeframeButtons = document.querySelectorAll('.timeframe-btn');
        timeframeButtons.forEach(button => {
            button.addEventListener('click', function () {
                timeframeButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const frame = this.dataset.frame;
                const newData = allChartData[frame];
                stockChart.data.datasets[0].data = newData;
                stockChart.data.labels = Array(newData.length).fill('');
                stockChart.update('easeInOutQuad');
            });
        });

        // MODAL and TRANSACTION SCRIPT
        const modal = document.getElementById('transaction-modal');
        const buyBtn = document.querySelector('.btn-buy');
        const sellBtn = document.querySelector('.btn-sell');
        const confirmBtn = document.getElementById('confirm-transaction-btn');
        let currentAction = '';

        function openModal(action) {
            currentAction = action;
            document.getElementById('modal-title').innerText = action.charAt(0).toUpperCase() + action.slice(1) + ' ' + stockSymbol;
            document.getElementById('modal-price').innerText = '₹' + currentPrice.toFixed(2);
            confirmBtn.className = (action === 'buy') ? 'btn-buy' : 'btn-sell';
            modal.style.display = 'flex';
        }

        buyBtn.onclick = () => openModal('buy');
        sellBtn.onclick = () => openModal('sell');
        document.getElementById('cancel-transaction-btn').onclick = () => modal.style.display = 'none';
        window.onclick = (event) => { if (event.target == modal) { modal.style.display = 'none'; } }

        confirmBtn.onclick = function () {
            const quantity = document.getElementById('quantity-input').value;
            if (!quantity || quantity <= 0) {
                showToast('Please enter a valid quantity.', 'error');
                return;
            }
            const formData = new FormData();
            formData.append('action', currentAction);
            formData.append('symbol', stockSymbol);
            formData.append('quantity', quantity);
            formData.append('price', currentPrice);

            fetch('transaction.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    showToast(data.message, data.status);
                    modal.style.display = 'none';
                })
                .catch(err => showToast('An error occurred.', 'error'));
        };

        function showToast(message, type) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerText = message;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    </script>
</body>

</html>