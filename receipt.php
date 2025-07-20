<?php
session_start();
require 'vendor/autoload.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$ordersCollection = $client->food_ordering->orders;

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
    <title>Order Receipt | KusinaGo</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" href="uploads/favicon.svg">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@iconify/iconify@3.1.0/dist/iconify.min.css">
</head>

<body>

    <?php include 'include/header.php'; ?>

    <div class="order-receipt-container">
        <div class="order-receipt-header">
            <h2>Order Receipt</h2>
        </div>

        <div class="order-meta">
            <p><strong>Order ID:</strong> <?= $order['_id'] ?></p>
            <p><strong>Date:</strong> <?= $order['ordered_at'] ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($order['status'] ?? 'Pending') ?></p>
            <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method'] ?? 'Not Specified') ?></p>
        </div>

        <ul class="order-item-list">
            <?php foreach ($order['items'] as $item): ?>
                <li class="order-item">
                    <div class="item-name"><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</div>
                    <div class="item-meta">₱<?= number_format($item['subtotal'], 2) ?></div>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="order-total">
            Total: ₱<?= number_format($order['total'], 2) ?>
        </div>

        <div style="text-align: center;">
            <a href="download_receipt_pdf.php" class="cssbuttons-io-button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path
                        fill="currentColor"
                        d="M1 14.5a6.496 6.496 0 0 1 3.064-5.519 8.001 8.001 0 0 1 15.872 0 6.5 6.5 0 0 1-2.936 12L7 21c-3.356-.274-6-3.078-6-6.5zm15.848 4.487a4.5 4.5 0 0 0 2.03-8.309l-.807-.503-.12-.942a6.001 6.001 0 0 0-11.903 0l-.12.942-.805.503a4.5 4.5 0 0 0 2.029 8.309l.173.013h9.35l.173-.013zM13 12h3l-4 5-4-5h3V8h2v4z"></path>
                </svg>
                <span>Download</span>
            </a>
        </div>
    </div>

</body>

<?php include 'include/footer.php'; ?>

</html>