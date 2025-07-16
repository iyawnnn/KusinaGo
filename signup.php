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

    if ($username === '' || $password === '') {
        $error = 'All fields are required.';
    } else {
        // Check if username already exists
        $existingUser = $usersCollection->findOne(['username' => $username]);

        if ($existingUser) {
            $error = 'Username already taken.';
        } else {
            // Insert new user
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
<html>
<head>
    <title>Sign Up</title>
</head>
<body>

    <h2>Sign Up</h2>

    <?php if ($error): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green"><?= $success ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Sign Up</button>
    </form>

    <br>
    <a href="index.php">Back to Home</a> | <a href="login.php">Already have an account?</a>

</body>
</html>
