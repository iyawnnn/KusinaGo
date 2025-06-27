<?php
session_start();
require 'vendor/autoload.php';

// Redirect if not admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$ordersCollection = $client->food_ordering->orders;

$orders = $ordersCollection->find([], [
    'sort' => ['ordered_at' => -1]
]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - View Orders</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'include/header.php'; ?>

<div class="container">
    <h2>📋 All Orders</h2>

    <?php foreach ($orders as $order): ?>
        <div class="order-card">
            <p>🧑‍💻 <strong>User:</strong> <?= htmlspecialchars($order['username']) ?></p>
            <p>🕒 <strong>Ordered on:</strong> <?= date('Y-m-d H:i:s', strtotime($order['ordered_at'])) ?></p>
            <ul>
                <?php foreach ($order['items'] as $item): ?>
                    <li><?= htmlspecialchars($item['name']) ?> - Qty: <?= $item['quantity'] ?> - ₱<?= number_format($item['subtotal'], 2) ?></li>
                <?php endforeach; ?>
            </ul>
            <strong>💰 Total:</strong> ₱<?= number_format($order['total'], 2) ?>
            <hr>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
