<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

use MongoDB\BSON\ObjectId;

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $itemId = $_POST['item_id'];

    $client = new MongoDB\Client("mongodb://localhost:27017");
    $menuCollection = $client->food_ordering->menu;

    $item = $menuCollection->findOne(['_id' => new ObjectId($itemId)]);

    if ($item) {
        $stock = isset($item['stock']) ? (int)$item['stock'] : 0;
        $cartItemId = (string)$item['_id'];

        // Count quantity in cart already
        $currentQtyInCart = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $ci) {
                if ($ci['id'] === $cartItemId) {
                    $currentQtyInCart = $ci['quantity'];
                    break;
                }
            }
        }

        if ($currentQtyInCart < $stock) {
            // Add or increment item in cart
            $found = false;
            foreach ($_SESSION['cart'] as &$ci) {
                if ($ci['id'] === $cartItemId) {
                    $ci['quantity']++;
                    $found = true;
                    break;
                }
            }
            unset($ci); // Break reference

            if (!$found) {
                $_SESSION['cart'][] = [
                    'id' => $cartItemId,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => 1
                ];
            }

        } else {
            $_SESSION['error'] = "🚫 You cannot add more than available stock!";
        }
    }
}

header("Location: index.php");
exit;
