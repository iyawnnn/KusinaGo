<?php
include 'admin_auth.php';
require __DIR__ . '/vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->food_ordering->menu;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = $_POST['name'];
    $price       = (int) $_POST['price'];
    $category    = $_POST['category'];
    $description = $_POST['description'];
    $image       = '';

    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imgName = basename($_FILES['image']['name']);
        $imgPath = 'uploads/' . $imgName;

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
            'name'        => $name,
            'price'       => $price,
            'category'    => $category,
            'description' => $description,
            'image'       => $image
        ]);
        $success = "Item added!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Menu Item | KusinaGo</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" href="uploads/favicon.svg">
</head>
<body>
<?php include 'include/header.php'; ?>

<section class="add-item-section">
    <div class="add-item-container">
        <h2 class="add-item-title">Add New Menu Item</h2>

        <?php if ($error): ?>
            <div class="form-message error"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="form-message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="add-item-form">
            <div class="input-group">
                <label for="name">Item Name</label>
                <input type="text" name="name" id="name" required>

                <label for="price">Price (₱)</label>
                <input type="number" name="price" id="price" required>

                <label for="category">Category</label>
                <select name="category" id="category" required class="category-select">
                    <option value="" disabled selected>Select Category</option>
                    <option value="Handa sa Hapág (Main Dishes)">Handa sa Hapág (Main Dishes)</option>
                    <option value="Panimula (Appetizers)">Panimula (Appetizers)</option>
                    <option value="Panghimagas (Desserts)">Panghimagas (Desserts)</option>
                    <option value="Pagpatid-Uhaw (Beverages)">Pagpatid-Uhaw (Beverages)</option>
                </select>

                <label for="description">Description</label>
                <textarea name="description" id="description" rows="3" required></textarea>
            </div>

            <label for="image">Upload Image</label>
            <div class="file-upload-form">
                <label class="file-upload-label" for="image">
                    <div class="file-upload-design">
                        <svg viewBox="0 0 640 512" height="1em">
                            <path d="M144 480C64.5 480 0 415.5 0 336c0-62.8 40.2-116.2 96.2-135.9c-.1-2.7-.2-5.4-.2-8.1
                            c0-88.4 71.6-160 160-160c59.3 0 111 32.2 138.7 80.2C409.9 102 428.3 96 448 96c53 0 96 43 96 96
                            c0 12.2-2.3 23.8-6.4 34.6C596 238.4 640 290.1 640 352c0 70.7-57.3 128-128 128H144zm79-217c-9.4
                            9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l39-39V392c0 13.3 10.7 24 24 24s24-10.7 24-24V257.9l39 39c9.4
                            9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-80-80c-9.4-9.4-24.6-9.4-33.9 0l-80 80z"/>
                        </svg>
                        <p>Upload Image here</p>
                        <span class="browse-button">Browse File</span>
                    </div>
                    <input type="file" name="image" id="image" accept="image/*" required>
                </label>
            </div>

            <div id="preview-container" class="image-preview hidden">
                <p>Selected Image Preview:</p>
                <img id="preview" src="#" alt="Image Preview">
            </div>

            <div id="upload-status" class="hidden">Uploading, please wait...</div>

            <div class="form-buttons">
                <button type="submit" class="primary-btn">Add Item</button>
            </div>
        </form>
    </div>
</section>

<script>
    const input = document.getElementById('image');
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('preview-container');
    const uploadStatus = document.getElementById('upload-status');
    const form = document.querySelector('.add-item-form');

    // Ensure both are hidden by default
    previewContainer.classList.add('hidden');
    uploadStatus.classList.add('hidden');

    input.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                previewContainer.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.classList.add('hidden');
        }
    });

    form.addEventListener('submit', function () {
        uploadStatus.classList.remove('hidden');
    });
</script>


</body>
</html>
