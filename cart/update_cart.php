<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $index = $_POST['index'] ?? null;
    $quantity = (int) ($_POST['quantity'] ?? 1);

    if (isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['quantity'] = max(1, $quantity);

        $item = $_SESSION['cart'][$index];
        $itemTotal = $item['price'] * $item['quantity'];

        // Recalculate cart total
        $cartTotal = 0;
        foreach ($_SESSION['cart'] as $i) {
            $cartTotal += $i['price'] * $i['quantity'];
        }

        echo json_encode([
            'success' => true,
            'item_total' => $itemTotal,
            'cart_total' => $cartTotal
        ]);
        exit;
    }
}

echo json_encode(['success' => false]);
