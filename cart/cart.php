<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';


if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;

$client = new MongoDB\Client("mongodb://localhost:27017");
$menuCollection = $db->menu;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Cart | KusinaGo</title>
    <link rel="stylesheet" href="<?= CSS_PATH ?>main.css">
    <link rel="icon" href="<?= ICON_PATH ?>favicon.svg">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</head>


<body>

    <?php include '../include/header.php'; ?>
    <main>
        <section class="cart-section">
            <div class="cart-container">
                <h2 class="section-heading">Your Cart</h2>

                <?php if (empty($cart)): ?>
                    <div class="empty-cart">
                        <iconify-icon icon="mdi:cart-outline" width="64" height="64" class="empty-cart-icon"></iconify-icon>
                        <p>Your cart is empty.</p>
                        <a href="../menu/menu.php" class="btn-back">← Back to Menu</a>
                    </div>
                <?php else: ?>
                    <div class="cart-table-wrapper">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart as $index => $item):
                                    $subtotal = $item['price'] * $item['quantity'];
                                    $total += $subtotal;
                                    $menuItem = $menuCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($item['id'])]);
                                    $availableStock = $menuItem['stock'] ?? 99;
                                ?>
                                    <tr>
                                        <td class="cart-item">
                                            <img src="../assets/item-pictures/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-item-image">
                                            <div class="cart-item-details">
                                                <span class="cart-item-name"><?= htmlspecialchars($item['name']) ?></span>
                                            </div>
                                        </td>
                                        <td>₱<?= number_format($item['price'], 2) ?></td>
                                        <td>
                                            <div class="quantity-controls">
                                                <button class="qty-btn minus" data-index="<?= $index ?>">−</button>
                                                <input
                                                    type="number"
                                                    class="quantity-input"
                                                    data-index="<?= $index ?>"
                                                    value="<?= $item['quantity'] ?>"
                                                    min="1"
                                                    max="<?= $availableStock ?>"
                                                    readonly>
                                                <button class="qty-btn plus" data-index="<?= $index ?>" data-max="<?= $availableStock ?>">+</button>
                                            </div>
                                            <?php if ($availableStock < $item['quantity']): ?>
                                                <div class="stock-warning">Only <?= $availableStock ?> left</div>
                                            <?php endif; ?>
                                        </td>

                                        <td class="item-total">₱<?= number_format($subtotal, 2) ?></td>
                                        <td>
                                            <form method="post" action="remove_from_cart.php">
                                                <input type="hidden" name="item_index" value="<?= $index ?>">
                                                <button type="submit" class="remove-btn">×</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="cart-summary">
                        <h3>Total: ₱<span id="cart-total"><?= number_format($total, 2) ?></span></h3>
                        <div class="cart-buttons">
                            <a href="../menu/menu.php" class="continue-btn">
                                <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M10 19l-7-7 7-7v4h8v6h-8v4z" />
                                </svg>
                                Browse More
                            </a>
                            <a href="checkout.php" class="checkout-btn">
                                Checkout
                                <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2S15.9 22 17 22s2-.9 2-2-.9-2-2-2zM7.16 14l.84-2h8.99l.94 2H7.16zM6.25 6l1.1 2h11.45l-1.6-4H5.21L4.27 2H2v2h1.27l3.6 7.59-.94 2.34c-.14.36-.23.75-.23 1.07C5.7 15.9 6.59 17 7.7 17h12v-2h-12l1.1-2h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49L21.9 3H5.21l-1-2H0v2h1.27l1.24 2.75z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

    </main>

    <script>
        document.querySelectorAll('.qty-btn').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.quantity-input');
                const index = input.dataset.index;
                let quantity = parseInt(input.value);
                const max = parseInt(this.dataset.max || input.max);

                if (this.classList.contains('plus') && quantity < max) {
                    quantity++;
                } else if (this.classList.contains('minus') && quantity > 1) {
                    quantity--;
                }

                input.value = quantity;

                fetch('update_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `index=${index}&quantity=${quantity}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            input.closest('tr').querySelector('.item-total').textContent = '₱' + data.item_total.toFixed(2);
                            document.getElementById('cart-total').textContent = data.cart_total.toFixed(2);
                        } else {
                            alert(data.message || "Stock limit reached.");
                            window.location.reload();
                        }
                    });
            });
        });
    </script>

    <?php include '../include/footer.php'; ?>

</body>

</html>