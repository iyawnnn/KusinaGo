<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

use MongoDB\BSON\ObjectId;

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
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
            $found = false;
            foreach ($_SESSION['cart'] as &$ci) {
                if ($ci['id'] === $cartItemId) {
                    $ci['quantity']++;
                    $found = true;
                    break;
                }
            }
            unset($ci);

            if (!$found) {
                $_SESSION['cart'][] = [
                    'id' => $cartItemId,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => 1,
                    'image' => $item['image']
                ];
            }

            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Out of stock']);
            exit;
        }
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;
