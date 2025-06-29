<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->food_ordering->menu;
$items = $collection->find();

$loggedInUser = $_SESSION['user'] ?? null;
$loggedInAdmin = $_SESSION['admin'] ?? null;
?>

<!DOCTYPE html>
<html>

<head>
    <title>AIM SWIFT - Menu</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php include 'include/header.php'; ?>

    <div class="menu-container">
        <?php foreach ($items as $item): ?>
            <div class="item">
                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                <h3><?= htmlspecialchars($item['name']) ?></h3>
                <p>₱<?= htmlspecialchars($item['price']) ?></p>
                <p><em><?= htmlspecialchars($item['category']) ?></em></p>

                <?php
                $stock = isset($item['stock']) ? (int)$item['stock'] : null;
                $outOfStock = $stock !== null && $stock <= 0;

                // Count current quantity in cart
                $currentInCart = 0;
                if (isset($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $ci) {
                        if ($ci['id'] === (string)$item['_id']) {
                            $currentInCart = $ci['quantity'];
                            break;
                        }
                    }
                }

                $reachedLimit = $stock !== null && $currentInCart >= $stock;
                ?>

                <?php if ($loggedInUser): ?>
                    <?php if ($outOfStock): ?>
                        <p class="out-of-stock">❌ Out of Stock</p>
                        <button class="cart-btn" disabled>Add to Cart</button>
                    <?php elseif ($reachedLimit): ?>
                        <p class="out-of-stock">🛑 Max stock (<?= $stock ?>) in cart</p>
                        <button class="cart-btn" disabled>Add to Cart</button>
                    <?php else: ?>
                        <form method="post" action="add_to_cart.php">
                            <input type="hidden" name="item_id" value="<?= $item['_id'] ?>">
                            <button type="submit" class="cart-btn">Add to Cart</button>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="cart-btn">Add to Cart</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

</body>

</html>
