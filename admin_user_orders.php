<?php
session_start();
require 'vendor/autoload.php';

// Restrict to admins only
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$username = $_GET['username'] ?? null;

if (!$username) {
    echo "❌ No user selected.";
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->food_ordering;
$ordersCollection = $db->orders;

// Get all orders for the specified user
$orders = $ordersCollection->find(
    ['username' => $username],
    ['sort' => ['ordered_at' => -1]]
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Orders of <?= htmlspecialchars($username) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'include/header.php'; ?>

<div class="container">
    <h2>🧾 Orders by <?= htmlspecialchars($username) ?></h2>
    <a href="admin_users.php">← Back to Users</a>
    <hr>

    <?php foreach ($orders as $order): ?>
        <div class="order-card">
            <p><strong>🕒 Ordered on:</strong> <?= $order['ordered_at'] ?></p>
            <p><strong>Status:</strong> <?= $order['status'] ?? 'Pending' ?></p>
            <ul>
                <?php foreach ($order['items'] as $item): ?>
                    <li><?= htmlspecialchars($item['name']) ?> - Qty: <?= $item['quantity'] ?> - ₱<?= number_format($item['subtotal'], 2) ?></li>
                <?php endforeach; ?>
            </ul>
            <p><strong>💰 Total:</strong> ₱<?= number_format($order['total'], 2) ?></p>
            <hr>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
