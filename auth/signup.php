<?php
session_start();
require 'vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->food_ordering;
$usersCollection = $db->users;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if ($username === '' || $password === '' || $confirmPassword === '') {
        $error = 'All fields are required.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $existingUser = $usersCollection->findOne(['username' => $username]);

        if ($existingUser) {
            $error = 'Username already taken.';
        } else {
            $usersCollection->insertOne([
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ]);

            $success = 'Account created successfully. You may now <a href="login.php">login</a>.';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign Up | KusinaGo</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" href="uploads/favicon.svg">
</head>

<body class="login-body">
    <div class="login-container">
        <div class="login-box">
            <div class="login-logo">
                <h2 class="login-title">Create an Account</h2>
                <p class="login-subtitle">Join the KusinaGo experience</p>
            </div>

            <?php if ($error): ?>
                <div class="login-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="login-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="post" class="login-form">
                <div class="login-field">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Choose a username" required>
                </div>

                <div class="login-field">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Choose a password" required>
                </div>

                <div class="login-field">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Re-enter your password" required>
                </div>

                <button type="submit" class="login-submit-btn">Sign Up</button>

                <div class="login-signup-link">
                    Already have an account? <a href="login.php">Log in here</a>
                </div>
            </form>

            <div class="login-footer">
                <a href="index.php" class="login-back-link">← Back to Home</a>
            </div>
        </div>
    </div>
</body>

</html>