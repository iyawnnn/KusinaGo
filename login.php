<?php
session_start();
require 'vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->food_ordering;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === 'admin') {
        // Admin login (hardcoded)
        if ($password === '12345') {
            $_SESSION['admin'] = $username;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = 'Invalid admin credentials.';
        }
    } else {
        // User login from MongoDB
        $collection = $db->users;
        $user = $collection->findOne(['username' => $username]);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $username;
            header("Location: index.php");
            exit;
        } else {
            $error = 'Invalid user credentials.';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login | KusinaGo</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" href="uploads/favicon.svg">
</head>

<body class="login-body">
    <div class="login-container">
        <div class="login-box">
            <div class="login-logo">
                <h2 class="login-title">Welcome to KusinaGo</h2>
                <p class="login-subtitle">Please log in to continue</p>
            </div>

            <?php if ($error): ?>
                <div class="login-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" class="login-form">
                <div class="login-field">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Enter your username" required>
                </div>

                <div class="login-field">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="login-submit-btn">Login</button>

                <div class="login-signup-link">
                    Don't have an account? <a href="signup.php">Sign up here</a>
                </div>

            </form>

            <div class="login-footer">
                <a href="index.php" class="login-back-link">← Back to Home</a>
            </div>
        </div>
    </div>
</body>

</html>