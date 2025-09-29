<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to transact.']);
    exit();
}

include 'db_connect.php';


$username = $_SESSION['user'];
$user_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$user_stmt->bind_param("s", $username);
$user_stmt->execute();
$user_id = $user_stmt->get_result()->fetch_assoc()['id'];
$user_stmt->close();

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'User not found.']);
    exit();
}

$action = $_POST['action'] ?? '';
$symbol = $_POST['symbol'] ?? '';
$quantity_to_transact = (int)($_POST['quantity'] ?? 0);
$price = (float)($_POST['price'] ?? 0);

if ($quantity_to_transact <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid quantity.']);
    exit();
}

try {
    $conn->begin_transaction();


    $p_stmt = $conn->prepare("SELECT quantity, purchase_price FROM portfolio WHERE user_id = ? AND stock_symbol = ? FOR UPDATE");
    $p_stmt->bind_param("is", $user_id, $symbol);
    $p_stmt->execute();
    $portfolio_item = $p_stmt->get_result()->fetch_assoc();
    $p_stmt->close();

    if ($action === 'buy') {
        if ($portfolio_item) {
            $old_qty = $portfolio_item['quantity'];
            $old_price = $portfolio_item['purchase_price'];
            $new_total_qty = $old_qty + $quantity_to_transact;
            $new_avg_price = (($old_qty * $old_price) + ($quantity_to_transact * $price)) / $new_total_qty;

            $update_stmt = $conn->prepare("UPDATE portfolio SET quantity = ?, purchase_price = ? WHERE user_id = ? AND stock_symbol = ?");
            $update_stmt->bind_param("idis", $new_total_qty, $new_avg_price, $user_id, $symbol);
            $update_stmt->execute();
            $update_stmt->close();
        } else {
            $insert_stmt = $conn->prepare("INSERT INTO portfolio (user_id, stock_symbol, quantity, purchase_price) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("isid", $user_id, $symbol, $quantity_to_transact, $price);
            $insert_stmt->execute();
            $insert_stmt->close();
        }
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => "Successfully bought $quantity_to_transact shares of $symbol."]);

    } elseif ($action === 'sell') {
        if (!$portfolio_item) {
            throw new Exception("You do not own any shares of this stock.");
        }

        $current_qty = $portfolio_item['quantity'];
        if ($quantity_to_transact > $current_qty) {
            throw new Exception("You cannot sell more shares than you own. You have $current_qty shares.");
        }

        $new_qty = $current_qty - $quantity_to_transact;

        if ($new_qty == 0) {
            $delete_stmt = $conn->prepare("DELETE FROM portfolio WHERE user_id = ? AND stock_symbol = ?");
            $delete_stmt->bind_param("is", $user_id, $symbol);
            $delete_stmt->execute();
            $delete_stmt->close();
        } else {
            $update_stmt = $conn->prepare("UPDATE portfolio SET quantity = ? WHERE user_id = ? AND stock_symbol = ?");
            $update_stmt->bind_param("iis", $new_qty, $user_id, $symbol);
            $update_stmt->execute();
            $update_stmt->close();
        }
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => "Successfully sold $quantity_to_transact shares of $symbol."]);
    
    } else {
        throw new Exception("Invalid action specified.");
    }
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>