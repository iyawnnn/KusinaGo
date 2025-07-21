<?php
session_start();
require 'vendor/autoload.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$menuCollection = $client->food_ordering->menu;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'], $_POST['stock'])) {
    $itemId = new MongoDB\BSON\ObjectId($_POST['item_id']);
    $stock = (int)$_POST['stock'];

    $menuCollection->updateOne(
        ['_id' => $itemId],
        ['$set' => ['stock' => $stock]]
    );

    header("Location: admin_inventory.php");
    exit;
}

$items = $menuCollection->find();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management | KusinaGo</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" href="uploads/favicon.svg">
</head>
<body>
<?php include 'include/header.php'; ?>

<main class="admin-content">
    <section class="admin-section">
        <h2 class="admin-section-title">Inventory Management</h2>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Current Stock</th>
                        <th>Update Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= $item['stock'] ?? 0 ?></td>
                            <td>
                                <form method="post" class="stock-form">
                                    <input type="hidden" name="item_id" value="<?= $item['_id'] ?>">
                                    <input type="number" name="stock" min="0" value="<?= $item['stock'] ?? 0 ?>" required>
                                    <button type="submit" class="btn-update">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php include 'include/footer.php'; ?>
</body>
</html>
