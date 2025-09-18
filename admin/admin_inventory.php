<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

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

$itemsArray = iterator_to_array($menuCollection->find());
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inventory Management | KusinaGo</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <link rel="icon" href="../assets/icons/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <?php include '../include/header.php'; ?>

    <main class="admin-content">
        <section class="admin-section">
            <h2 class="admin-section-title">Inventory Management</h2>

            <!-- Desktop Table -->
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
                        <?php foreach ($itemsArray as $item): ?>
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

            <!-- Mobile Card View -->
            <div class="inventory-card-container">
                <?php foreach ($itemsArray as $item): ?>
                    <div class="inventory-card">
                        <p><strong>Item:</strong> <?= htmlspecialchars($item['name']) ?></p>
                        <p><strong>Current Stock:</strong> <?= $item['stock'] ?? 0 ?></p>
                        <p>
                            <strong>Update Stock:</strong>
                            <form method="post" class="stock-form">
                                <input type="hidden" name="item_id" value="<?= $item['_id'] ?>">
                                <input type="number" name="stock" min="0" value="<?= $item['stock'] ?? 0 ?>" required>
                                <button type="submit" class="btn-update">Update</button>
                            </form>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>

        </section>
    </main>

    <?php include '../include/footer_admin.php'; ?>
</body>

</html>
