<?php
session_start();
require 'vendor/autoload.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    header("Location: cart.php");
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->food_ordering;
$ordersCollection = $db->orders;
$menuCollection = $db->menu;

$total = 0;
$itemsToSave = [];

use MongoDB\BSON\ObjectId;

foreach ($cart as $item) {
    $subtotal = $item['price'] * $item['quantity'];
    $total += $subtotal;

    $itemsToSave[] = [
        '_id' => $item['id'], // We'll use this to reduce stock
        'name' => $item['name'],
        'price' => $item['price'],
        'quantity' => $item['quantity'],
        'subtotal' => $subtotal
    ];
}

// Save order
$order = [
    'username' => $_SESSION['user'],
    'items' => $itemsToSave,
    'total' => $total,
    'ordered_at' => date('Y-m-d H:i:s'),
    'status' => 'Pending'
];

$result = $ordersCollection->insertOne($order);

if ($result->getInsertedCount() === 1) {
    // 🔻 Reduce stock securely using _id
    foreach ($itemsToSave as $orderedItem) {
        $menuCollection->updateOne(
            ['_id' => new ObjectId($orderedItem['_id'])],
            ['$inc' => ['stock' => -$orderedItem['quantity']]]
        );
    }

    unset($_SESSION['cart']);
    echo "✅ Order placed successfully!";
} else {
    echo "❌ Order failed!";
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmed</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'include/header.php'; ?>

<div class="container">
    <h2>✅ Thank You for Your Order!</h2>
    <p>Your order has been placed successfully.</p>
    <p><strong>Total Paid: ₱<?= number_format($total, 2) ?></strong></p>
    <a href="index.php" class="btn">← Back to Menu</a>
</div>

</body>
</html>
