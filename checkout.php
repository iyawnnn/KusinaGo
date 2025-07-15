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
        <link rel="stylesheet" href="css/main.css">
        <link rel="icon" href="uploads/favicon.svg">
    </head>

    <body>
        <?php include 'include/header.php'; ?>

        <div class="checkout-form-wrapper">
            <div class="checkout-container">
                <h2>Checkout</h2>

                <form method="post" class="checkout-form">

                    <!-- Shipping Details -->
                    <h3>Shipping Details</h3>

                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Juan Dela Cruz" required>

                    <label for="contact_number">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number" placeholder="09XXXXXXXXX" required>

                    <label for="street">Street Address</label>
                    <input type="text" id="street" name="street" placeholder="123 Purok 5, Mabini St." required>

                    <label for="barangay">Barangay</label>
                    <input type="text" id="barangay" name="barangay" placeholder="Brgy. San Isidro" required>

                    <label for="city">City / Municipality</label>
                    <input type="text" id="city" name="city" placeholder="San Fernando" required>

                    <label for="province">Province</label>
                    <select id="province" name="province" required>
                        <option value="">Select a province</option>
                        <option value="Pampanga">Pampanga</option>
                        <option value="Bulacan">Bulacan</option>
                        <option value="Cavite">Cavite</option>
                        <option value="Laguna">Laguna</option>
                        <option value="Batangas">Batangas</option>
                        <option value="Quezon">Quezon</option>
                        <option value="Metro Manila">Metro Manila</option>
                    </select>

                    <label for="zip">ZIP Code</label>
                    <input type="text" id="zip" name="zip" placeholder="2000" required>

                    <!-- Payment Method -->
                    <h3 class="payment-method-header">Payment Method</h3>
                    <div class="payment-method-group">
                        <label class="payment-method">
                            <div class="method-info">
                                <img src="uploads/cod.svg" alt="COD" />
                                <p>Cash on Delivery</p>
                                <input type="radio" name="payment_method" value="Cash on Delivery" required />
                            </div>
                        </label>

                        <label class="payment-method">
                            <div class="method-info">
                                <img src="uploads/gcash.svg" alt="GCash" />
                                <p>GCash</p>
                                <input type="radio" name="payment_method" value="GCash" required />
                            </div>
                        </label>

                        <label class="payment-method">
                            <div class="method-info">
                                <img src="uploads/creditcard.svg" alt="Credit Card" />
                                <p>Credit Card</p>
                                <input type="radio" name="payment_method" value="Credit Card" required />
                            </div>
                        </label>
                    </div>

                    <!-- Total + Button -->
                    <div class="checkout-footer">
                        <div class="total-and-confirm">
                            <p><strong>Total: ₱<?= number_format($total, 2) ?></strong></p>

                            <button
                                type="submit"
                                class="checkout-confirm-btn"
                                data-tooltip="Total: ₱<?= number_format($total, 2) ?>">

                                <div class="checkout-btn-wrapper">
                                    <span class="checkout-btn-text">Confirm & Pay</span>
                                    <span class="checkout-btn-icon">
                                        <svg viewBox="0 0 16 16" fill="currentColor" height="20" width="20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 
              0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 
              1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 
              5l1.25 5h8.22l1.25-5H3.14zM5 13a1 1 0 1 0 0 2 
              1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 
              0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 
              0 1 1 4 0 2 2 0 0 1-4 0z" />
                                        </svg>
                                    </span>
                                </div>
                            </button>
                        </div>
                    </div>

                </form>
            </div>
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