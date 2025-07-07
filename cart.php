<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;

// MongoDB client to fetch stock
$client = new MongoDB\Client("mongodb://localhost:27017");
$menuCollection = $client->food_ordering->menu;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart | KusinaGo</title>
</head>
<body>

<?php include 'include/header.php'; ?>

<div class="container">
    <h2>Your Cart 🛒</h2>

    <?php if (empty($cart)): ?>
        <p>Your cart is empty.</p>
        <a href="index.php" class="btn">← Back to Menu</a>
    <?php else: ?>
        <table>
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
            <?php foreach ($cart as $index => $item): 
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;

                // Fetch stock from DB
                $menuItem = $menuCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($item['id'])]);
                $availableStock = $menuItem['stock'] ?? 99;
            ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td>₱<?= number_format($item['price'], 2) ?></td>
                    <td>
                        <input 
                            type="number" 
                            class="quantity-input" 
                            data-index="<?= $index ?>" 
                            value="<?= $item['quantity'] ?>" 
                            min="1" 
                            max="<?= $availableStock ?>" 
                            style="width:60px;"
                        >
                        <?php if ($availableStock < $item['quantity']): ?>
                            <div style="color:red; font-size: 12px;">Only <?= $availableStock ?> in stock</div>
                        <?php endif; ?>
                    </td>
                    <td class="item-total">₱<?= number_format($subtotal, 2) ?></td>
                    <td>
                        <form method="post" action="remove_from_cart.php" style="display:inline;">
                            <input type="hidden" name="item_index" value="<?= $index ?>">
                            <button type="submit">❌</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>Total: ₱<span id="cart-total"><?= number_format($total, 2) ?></span></h3>
        <a href="checkout.php" class="btn">✅ Proceed to Checkout</a>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('change', function () {
        const index = this.dataset.index;
        const quantity = this.value;

        fetch('update_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `index=${index}&quantity=${quantity}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.closest('tr').querySelector('.item-total').textContent = '₱' + data.item_total.toFixed(2);
                document.getElementById('cart-total').textContent = data.cart_total.toFixed(2);
            } else {
                alert(data.message || "Stock limit reached.");
                window.location.reload();
            }
        });
    });
});
</script>

</body>
</html>
