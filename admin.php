<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "stockbuddy_db");
if ($conn->connect_error) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
        exit();
    }
    die("DB connection error: " . $conn->connect_error);
}

if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $action = $_GET['action'];
    $response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

    try {
        if ($action === 'add_user') {
            $user = trim($_POST['username']);
            $pass = trim($_POST['password']);
            if (empty($user) || empty($pass))
                throw new Exception("Username and password are required.");
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->bind_param("s", $user);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0)
                throw new Exception("Username '$user' already exists.");
            $stmt->close();
            $pass_hashed = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $user, $pass_hashed);
            $stmt->execute();
            $stmt->close();
            $response = ['status' => 'success', 'message' => "User '$user' added successfully!"];
        } elseif ($action === 'update_user') {
            $id = $_POST['id'];
            $user = trim($_POST['username']);
            $pass = trim($_POST['password']);
            if (empty($user))
                throw new Exception("Username cannot be empty.");
            if (!empty($pass)) {
                $pass_hashed = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET username=?, password=? WHERE id=?");
                $stmt->bind_param("ssi", $user, $pass_hashed, $id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET username=? WHERE id=?");
                $stmt->bind_param("si", $user, $id);
            }
            $stmt->execute();
            $stmt->close();
            $response = ['status' => 'success', 'message' => "User '$user' updated successfully."];
        } elseif ($action === 'delete_user') {
            $id = $_POST['id'];
            $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            if ($result && $result['username'] === 'admin')
                throw new Exception("Cannot delete the primary admin account.");
            $stmt->close();
            $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            $response = ['status' => 'success', 'message' => 'User deleted successfully.'];
        }
        elseif ($action === 'add_stock') {
            $name = trim($_POST['stock_name']);
            $symbol = strtoupper(trim($_POST['symbol']));
            $price = $_POST['base_price'];
            if (empty($name) || empty($symbol) || !isset($price))
                throw new Exception("All stock fields are required.");
            $stmt = $conn->prepare("SELECT id FROM stocks WHERE symbol = ?");
            $stmt->bind_param("s", $symbol);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0)
                throw new Exception("Stock symbol '$symbol' already exists.");
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO stocks (stock_name, symbol, base_price) VALUES (?, ?, ?)");
            $stmt->bind_param("ssd", $name, $symbol, $price);
            $stmt->execute();
            $stmt->close();
            $response = ['status' => 'success', 'message' => "Stock '$name' added successfully!"];
        } elseif ($action === 'update_stock') {
            $id = $_POST['id'];
            $name = trim($_POST['stock_name']);
            $symbol = strtoupper(trim($_POST['symbol']));
            if (empty($name) || empty($symbol))
                throw new Exception("Stock name and symbol are required.");

            $stmt = $conn->prepare("UPDATE stocks SET stock_name=?, symbol=? WHERE id=?");
            $stmt->bind_param("ssi", $name, $symbol, $id);
            $stmt->execute();
            $stmt->close();
            $response = ['status' => 'success', 'message' => "Stock '$name' updated successfully."];
        } elseif ($action === 'delete_stock') {
            $id = $_POST['id'];
            $stmt = $conn->prepare("DELETE FROM stocks WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            $response = ['status' => 'success', 'message' => 'Stock deleted successfully.'];
        } elseif ($action === 'delete_feedback') {
            $id = $_POST['id'];
            if (empty($id))
                throw new Exception("Feedback ID is missing.");

            $stmt = $conn->prepare("DELETE FROM feedback WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            $response = ['status' => 'success', 'message' => 'Feedback deleted successfully.'];
        }

    } catch (Exception $e) {
        $response = ['status' => 'error', 'message' => $e->getMessage()];
    }

    echo json_encode($response);
    exit();
}

