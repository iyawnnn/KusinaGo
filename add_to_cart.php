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
    $collection = $client->food_ordering->menu;

    $item = $collection->findOne(['_id' => new ObjectId($itemId)]);

    if ($item) {
        $cartItem = [
            'id' => (string)$item['_id'],
            'name' => $item['name'],
            'price' => $item['price'],
            'quantity' => 1
        ];

        // Initialize cart if not set
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $found = false;
        // If item already in cart, increase quantity
        foreach ($_SESSION['cart'] as &$ci) {
            if ($ci['id'] === $cartItem['id']) {
                $ci['quantity']++;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $_SESSION['cart'][] = $cartItem;
        }
    }
}

header("Location: index.php");
exit;
