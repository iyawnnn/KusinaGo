<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $collection = $client->food_ordering->admins;

    $username = $_POST['username'];
    $password = $_POST['password'];

    $admin = $collection->findOne(['username' => $username]);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin'] = $username;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid credentials!';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
</head>
<body>
    <h2>Admin Login</h2>
    <?php if ($error): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endif; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
