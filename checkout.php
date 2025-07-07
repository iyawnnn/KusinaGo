<?php
session_start();
require 'vendor/autoload.php';

use MongoDB\BSON\ObjectId;

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

// If GET: Show payment method form
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }
?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Checkout | KusiaGo</title>
        <link rel="stylesheet" href="css/style.css">
        <link rel="icon" href="uploads/favicon.svg">
    </head>

    <body>
        <?php include 'include/header.php'; ?>

        <div class="container">
            <h2>🧾 Checkout</h2>
            <p><strong>Total: ₱<?= number_format($total, 2) ?></strong></p>

            <form method="post">
                <label><strong>💳 Select Payment Method:</strong></label><br>
                <select name="payment_method" required>
                    <option value="">-- Choose --</option>
                    <option value="Cash on Delivery">Cash on Delivery</option>
                    <option value="GCash">GCash</option>
                    <option value="Credit Card">Credit Card</option>
                </select>
                <br><br>
                <button type="submit">✅ Confirm and Pay</button>
            </form>
        </div>

    </body>

    </html>
<?php
    exit;
}

// If POST: Process the order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method'])) {
    $total = 0;
    $itemsToSave = [];

    foreach ($cart as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;

        $itemsToSave[] = [
            '_id' => $item['id'],
            'name' => $item['name'],
            'price' => $item['price'],
            'quantity' => $item['quantity'],
            'subtotal' => $subtotal
        ];
    }

    $order = [
        'username' => $_SESSION['user'],
        'items' => $itemsToSave,
        'total' => $total,
        'payment_method' => $_POST['payment_method'],
        'ordered_at' => date('Y-m-d H:i:s'),
        'status' => 'Pending'
    ];

    $result = $ordersCollection->insertOne($order);

    if ($result->getInsertedCount() === 1) {
        foreach ($itemsToSave as $orderedItem) {
            $menuCollection->updateOne(
                ['_id' => new ObjectId($orderedItem['_id'])],
                ['$inc' => ['stock' => -$orderedItem['quantity']]]
            );
        }

        unset($_SESSION['cart']);
        header("Location: receipt.php");
        exit;
    } else {
        echo "❌ Order failed!";
    }
}
?>