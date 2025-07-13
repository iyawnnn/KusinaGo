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
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" href="uploads/favicon.svg">
</head>
<body>

<?php include 'include/header.php'; ?>

<div class="order-history-wrapper">
    <h2 class="order-history-title">My Orders</h2>

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
                    <li>
                        <?= htmlspecialchars($item['name']) ?>
                        <span>Qty: <?= $item['quantity'] ?> - ₱<?= number_format($item['subtotal'], 2) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="order-actions">
                <?php if (($order['status'] ?? 'Pending') === 'Pending'): ?>
                    <form method="post" action="cancel_order.php">
                        <input type="hidden" name="order_id" value="<?= $order['_id'] ?>">
                        <button type="submit" class="cancel-btn">Cancel Order</button>
                    </form>
                <?php else: ?>
                    <div></div> <!-- placeholder for layout -->
                <?php endif; ?>
                <div class="order-total">₱<?= number_format($order['total'], 2) ?></div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (!$foundOrders): ?>
        <p class="no-orders-msg">You have no previous orders.</p>
    <?php endif; ?>
</div>

</body>
</html>
