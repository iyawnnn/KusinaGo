<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->food_ordering->menu;
$items = $collection->find();

$loggedInUser = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Menu</title>
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

            <?php if ($loggedInUser): ?>
                <form method="post" action="add_to_cart.php">
                    <input type="hidden" name="item_id" value="<?= $item['_id'] ?>">
                    <button type="submit" class="cart-btn">Add to Cart</button>
                </form>
            <?php else: ?>
                <a href="login.php" class="cart-btn">Add to Cart</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
