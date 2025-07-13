<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->food_ordering->menu;
$allItems = $collection->find()->toArray();

// Group items by category
$groupedItems = [];
foreach ($allItems as $item) {
    $category = $item['category'] ?? 'Uncategorized';
    $groupedItems[$category][] = $item;
}

$loggedInUser = $_SESSION['user'] ?? null;
$loggedInAdmin = $_SESSION['admin'] ?? null;

// Optional: define category display order
$displayOrder = ['Handa sa Hapág (Main Dishes)', 'Panimula (Appetizers)', 'Panghimagas (Desserts)', 'Pang-alis Uhaw (Beverages)'];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Menu | KusinaGo</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="icon" href="uploads/favicon.svg">
</head>

<body>

    <?php include 'include/header.php'; ?>

    <?php foreach ($displayOrder as $category): ?>
        <?php if (!isset($groupedItems[$category])) continue; ?>

        <div class="menu-section">
            <h2 class="menu-heading"><?= htmlspecialchars($category) ?></h2>

            <div class="menu-container">
                <?php foreach ($groupedItems[$category] as $item): ?>
                    <?php
                    $stock = isset($item['stock']) ? (int)$item['stock'] : null;
                    $outOfStock = $stock !== null && $stock <= 0;

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

                    <div class="item">
                        <div class="item-info">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="desc"><?= htmlspecialchars($item['description'] ?? 'No description available.') ?></p>
                            <p class="price">₱<?= htmlspecialchars($item['price']) ?></p>

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

                        <div class="item-img">
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

</body>

<script>
  // Save scroll position before form submit
  document.querySelectorAll("form").forEach(form => {
    form.addEventListener("submit", () => {
      localStorage.setItem("scrollPos", window.scrollY);
    });
  });

  // Restore scroll after reload
  window.addEventListener("load", () => {
    const scrollPos = localStorage.getItem("scrollPos");
    if (scrollPos) {
      window.scrollTo(0, parseInt(scrollPos));
      localStorage.removeItem("scrollPos");
    }
  });

  const header = document.querySelector('.header');
</script>

</html>