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
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" href="uploads/favicon.svg">
</head>
<body>

<?php include 'include/header.php'; ?>

<section class="menu-list-section">
    <div class="menu-list-inner">

        <div class="menu-list-header">
            <h2 class="menu-list-title">Manage Menu Items</h2>
            <div class="menu-list-actions">
                <a href="add_item.php" class="menu-btn add">Add New Item</a>
            </div>
        </div>

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
                                <button 
                                    class="menu-action delete" 
                                    onclick="openDeleteModal('<?= $item['_id'] ?>')"
                                >Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</section>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay hidden" id="deleteModal">
    <div class="modal">
        <h3>Confirm Deletion</h3>
        <p>Are you sure you want to delete this menu item? This action cannot be undone.</p>
        <div class="modal-buttons">
            <button class="cancel-btn" onclick="closeDeleteModal()">Cancel</button>
            <a href="#" id="confirmDeleteBtn" class="confirm-btn">Delete</a>
        </div>
    </div>
</div>

<script>
    function openDeleteModal(id) {
        const modal = document.getElementById('deleteModal');
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        confirmBtn.href = 'delete_item.php?id=' + id;
        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
    }

    // Close on ESC
    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDeleteModal();
    });
</script>

<?php include 'include/footer_admin.php'; ?>

</body>
</html>
