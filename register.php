<?php
// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stockbuddy_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$message_class = "";
$redirect = false;

if (isset($_POST['register'])) {  // 👉 Run only after pressing submit
    $newUser = trim($_POST['username']);
    $plainPassword = trim($_POST['password']);

    // Step 1: Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $newUser);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = "❌ Username already taken!";
        $message_class = "error-msg";
    } else {
        // Step 2: Hash password
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        // Step 3: Insert into DB
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $newUser, $hashedPassword);

        if ($stmt->execute()) {
            $message = "✅ Registration successful! Redirecting to login...";
            $message_class = "success-msg";
            $redirect = true;
        } else {
            $message = "Error: " . $stmt->error;
            $message_class = "error-msg";
        }
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - StockBuddy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-box {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px gray;
            width: 350px;
        }
        h2 { text-align: center; margin-bottom: 20px; color: #333; }
        input[type=text], input[type=password] {
            width: 100%; padding: 10px; margin: 8px 0;
            border-radius: 5px; border: 1px solid #ccc;
        }
        button {
            width: 100%; padding: 10px; background: #4CAF50;
            border: none; color: white; font-weight: bold;
            border-radius: 5px; cursor: pointer;
        }
        button:hover { background: #45a049; }
        .success-msg { color: green; text-align: center; margin-bottom: 10px; font-weight: bold; }
        .error-msg { color: red; text-align: center; margin-bottom: 10px; font-weight: bold; }
    </style>
    <?php if ($redirect): ?>
        <meta http-equiv="refresh" content="2;url=login.php">
    <?php endif; ?>
</head>
<body>
    <div class="register-box">
        <h2>Register</h2>

        <?php if (!empty($message)): ?>
            <div class="<?php echo $message_class; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="username" placeholder="Enter username" required>
            <input type="password" name="password" placeholder="Enter password" required>
            <button type="submit" name="register">Register</button>
        </form>
    </div>
</body>
</html>
