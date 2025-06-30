<?php
session_start();
require 'vendor/autoload.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$ordersCollection = $client->food_ordering->orders;

// Get latest order of this user
$order = $ordersCollection->findOne(
    ['username' => $_SESSION['user']],
    ['sort' => ['ordered_at' => -1]]
);

if (!$order) {
    echo "❌ No order found.";
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Receipt</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .receipt {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 2px dashed #ccc;
            background: #fff;
        }
        .receipt h2 {
            text-align: center;
        }
        .receipt ul {
            list-style: none;
            padding: 0;
        }
        .receipt li {
            margin: 5px 0;
        }
        .print-btn {
            margin-top: 20px;
            display: block;
            text-align: center;
        }
        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>

<?php include 'include/header.php'; ?>

<div class="receipt">
    <h2>🧾 Order Receipt</h2>
    <p><strong>Order ID:</strong> <?= $order['_id'] ?></p>
    <p><strong>Date:</strong> <?= $order['ordered_at'] ?></p>
    <p><strong>Status:</strong> <?= $order['status'] ?? 'Pending' ?></p>
    <hr>
    <ul>
        <?php foreach ($order['items'] as $item): ?>
            <li><?= htmlspecialchars($item['name']) ?> — Qty: <?= $item['quantity'] ?> — ₱<?= number_format($item['subtotal'], 2) ?></li>
        <?php endforeach; ?>
    </ul>
    <hr>
    <p><strong>Total:</strong> ₱<?= number_format($order['total'], 2) ?></p>

    <button class="btn print-btn" onclick="window.print()">🖨️ Print Receipt</button>
</div>

</body>
</html>
