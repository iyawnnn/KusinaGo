<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

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
$displayOrder = ['Handa sa Hapág (Main Dishes)', 'Panimula (Appetizers)', 'Panghimagas (Desserts)', 'Pagpatid-Uhaw (Beverages)'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Menu | KusinaGo</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <link rel="icon" href="../assets/icons/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<?php include '../include/header.php'; ?>

<main>
<?php foreach ($displayOrder as $category): ?>
    <?php if (!isset($groupedItems[$category])) continue; ?>

    <div class="menu-section">
        <h2 class="menu-heading"><?= htmlspecialchars($category) ?></h2>

        <div class="menu-container">
        <?php foreach ($groupedItems[$category] as $item): ?>
            <?php
            $itemId = (string)$item['_id'];
            $stock = isset($item['stock']) ? (int)$item['stock'] : null;

            $currentInCart = 0;
            if (isset($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $ci) {
                    if ($ci['id'] === $itemId) {
                        $currentInCart = $ci['quantity'];
                        break;
                    }
                }
            }

            $outOfStock = $stock !== null && $currentInCart >= $stock;
            ?>

            <div class="item">
                <div class="item-info">
                    <div class="item-text">
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <p class="desc"><?= htmlspecialchars($item['description'] ?? 'No description available.') ?></p>
                        <p class="price">₱<?= htmlspecialchars($item['price']) ?></p>
                    </div>

                    <div class="item-action">
                        <?php if ($loggedInUser): ?>
                            <?php if ($outOfStock): ?>
                                <button class="cart-btn" disabled>Out of Stock</button>
                            <?php else: ?>
                                <form class="add-to-cart-form" data-id="<?= $itemId ?>" data-stock="<?= $stock ?>" style="display:inline;">
                                    <button type="submit" class="cart-btn">Add to Cart</button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="../auth/login.php" class="cart-btn">Add to Cart</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="item-img">
                    <img src="../assets/item-pictures/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>
</main>

<?php include '../include/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const button = this.querySelector('button');
            const itemId = this.getAttribute('data-id');
            const stock = parseInt(this.getAttribute('data-stock'));

            const response = await fetch('../cart/add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `item_id=${encodeURIComponent(itemId)}`
            });

            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    // Update cart count
                    const badge = document.getElementById('cart-count');
                    if (badge) {
                        const current = parseInt(badge.textContent) || 0;
                        badge.textContent = current + 1;
                    }

                    // Update button if stock limit reached
                    if (stock !== null && result.newQuantity >= stock) {
                        button.textContent = "Out of Stock";
                        button.disabled = true;
                    }

                    button.blur();
                } else if (result.message) {
                    // If backend says out of stock
                    button.textContent = "Out of Stock";
                    button.disabled = true;
                }
            }
        });
    });
});
</script>
</body>
</html>
