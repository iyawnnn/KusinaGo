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

$total = 0;
$itemsToSave = [];

foreach ($cart as $item) {
    $subtotal = $item['price'] * $item['quantity'];
    $total += $subtotal;

    $itemsToSave[] = [
        'name' => $item['name'],
        'price' => $item['price'],
        'quantity' => $item['quantity'],
        'subtotal' => $subtotal,
    ];
}

// Save order
$order = [
    'username' => $_SESSION['user'],
    'items' => $itemsToSave,
    'total' => $total,
    'ordered_at' => date('Y-m-d H:i:s'),
];

$result = $ordersCollection->insertOne($order);

if ($result->getInsertedCount() === 1) {
    unset($_SESSION['cart']); // ✅ clear cart after insert
    echo "✅ Order placed successfully!";
    // Optionally redirect to a thank-you page:
    // header("Location: thank_you.php");
    // exit;
} else {
    echo "❌ Order failed to insert!";
}



// Clear cart
unset($_SESSION['cart']);

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
