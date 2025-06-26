<?php
include 'admin_auth.php';

require __DIR__ . '/vendor/autoload.php';
$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->food_ordering->menu;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'];
    $price    = (int) $_POST['price'];
    $category = $_POST['category'];
    $image    = '';

    // Handle image upload
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imgName = basename($_FILES['image']['name']);
        $imgPath = 'uploads/' . $imgName;

        // If the exact file already exists, just reuse it
        if (file_exists($imgPath)) {
            $image = $imgPath;
        } else {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $imgPath)) {
                $image = $imgPath;
            } else {
                $error = "Failed to upload image.";
            }
        }
    }

    if (!$error) {
        $collection->insertOne([
            'name'     => $name,
            'price'    => $price,
            'category' => $category,
            'image'    => $image
        ]);
        $success = "Item added!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Menu Item</title>
</head>

<body>
    <h2>Add New Food Item</h2>
    <a href="menu_list.php">Back to Menu List</a>
    <br><br>

    <?php if ($error): ?>
        <p style="color:red"><?= $error ?></p>
    <?php elseif ($success): ?>
        <p style="color:green"><?= $success ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Name:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Price (₱):</label><br>
        <input type="number" name="price" required><br><br>

        <label>Category:</label><br>
        <input type="text" name="category" required><br><br>

        <label>Image:</label><br>
        <input type="file" name="image" accept="image/*" required><br><br>

        <button type="submit">Add Item</button>
    </form>
</body>

</html>