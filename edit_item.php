<?php
include 'admin_auth.php';

require __DIR__ . '/vendor/autoload.php';
$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->food_ordering->menu;

$id = $_GET['id'] ?? '';
if (!$id) {
    header("Location: menu_list.php");
    exit;
}

use MongoDB\BSON\ObjectId;

$item = $collection->findOne(['_id' => new ObjectId($id)]);
if (!$item) {
    echo "Item not found.";
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = (int) $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $image = $item['image'];

    // Handle new image upload
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imgName = basename($_FILES['image']['name']);
        $newPath = 'uploads/' . $imgName;

        // If it's not already the same file
        if ($item['image'] !== $newPath) {
            // Optional: remove newPath if it's already in uploads/ to prevent exact duplicates
            if (file_exists($newPath)) {
                // File with same name already exists; don't re-upload or delete anything
                $image = $newPath;
            } else {
                // Move new uploaded file
                if (move_uploaded_file($_FILES['image']['tmp_name'], $newPath)) {
                    $image = $newPath;
                    // Only delete old image if it's a different file and exists
                    if (!empty($item['image']) && file_exists($item['image'])) {
                        unlink($item['image']);
                    }
                } else {
                    $error = "❌ Failed to upload image.";
                }
            }
        } else {
            // File name is the same, so don't change image
            $image = $item['image'];
        }
    }


    if (!$error) {
        $collection->updateOne(
            ['_id' => new ObjectId($id)],
            ['$set' => [
                'name'     => $name,
                'price'    => $price,
                'category' => $category,
                'description'=> $description,
                'image'    => $image
            ]]
        );
        $success = "Item updated!";
        // Refresh item data
        $item = $collection->findOne(['_id' => new ObjectId($id)]);
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Item</title>
</head>

<body>
    <h2>Edit Food Item</h2>
    <a href="menu_list.php">Back to Menu</a><br><br>

    <?php if ($success): ?>
        <p style="color:green"><?= $success ?></p>
    <?php elseif ($error): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required><br><br>

        <label>Price (₱):</label><br>
        <input type="number" name="price" value="<?= htmlspecialchars($item['price']) ?>" required><br><br>

        <label>Category:</label><br>
        <input type="text" name="category" value="<?= htmlspecialchars($item['category']) ?>" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" rows="3" required><?= htmlspecialchars($item['description'] ?? '') ?></textarea><br><br>

        <label>Change Image (optional):</label><br>
        <input type="file" name="image" accept="image/*"><br>
        <small>Current:</small><br>
        <img src="<?= $item['image'] ?>" width="120"><br><br>

        <button type="submit">Update Item</button>
    </form>
</body>

</html>