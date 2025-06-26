<?php
include 'admin_auth.php';

require __DIR__ . '/vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->food_ordering->menu;

$items = $collection->find();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Menu Management</title>
</head>
<body>
    <h2>Food Menu</h2>
    <a href="add_item.php">Add New Item</a> |
    <a href="dashboard.php">Back to Dashboard</a> |
    <a href="logout.php">Logout</a>
    <br><br>

    <table border="1" cellpadding="8">
        <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td>₱<?= htmlspecialchars($item['price']) ?></td>
            <td><?= htmlspecialchars($item['category']) ?></td>
            <td><img src="<?= htmlspecialchars($item['image']) ?>" width="80"></td>
            <td>
                <a href="edit_item.php?id=<?= $item['_id'] ?>">Edit</a> |
                <a href="delete_item.php?id=<?= $item['_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
