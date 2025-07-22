<?php
session_start();
require 'vendor/autoload.php';

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

$ordersCursor = $ordersCollection->find(
    ['username' => $username],
    ['sort' => ['ordered_at' => -1]]
);
$orders = iterator_to_array($ordersCursor);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Orders of <?= htmlspecialchars($username) ?> | KusinaGo</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" href="uploads/favicon.svg">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</head>

<body>

    <?php include 'include/header.php'; ?>

    <main>
        <div class="kg-orders-wrapper">

            <div style="margin-bottom: 30px;">
                <h2 style="font-size: 26px; color: #0E3F18; font-weight: 700;">Orders by <?= htmlspecialchars($username) ?></h2>
                <a href="admin_users.php" class="menu-btn back" style="margin-top: 10px; display: inline-block;">← Back to Users</a>
            </div>

            <?php if (empty($orders)): ?>
                <div class="empty-orders">
                    <iconify-icon icon="fluent:clipboard-task-list-ltr-24-regular"></iconify-icon>
                    <p>No orders yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="kg-order-card">
                        <p><strong>Ordered on:</strong> <?= $order['ordered_at'] ?></p>
                        <p><strong>Status:</strong>
                            <span class="kg-status-tag kg-status-<?= $order['status'] ?? 'Pending' ?>">
                                <?= $order['status'] ?? 'Pending' ?>
                            </span>
                        </p>
                        <ul class="kg-order-items">
                            <?php foreach ($order['items'] as $item): ?>
                                <li><?= htmlspecialchars($item['name']) ?> - Qty: <?= $item['quantity'] ?> - ₱<?= number_format($item['subtotal'], 2) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="kg-order-total"><strong>Total:</strong> ₱<?= number_format($order['total'], 2) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </main>

    <?php include 'include/footer_admin.php'; ?>

</body>

</html>