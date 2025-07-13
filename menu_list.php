<?php
include 'admin_auth.php';
require __DIR__ . '/vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->food_ordering->menu;

$items = $collection->find();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menu Management | KusinaGo</title>
    <link rel="stylesheet" href="css/main.css"> <!-- Connect your main stylesheet -->
    <link rel="icon" href="uploads/favicon.svg">
</head>
<body>

<?php include 'include/header.php'; ?> <!-- Include admin navbar -->

<section class="menu-list-section">
    <div class="menu-list-inner">

        <!-- Header: Title + Buttons -->
        <div class="menu-list-header">
            <h2 class="menu-list-title">Manage Menu Items</h2>
            <div class="menu-list-actions">
                <a href="add_item.php" class="menu-btn add">Add New Item</a>
                <a href="dashboard.php" class="menu-btn back">Back to Dashboard</a>
            </div>
        </div>

        <!-- Table -->
        <div class="menu-table-container">
            <table class="menu-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td>₱<?= number_format($item['price'], 2) ?></td>
                            <td><?= htmlspecialchars($item['category']) ?></td>
                            <td><img src="<?= htmlspecialchars($item['image']) ?>" class="menu-thumbnail"></td>
                            <td>
                                <a href="edit_item.php?id=<?= $item['_id'] ?>" class="menu-action edit">Edit</a>
                                <a href="delete_item.php?id=<?= $item['_id'] ?>" class="menu-action delete" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</section>

</body>
</html>
