<?php
session_start();
$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="header">
        <h2>🛒 Your Cart</h2>
        <a class="login-btn" href="index.php">⬅️ Back to Menu</a>
    </div>

    <div style="padding: 20px;">
        <?php if (empty($cart)): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <table border="1" cellpadding="10" cellspacing="0">
                <tr>
                    <th>Item</th>
                    <th>₱ Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
                <?php foreach ($cart as $item): ?>
                    <?php $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td>₱<?= $item['price'] ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>₱<?= $subtotal ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td><strong>₱<?= $total ?></strong></td>
                </tr>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
