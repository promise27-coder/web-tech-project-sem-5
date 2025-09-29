<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

// 1. Get user ID safely
$user_id = null;
$user_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$user_stmt->bind_param("s", $_SESSION['user']);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
if ($user_row = $user_result->fetch_assoc()) {
    $user_id = $user_row['id'];
}
$user_stmt->close();

// If for some reason user is not found, exit gracefully
if ($user_id === null) {
    die("Error: Could not validate user session.");
}

// 2. Fetch portfolio data using a standard while loop
$portfolio_data = []; // Use this array to store data
$sql = "SELECT p.stock_symbol, p.quantity, p.purchase_price, s.stock_name, s.base_price 
        FROM portfolio p 
        JOIN stocks s ON p.stock_symbol = s.symbol 
        WHERE p.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $portfolio_data[] = $row;
}
$stmt->close();


// 3. Perform calculations on the fetched data
$total_investment = 0;
$current_total_value = 0;
$portfolio_for_js = [];

foreach ($portfolio_data as $row) {
    $investment = $row['quantity'] * $row['purchase_price'];
    $current_value = $row['quantity'] * $row['base_price'];
    $total_investment += $investment;
    $current_total_value += $current_value;
    $portfolio_for_js[] = [
        'symbol' => $row['stock_symbol'],
        'quantity' => $row['quantity'],
        'purchase_price' => $row['purchase_price'],
        'base_price' => $row['base_price']
    ];
}
$overall_pnl = $current_total_value - $total_investment;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Portfolio - StockBuddy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
        }
        
        nav {
            background: var(--primary-color);
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
        
        .nav-links {
            display: flex;
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
            background: var(--secondary-color);
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
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

        .hamburger-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.8rem;
            cursor: pointer;
        }
        
        @media screen and (max-width: 768px) {
            .nav-links {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background-color: var(--primary-color);
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                transform: none; 
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links a {
                padding: 15px;
                width: 100%;
                text-align: center;
                border-bottom: 1px solid var(--secondary-color);
            }

            .hamburger-btn {
                display: block;
            }
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .portfolio-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
        }

        .summary-card .label {
            font-size: 1rem;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-card .value {
            font-size: 2rem;
            font-weight: 600;
        }

        .table-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 18px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        th {
            background-color: #f9fafb;
            font-weight: 600;
        }

        .positive {
            color: var(--positive);
        }

        .negative {
            color: var(--negative);
        }

        .action-buttons button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: opacity 0.3s;
            font-size: 0.9rem;
        }

        .btn-buy {
            background-color: var(--buy);
        }

        .btn-sell {
            background-color: var(--sell);
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

        .no-holdings {
            text-align: center;
            padding: 50px;
            font-size: 1.2rem;
            color: #888;
        }
    </style>
</head>

<body>
    <div id="toast-container"></div>
    <nav>
        <a href="home.php" class="logo"><i class="fa-solid fa-chart-pie"></i> StockBuddy</a>
        
        <div class="nav-links" id="nav-links-container">
            <a href="home.php">Home</a>
            <a href="sharelist.php">Share List</a>
            <a href="portfolio.php">Portfolio</a>
            <a href="about.php">About Us</a>
        </div>
        
        <div class="nav-right">
             <button class="hamburger-btn" id="hamburger-btn">
                <i class="fa-solid fa-bars"></i>
            </button>
            <a href="logout.php" class="logout-icon" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a>
        </div>
    </nav>

    <div class="container">
        <h1>My Portfolio</h1>
        <div class="portfolio-summary">
            <div class="summary-card">
                <div class="label">Total Investment</div>
                <div class="value">₹<?php echo number_format($total_investment, 2); ?></div>
            </div>
            <div class="summary-card">
                <div class="label">Current Value</div>
                <div class="value" id="current-value-total">₹<?php echo number_format($current_total_value, 2); ?></div>
            </div>
            <div class="summary-card">
                <div class="label">Overall Profit & Loss</div>
                <div class="value <?php echo ($overall_pnl >= 0) ? 'positive' : 'negative'; ?>" id="overall-pnl">
                    ₹<?php echo number_format($overall_pnl, 2); ?>
                </div>
            </div>
        </div>
        <div class="table-container">
            <?php if (count($portfolio_data) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Stock</th>
                            <th>Quantity</th>
                            <th>Avg. Buy Price</th>
                            <th>Current Price</th>
                            <th>Total Value</th>
                            <th>Profit / Loss</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="portfolio-body">
                        <?php foreach ($portfolio_data as $row):
                            $investment = $row['quantity'] * $row['purchase_price'];
                            $current_value = $row['quantity'] * $row['base_price'];
                            $pnl = $current_value - $investment;
                            ?>
                            <tr id="row-<?php echo $row['stock_symbol']; ?>" data-symbol="<?php echo $row['stock_symbol']; ?>"
                                data-quantity="<?php echo $row['quantity']; ?>"
                                data-purchase-price="<?php echo $row['purchase_price']; ?>">
                                <td><?php echo htmlspecialchars($row['stock_name']); ?> (<?php echo $row['stock_symbol']; ?>)
                                </td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td>₹<?php echo number_format($row['purchase_price'], 2); ?></td>
                                <td id="price-<?php echo $row['stock_symbol']; ?>">
                                    ₹<?php echo number_format($row['base_price'], 2); ?></td>
                                <td id="value-<?php echo $row['stock_symbol']; ?>">
                                    ₹<?php echo number_format($current_value, 2); ?></td>
                                <td id="pnl-<?php echo $row['stock_symbol']; ?>"
                                    class="<?php echo ($pnl >= 0) ? 'positive' : 'negative'; ?>">
                                    ₹<?php echo number_format($pnl, 2); ?></td>
                                <td class="action-buttons">
                                    <button class="btn-buy"
                                        onclick="openModal('buy', '<?php echo $row['stock_symbol']; ?>')">Buy</button>
                                    <button class="btn-sell"
                                        onclick="openModal('sell', '<?php echo $row['stock_symbol']; ?>')">Sell</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-holdings">You do not have any holdings in your portfolio.</div>
            <?php endif; ?>
        </div>
    </div>

    <div id="transaction-modal" class="modal">
        <div class="modal-content">
            <h3 id="modal-title">Transaction</h3>
            <p>Current Price: <strong id="modal-price"></strong></p>
            <input type="number" id="quantity-input" placeholder="Enter Quantity" min="1">
            <div class="modal-buttons">
                <button id="confirm-transaction-btn">Confirm</button>
                <button id="cancel-transaction-btn" style="background-color:#6c757d;">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        const portfolio = <?php echo json_encode($portfolio_for_js); ?>;
        const totalInvestment = <?php echo $total_investment; ?>;

        function updatePortfolioPrices() {
            let newTotalValue = 0;
            portfolio.forEach(stock => {
                const priceEl = document.getElementById(`price-${stock.symbol}`);
                if (!priceEl) return;

                let currentPrice = parseFloat(priceEl.innerText.replace('₹', '').replace(/,/g, ''));
                const change = (Math.random() - 0.5) * 0.01;
                currentPrice *= (1 + change);

                const quantity = parseFloat(document.getElementById(`row-${stock.symbol}`).dataset.quantity);
                const purchasePrice = parseFloat(document.getElementById(`row-${stock.symbol}`).dataset.purchasePrice);

                const totalValue = currentPrice * quantity;
                const pnl = totalValue - (purchasePrice * quantity);

                priceEl.innerText = '₹' + currentPrice.toFixed(2);
                document.getElementById(`value-${stock.symbol}`).innerText = '₹' + totalValue.toFixed(2);

                const pnlEl = document.getElementById(`pnl-${stock.symbol}`);
                pnlEl.innerText = '₹' + pnl.toFixed(2);
                pnlEl.className = pnl >= 0 ? 'positive' : 'negative';
                newTotalValue += totalValue;
            });

            document.getElementById('current-value-total').innerText = '₹' + newTotalValue.toFixed(2);

            const overallPnl = newTotalValue - totalInvestment;
            const overallPnlEl = document.getElementById('overall-pnl');
            overallPnlEl.innerText = '₹' + overallPnl.toFixed(2);
            overallPnlEl.className = 'value ' + (overallPnl >= 0 ? 'positive' : 'negative');
        }

        if (portfolio.length > 0) {
            setInterval(updatePortfolioPrices, 3000);
        }

        const modal = document.getElementById('transaction-modal');
        const confirmBtn = document.getElementById('confirm-transaction-btn');
        let currentAction = '';
        let currentSymbol = '';

        function openModal(action, symbol) {
            currentAction = action;
            currentSymbol = symbol;
            const currentPriceText = document.getElementById(`price-${symbol}`).innerText;

            document.getElementById('modal-title').innerText = `${action.charAt(0).toUpperCase() + action.slice(1)} ${symbol}`;
            document.getElementById('modal-price').innerText = currentPriceText;
            confirmBtn.className = (action === 'buy') ? 'btn-buy' : 'btn-sell';
            modal.style.display = 'flex';
        }

        document.getElementById('cancel-transaction-btn').onclick = () => modal.style.display = 'none';
        window.onclick = (event) => { if (event.target == modal) { modal.style.display = 'none'; } }

        confirmBtn.onclick = function () {
            const quantity = document.getElementById('quantity-input').value;
            const currentPrice = parseFloat(document.getElementById(`price-${currentSymbol}`).innerText.replace('₹', '').replace(/,/g, ''));

            if (!quantity || quantity <= 0) {
                showToast('Please enter a valid quantity.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('action', currentAction);
            formData.append('symbol', currentSymbol);
            formData.append('quantity', quantity);
            formData.append('price', currentPrice);

            fetch('transaction.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    showToast(data.message, data.status);
                    modal.style.display = 'none';
                    if (data.status === 'success') {
                        setTimeout(() => location.reload(), 1500);
                    }
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

        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerBtn = document.getElementById('hamburger-btn');
            const navLinks = document.getElementById('nav-links-container');

            hamburgerBtn.addEventListener('click', function() {
                navLinks.classList.toggle('active');
            });
        });
    </script>
</body>

</html>