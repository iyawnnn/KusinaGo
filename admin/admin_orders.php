<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

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
    $statusBack = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : 'All';
    header("Location: admin_orders.php?status=" . urlencode($statusBack));
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
    <title>Orders | KusinaGo</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <link rel="icon" href="../assets/icons/favicon.svg">
</head>

<body>

    <?php include '../include/header.php'; ?>

    <main>
    <div class="kg-orders-wrapper">

        <!-- Filter -->
        <form method="get" class="kg-orders-filter-form">
            <label for="status" class="kg-orders-filter-label">Filter by Status:</label>
            <select name="status" id="status" onchange="this.form.submit()" class="kg-orders-filter-select">
                <?php
                $statuses = ['All', 'Pending', 'Processed', 'Delivered', 'Cancelled'];
                foreach ($statuses as $status) {
                    $selected = ($status === $statusFilter) ? 'selected' : '';
                    echo "<option value=\"$status\" $selected>$status</option>";
                }
                ?>
            </select>
        </form>

        <!-- Orders List -->
        <?php foreach ($orders as $order): ?>
            <div class="kg-order-card">
                <p><strong>Order ID:</strong> <?= $order['_id'] ?></p>
                <p><strong>Customer:</strong> <?= htmlspecialchars($order['username']) ?></p>
                <p><strong>Ordered on:</strong> <?= $order['ordered_at'] ?></p>
                <p><strong>Payment:</strong> <?= $order['payment_method'] ?? 'N/A' ?></p>

                <p>
                    <strong>Status:</strong>
                    <span class="kg-status-tag kg-status-<?= $order['status'] ?? 'Pending' ?>">
                        <?= $order['status'] ?? 'Pending' ?>
                    </span>
                </p>

                <ul class="kg-order-items">
                    <?php foreach ($order['items'] as $item): ?>
                        <li><?= htmlspecialchars($item['name']) ?> - Qty: <?= $item['quantity'] ?> - ₱<?= number_format($item['subtotal'], 2) ?></li>
                    <?php endforeach; ?>
                </ul>

                <p class="kg-order-total"><strong>Total:</strong> ₱<?= number_format($order['total'], 2) ?></p>

                <form method="post" class="kg-order-status-form">
                    <input type="hidden" name="order_id" value="<?= $order['_id'] ?>">
                    <select name="new_status" required class="kg-status-select">
                        <option value="">-- Update Status --</option>
                        <option value="Processed">Mark as Processed</option>
                        <option value="Delivered">Mark as Delivered</option>
                        <option value="Cancelled">Cancel</option>
                    </select>
                    <button type="submit" class="kg-status-btn">Update</button>
                </form>
            </div>
        <?php endforeach; ?>

    </div>
    </main>

    <?php include '../include/footer_admin.php'; ?>
</body>

</html>