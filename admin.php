<?php
// DB connect
$conn = new mysqli("localhost", "root", "", "stockbuddy_db");
if ($conn->connect_error) die("DB error: " . $conn->connect_error);

///// USERS CRUD /////
// Add user
if (isset($_POST['add_user'])) {
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $stmt->close();
}

// Update user
if (isset($_POST['update_user'])) {
    $id   = $_POST['id'];
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET username=?, password=? WHERE id=?");
    $stmt->bind_param("ssi", $user, $pass, $id);
    $stmt->execute();
    $stmt->close();
}

// Delete user
if (isset($_GET['delete_user'])) {
    $id = $_GET['delete_user'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

///// STOCKS CRUD /////
// Add stock
if (isset($_POST['add_stock'])) {
    $name   = $_POST['stock_name'];
    $symbol = $_POST['symbol'];
    $price  = $_POST['base_price'];
    $stmt = $conn->prepare("INSERT INTO stocks (stock_name, symbol, base_price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $name, $symbol, $price);
    $stmt->execute();
    $stmt->close();
}

// Update stock
if (isset($_POST['update_stock'])) {
    $id     = $_POST['id'];
    $name   = $_POST['stock_name'];
    $symbol = $_POST['symbol'];
    $price  = $_POST['base_price'];
    $stmt = $conn->prepare("UPDATE stocks SET stock_name=?, symbol=?, base_price=? WHERE id=?");
    $stmt->bind_param("ssdi", $name, $symbol, $price, $id);
    $stmt->execute();
    $stmt->close();
}

// Delete stock
if (isset($_GET['delete_stock'])) {
    $id = $_GET['delete_stock'];
    $stmt = $conn->prepare("DELETE FROM stocks WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Panel</title>
</head>
<body>
  <h1>Admin Panel</h1>

  <!-- USERS SECTION -->
  <h2>Manage Users</h2>
  <form method="POST">
    <input type="text" name="username" placeholder="New Username" required>
    <input type="password" name="password" placeholder="New Password" required>
    <button type="submit" name="add_user">Add User</button>
  </form>

  <table border="1" cellpadding="5">
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Actions</th>
    </tr>
    <?php
    $users = $conn->query("SELECT * FROM users");
    while ($row = $users->fetch_assoc()) {
        echo "<tr>
          <td>{$row['id']}</td>
          <td>{$row['username']}</td>
          <td>
            <a href='?delete_user={$row['id']}'>Delete</a> | 
            <form method='POST' style='display:inline;'>
              <input type='hidden' name='id' value='{$row['id']}'>
              <input type='text' name='username' value='{$row['username']}' required>
              <input type='password' name='password' placeholder='New Password' required>
              <button type='submit' name='update_user'>Update</button>
            </form>
          </td>
        </tr>";
    }
    ?>
  </table>

  <hr>

  <!-- STOCKS SECTION -->
  <h2>Manage Stocks</h2>
  <form method="POST">
    <input type="text" name="stock_name" placeholder="Stock Name" required>
    <input type="text" name="symbol" placeholder="Symbol" required>
    <input type="number" step="0.01" name="base_price" placeholder="Base Price" required>
    <button type="submit" name="add_stock">Add Stock</button>
  </form>

  <table border="1" cellpadding="5">
    <tr>
      <th>ID</th>
      <th>Stock Name</th>
      <th>Symbol</th>
      <th>Base Price</th>
      <th>Actions</th>
    </tr>
    <?php
    $stocks = $conn->query("SELECT * FROM stocks");
    while ($row = $stocks->fetch_assoc()) {
        echo "<tr>
          <td>{$row['id']}</td>
          <td>{$row['stock_name']}</td>
          <td>{$row['symbol']}</td>
          <td>{$row['base_price']}</td>
          <td>
            <a href='?delete_stock={$row['id']}'>Delete</a> | 
            <form method='POST' style='display:inline;'>
              <input type='hidden' name='id' value='{$row['id']}'>
              <input type='text' name='stock_name' value='{$row['stock_name']}' required>
              <input type='text' name='symbol' value='{$row['symbol']}' required>
              <input type='number' step='0.01' name='base_price' value='{$row['base_price']}' required>
              <button type='submit' name='update_stock'>Update</button>
            </form>
          </td>
        </tr>";
    }
    ?>
  </table>

</body>
</html>