$user_count = $conn->query("SELECT COUNT(id) as count FROM users")->fetch_assoc()['count'];
$stock_count = $conn->query("SELECT COUNT(id) as count FROM stocks")->fetch_assoc()['count'];
$feedback_count = $conn->query("SELECT COUNT(id) as count FROM feedback")->fetch_assoc()['count'];
$users = $conn->query("SELECT * FROM users ORDER BY username ASC");
$stocks = $conn->query("SELECT * FROM stocks ORDER BY stock_name ASC");
$feedbacks = $conn->query("SELECT * FROM feedback ORDER BY submission_date DESC");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - StockBuddy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        :root {
            --primary-color: #00796b;
            --secondary-color: #004d40;
            --background-color: #f0f2f5;
            --card-bg: #ffffff;
            --text-color: #333;
            --light-gray: #eef2f7;
            --success-bg: #d4edda;
            --success-text: #155724;
            --error-bg: #f8d7da;
            --error-text: #721c24;
            --info-color: #006097;
        }

        * {
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            margin: 0;
            color: var(--text-color);
        }

        .header {
            background-color: var(--card-bg);
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            height: 70px;
        }

        .header h1 {
            color: var(--primary-color);
            margin: 0;
            font-size: 1.6rem;
        }

        .header h1 i {
            margin-right: 10px;
        }

        .header a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
            border: 1px solid transparent;
        }

        .header a:hover {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .container {
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .stat-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        a.stat-card-link {
            text-decoration: none;
            color: inherit;
        }

        .stat-card {
            background-color: var(--card-bg);
            border-radius: 10px;
            padding: 25px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .stat-card i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-right: 20px;
        }

        .stat-card .info h3 {
            margin: 0 0 5px 0;
            font-size: 1rem;
            color: #666;
        }

        .stat-card .info p {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
        }

        .card {
            background-color: var(--card-bg);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--light-gray);
        }

        .card-header h2 {
            margin: 0;
            font-size: 1.2rem;
        }

        .card-header .controls {
            display: flex;
            gap: 10px;
        }

        .card-body {
            padding: 25px;
        }

        input[type="search"],
        input[type="text"],
        input[type="password"],
        input[type="number"] {
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(0, 121, 107, 0.2);
            outline: none;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        button.danger {
            background-color: var(--error-text);
        }

        button.danger:hover {
            background-color: #a32530;
        }

        button.info {
            background-color: var(--info-color);
        }

        button.info:hover {
            background-color: #004a75;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        td {
            border-bottom: 1px solid var(--light-gray);
            padding: 15px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        .actions button {
            padding: 8px 12px;
            font-size: 0.85rem;
            margin-right: 5px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 30px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--light-gray);
            margin-bottom: 20px;
        }

        .modal-header h2 {
            margin: 0;
            color: var(--primary-color);
        }

        .close-btn {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: black;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
        }

        .modal-body p {
            white-space: pre-wrap;
            line-height: 1.6;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            max-height: 300px;
            overflow-y: auto;
        }

        .modal-footer {
            text-align: right;
            margin-top: 30px;
        }

        #toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 2000;
        }

        .toast {
            padding: 15px 20px;
            margin-bottom: 10px;
            border-radius: 5px;
            color: white;
            min-width: 250px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            opacity: 0;
            transform: translateX(100%);
            animation: slideInToast 0.5s forwards;
        }

        .toast.success {
            background-color: var(--success-text);
        }

        .toast.error {
            background-color: var(--error-text);
        }

        .toast i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        #back-to-top-btn {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 30px;
            z-index: 99;
            border: none;
            outline: none;
            background-color: var(--primary-color);
            color: white;
            cursor: pointer;
            padding: 10px 15px;
            border-radius: 50%;
            font-size: 18px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        #back-to-top-btn:hover {
            background-color: var(--secondary-color);
        }

        @keyframes slideInToast {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeOutToast {
            to {
                opacity: 0;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>

    <div id="toast-container"></div>

    <header class="header">
        <h1><i class="fa fa-shield-halved"></i> Admin Dashboard</h1>
        <a href="logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a>
    </header>

    <main class="container">
        <div class="stat-cards">
            <a href="#users-section" class="stat-card-link">
                <div class="stat-card">
                    <i class="fa fa-users"></i>
                    <div class="info">
                        <h3>Total Users</h3>
                        <p id="user-count"><?php echo $user_count; ?></p>
                    </div>
                </div>
            </a>
            <a href="#stocks-section" class="stat-card-link">
                <div class="stat-card">
                    <i class="fa fa-boxes-stacked"></i>
                    <div class="info">
                        <h3>Listed Stocks</h3>
                        <p id="stock-count"><?php echo $stock_count; ?></p>
                    </div>
                </div>
            </a>
            <a href="#feedback-section" class="stat-card-link">
                <div class="stat-card">
                    <i class="fa fa-envelope-open-text"></i>
                    <div class="info">
                        <h3>Total Feedbacks</h3>
                        <p id="feedback-count"><?php echo $feedback_count; ?></p>
                    </div>
                </div>
            </a>
        </div>

        <div class="card" id="users-section">
            <div class="card-header">
                <h2>Manage Users</h2>
                <div class="controls"><input type="search" id="user-search" placeholder="Search users..."><button
                        id="add-user-btn"><i class="fa fa-user-plus"></i> Add User</button></div>
            </div>
            <div class="card-body">
                <div class="table-wrapper">
                    <table id="user-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $users->fetch_assoc()): ?>
                                <tr data-user-id="<?php echo $row['id']; ?>"
                                    data-username="<?php echo $row['username']; ?>">
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td class="actions"><button class="edit-user-btn"><i class="fa fa-edit"></i>
                                            Edit</button><button class="danger delete-user-btn"><i
                                                class="fa fa-user-times"></i> Delete</button></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card" id="stocks-section">
            <div class="card-header">
                <h2>Manage Stocks</h2>
                <div class="controls"><input type="search" id="stock-search" placeholder="Search stocks..."><button
                        id="add-stock-btn"><i class="fa fa-chart-line"></i> Add Stock</button></div>
            </div>
            <div class="card-body">
                <div class="table-wrapper">
                    <table id="stock-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Stock Name</th>
                                <th>Symbol</th>
                                <th>Base Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $stocks->fetch_assoc()): ?>
                                <tr data-stock-id="<?php echo $row['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($row['stock_name']); ?>"
                                    data-symbol="<?php echo $row['symbol']; ?>"
                                    data-price="<?php echo $row['base_price']; ?>">
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['stock_name']); ?></td>
                                    <td><?php echo $row['symbol']; ?></td>
                                    <td><?php echo $row['base_price']; ?></td>
                                    <td class="actions"><button class="edit-stock-btn"><i class="fa fa-edit"></i>
                                            Edit</button><button class="danger delete-stock-btn"><i class="fa fa-trash"></i>
                                            Delete</button></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card" id="feedback-section">
            <div class="card-header">
                <h2>Manage Feedback</h2>
                <div class="controls"><input type="search" id="feedback-search" placeholder="Search feedback..."></div>
            </div>
            <div class="card-body">
                <div class="table-wrapper">
                    <table id="feedback-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Received On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $feedbacks->fetch_assoc()): ?>
                                <tr data-feedback-id="<?php echo $row['id']; ?>">
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><a
                                            href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a>
                                    </td>
                                    <td><?php echo htmlspecialchars(substr($row['message'], 0, 50)) . (strlen($row['message']) > 50 ? '...' : ''); ?>
                                    </td>
                                    <td><?php echo date("d M, Y h:i A", strtotime($row['submission_date'])); ?></td>
                                    <td class="actions">
                                        <button class="info view-feedback-btn"
                                            data-message="<?php echo htmlspecialchars($row['message']); ?>"><i
                                                class="fa fa-eye"></i> View</button>
                                        <button class="danger delete-feedback-btn"><i class="fa fa-trash"></i>
                                            Delete</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <button id="back-to-top-btn" title="Go to top"><i class="fa fa-arrow-up"></i></button>

    <div id="user-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="user-modal-title">Add New User</h2><span class="close-btn">&times;</span>
            </div>
            <form id="user-form"><input type="hidden" name="id" id="user-id">
                <div class="form-group"><label for="username">Username</label><input type="text" name="username"
                        id="username" required></div>
                <div class="form-group"><label for="password">Password</label><input type="password" name="password"
                        id="password" placeholder="Leave blank to keep current password"></div>
                <div class="modal-footer"><button type="submit" id="user-form-submit">Save User</button></div>
            </form>
        </div>
    </div>

    <div id="stock-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="stock-modal-title">Add New Stock</h2><span class="close-btn">&times;</span>
            </div>
            <form id="stock-form"><input type="hidden" name="id" id="stock-id">
                <div class="form-group"><label for="stock_name">Stock Name</label><input type="text" name="stock_name"
                        id="stock_name" required></div>
                <div class="form-group"><label for="symbol">Symbol</label><input type="text" name="symbol" id="symbol"
                        required></div>
                <div class="form-group" id="add-stock-price-group"><label for="base_price">Base Price</label><input
                        type="number" step="0.01" name="base_price" id="base_price" required></div>
                <div class="modal-footer"><button type="submit">Save Stock</button></div>
            </form>
        </div>
    </div>

    <div id="feedback-view-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Feedback Message</h2><span class="close-btn">&times;</span>
            </div>
            <div class="modal-body" id="feedback-message-content">
                <p></p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const showToast = (message, type = 'success') => {
                const container = document.getElementById('toast-container'); const toast = document.createElement('div');
                toast.className = `toast ${type}`; toast.innerHTML = `<i class="fa ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'}"></i> ${message}`;
                container.appendChild(toast); setTimeout(() => { toast.style.animation = 'fadeOutToast 0.5s forwards'; setTimeout(() => toast.remove(), 500); }, 3000);
            };

            const handleFormSubmit = async (url, formData) => {
                try {
                    const response = await fetch(url, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const result = await response.json();
                    showToast(result.message, result.status);
                    if (result.status === 'success') { setTimeout(() => location.reload(), 1000); }
                } catch (error) { showToast('A client-side error occurred.', 'error'); console.error('Error:', error); }
            };

            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                const closeBtn = modal.querySelector('.close-btn');
                if (closeBtn) closeBtn.onclick = () => modal.style.display = 'none';
                window.onclick = event => { if (event.target == modal) { modal.style.display = 'none'; } };
            });

            const userModal = document.getElementById('user-modal');
            const userForm = document.getElementById('user-form');
            document.getElementById('add-user-btn').addEventListener('click', () => {
                userForm.reset(); document.getElementById('user-id').value = ''; document.getElementById('user-modal-title').innerText = 'Add New User';
                document.getElementById('password').setAttribute('required', 'required'); userModal.style.display = 'block';
            });
            document.querySelectorAll('.edit-user-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const row = btn.closest('tr'); document.getElementById('user-id').value = row.dataset.userId;
                    document.getElementById('username').value = row.dataset.username; document.getElementById('password').value = '';
                    document.getElementById('user-modal-title').innerText = 'Edit User'; document.getElementById('password').removeAttribute('required');
                    userModal.style.display = 'block';
                });
            });
            userForm.addEventListener('submit', e => {
                e.preventDefault(); const formData = new FormData(userForm); const action = formData.get('id') ? 'update_user' : 'add_user';
                handleFormSubmit(`admin.php?action=${action}`, formData);
            });
            document.querySelectorAll('.delete-user-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (confirm('Are you sure you want to delete this user?')) {
                        const row = btn.closest('tr'); const formData = new FormData(); formData.append('id', row.dataset.userId);
                        handleFormSubmit(`admin.php?action=delete_user`, formData);
                    }
                });
            });

            const stockModal = document.getElementById('stock-modal');
            const stockForm = document.getElementById('stock-form');
            const addStockPriceGroup = document.getElementById('add-stock-price-group');

            document.getElementById('add-stock-btn').addEventListener('click', () => {
                stockForm.reset();
                document.getElementById('stock-id').value = '';
                document.getElementById('stock-modal-title').innerText = 'Add New Stock';
                addStockPriceGroup.style.display = 'block';
                document.getElementById('base_price').setAttribute('required', 'required');
                stockModal.style.display = 'block';
            });
            document.querySelectorAll('.edit-stock-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const row = btn.closest('tr');
                    document.getElementById('stock-id').value = row.dataset.stockId;
                    document.getElementById('stock_name').value = row.dataset.name;
                    document.getElementById('symbol').value = row.dataset.symbol;
                    addStockPriceGroup.style.display = 'none';
                    document.getElementById('base_price').removeAttribute('required');

                    document.getElementById('stock-modal-title').innerText = 'Edit Stock';
                    stockModal.style.display = 'block';
                });
            });
            stockForm.addEventListener('submit', e => {
                e.preventDefault(); const formData = new FormData(stockForm); const action = formData.get('id') ? 'update_stock' : 'add_stock';
                handleFormSubmit(`admin.php?action=${action}`, formData);
            });
            document.querySelectorAll('.delete-stock-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (confirm('Are you sure you want to delete this stock?')) {
                        const row = btn.closest('tr'); const formData = new FormData(); formData.append('id', row.dataset.stockId);
                        handleFormSubmit(`admin.php?action=delete_stock`, formData);
                    }
                });
            });

            const feedbackModal = document.getElementById('feedback-view-modal');
            document.querySelectorAll('.view-feedback-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const message = btn.dataset.message;
                    document.querySelector('#feedback-message-content p').innerText = message;
                    feedbackModal.style.display = 'block';
                });
            });
            document.querySelectorAll('.delete-feedback-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (confirm('Are you sure you want to delete this feedback?')) {
                        const row = btn.closest('tr');
                        const formData = new FormData();
                        formData.append('id', row.dataset.feedbackId);
                        handleFormSubmit(`admin.php?action=delete_feedback`, formData);
                    }
                });
            });

            const setupSearch = (inputId, tableId) => {
                document.getElementById(inputId).addEventListener('keyup', function () {
                    const filter = this.value.toUpperCase();
                    const rows = document.getElementById(tableId).querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        const text = row.textContent || row.innerText;
                        row.style.display = text.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
                    });
                });
            };
            setupSearch('user-search', 'user-table');
            setupSearch('stock-search', 'stock-table');
            setupSearch('feedback-search', 'feedback-table');

            const backToTopBtn = document.getElementById('back-to-top-btn');
            window.onscroll = function () {
                if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                    backToTopBtn.style.display = "block";
                } else {
                    backToTopBtn.style.display = "none";
                }
            };
            backToTopBtn.addEventListener('click', function () {
                document.body.scrollTop = 0;
                document.documentElement.scrollTop = 0;
            });

        });
    </script>

</body>

</html>