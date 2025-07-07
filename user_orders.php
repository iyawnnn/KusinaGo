<?php
session_start();
require 'vendor/autoload.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->food_ordering;
$ordersCollection = $db->orders;

$username = $_SESSION['user'];
$orders = $ordersCollection->find(['username' => $username], ['sort' => ['ordered_at' => -1]]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders | KusinaGo</title>
</head>
<body>

<?php include 'include/header.php'; ?>

<div class="container">
    <h2>My Orders</h2>

    <?php
    $foundOrders = false;
    foreach ($orders as $order):
        $foundOrders = true;
    ?>
        <div class="order-box">
            <h3>Ordered on: <?= $order['ordered_at'] ?></h3>
            <p>Status: <?= $order['status'] ?? 'Pending' ?></p>
            <ul>
                <?php foreach ($order['items'] as $item): ?>
                    <li><?= htmlspecialchars($item['name']) ?> - Qty: <?= $item['quantity'] ?> - ₱<?= number_format($item['subtotal'], 2) ?></li>
                <?php endforeach; ?>
            </ul>
            <strong>Total: ₱<?= number_format($order['total'], 2) ?></strong>

            <?php if (($order['status'] ?? 'Pending') === 'Pending'): ?>
                <form method="post" action="cancel_order.php" style="margin-top: 10px;">
                    <input type="hidden" name="order_id" value="<?= $order['_id'] ?>">
                    <button type="submit" class="btn">Cancel Order</button>
                </form>
            <?php endif; ?>
        </div>
        <hr>
    <?php endforeach; ?>

    <?php if (!$foundOrders): ?>
        <p>You have no previous orders.</p>
    <?php endif; ?>
</div>
</body>
</html>
