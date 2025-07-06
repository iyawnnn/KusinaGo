<?php
session_start();
require 'vendor/autoload.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->food_ordering->orders;

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $orderId = new MongoDB\BSON\ObjectId($_POST['order_id']);
    $collection->updateOne(
        ['_id' => $orderId],
        ['$set' => ['status' => $_POST['new_status']]]
    );
    header("Location: admin_orders.php?status=" . ($_GET['status'] ?? ''));
    exit;
}

// ✅ Get selected filter
$statusFilter = $_GET['status'] ?? 'All';

// ✅ Build filter query
$filter = [];
if ($statusFilter !== 'All') {
    $filter['status'] = $statusFilter;
}

$orders = $collection->find($filter, ['sort' => ['ordered_at' => -1]]);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Orders</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .status-Pending {
            color: orange;
            font-weight: bold;
        }

        .status-Processed {
            color: blue;
            font-weight: bold;
        }

        .status-Delivered {
            color: green;
            font-weight: bold;
        }

        .status-Cancelled {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <?php include 'include/header.php'; ?>

    <div class="container">


        <!-- 🔍 Filter Form -->
        <form method="get" style="margin-bottom: 20px;">
            <label for="status">Filter by Status:</label>
            <select name="status" id="status" onchange="this.form.submit()">
                <?php
                $statuses = ['All', 'Pending', 'Processed', 'Delivered', 'Cancelled'];
                foreach ($statuses as $status) {
                    $selected = ($status === $statusFilter) ? 'selected' : '';
                    echo "<option value=\"$status\" $selected>$status</option>";
                }
                ?>
            </select>
        </form>

        <?php foreach ($orders as $order): ?>
            <div class="order-box">
                <p>🆔 Order ID: <?= $order['_id'] ?></p>
                <p>👤 Username: <?= htmlspecialchars($order['username']) ?></p>
                <p>🕒 Ordered on: <?= $order['ordered_at'] ?></p>
                <p>💳 Payment: <?= $order['payment_method'] ?? 'N/A' ?></p>

                <?php
                $status = $order['status'] ?? 'Pending';
                echo "<p>📦 Status: <span class='status-$status'>$status</span></p>";
                ?>

                <ul>
                    <?php foreach ($order['items'] as $item): ?>
                        <li><?= htmlspecialchars($item['name']) ?> - Qty: <?= $item['quantity'] ?> - ₱<?= $item['subtotal'] ?></li>
                    <?php endforeach; ?>
                </ul>
                <p><strong>Total: ₱<?= $order['total'] ?></strong></p>

                <form method="post" style="margin-top: 10px;">
                    <input type="hidden" name="order_id" value="<?= $order['_id'] ?>">
                    <select name="new_status" required>
                        <option value="">-- Update Status --</option>
                        <option value="Processed">✅ Mark as Processed</option>
                        <option value="Delivered">🚚 Mark as Delivered</option>
                        <option value="Cancelled">❌ Cancel</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
                <hr>
            </div>
        <?php endforeach; ?>
    </div>

</body>

</html>