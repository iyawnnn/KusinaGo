<?php
session_start();
require 'vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->food_ordering;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if ($role === 'admin') {
        // Hardcoded admin credentials
        if ($username === 'admin' && $password === '12345') {
            $_SESSION['admin'] = $username;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = 'Invalid admin credentials.';
        }
    } else {
        // For user login (assuming users are stored in MongoDB)
        $collection = $db->users;
        $user = $collection->findOne(['username' => $username]);

        if ($user && $user['password'] === $password) {
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
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>🔐 Login</h2>

    <?php if ($error): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Username:</label><br>
        <input type="text" name="username" required placeholder="Enter your username"><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Login as:</label><br>
        <select name="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select><br><br>

        <button type="submit">Login</button>
    </form>

    <br>
    <a href="index.php">Back to Home</a>
</body>
</html>
