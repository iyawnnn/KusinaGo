<?php
require_once __DIR__ . '/vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$adminName = $_SESSION['admin'];
$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->food_ordering;

$orders = $db->orders;
$menu = $db->menu;
$users = $db->users;
$inventory = $db->menu;

// Stats
$totalOrders = $orders->countDocuments();
$pendingOrders = $orders->countDocuments(['status' => 'Pending']);
$deliveredOrders = $orders->countDocuments(['status' => 'Delivered']);
$menuCount = $menu->countDocuments();
$customerCount = $users->countDocuments();

// Recent pending
$recentPending = $orders->find(
    ['status' => 'Pending'],
    ['sort' => ['ordered_at' => -1], 'limit' => 5]
);

// Low inventory (fixed: use 'stock')
$lowInventory = $inventory->find(['stock' => ['$lt' => 5]]);
$lowStockCount = $inventory->countDocuments(['stock' => ['$lt' => 5]]);

$cancelledOrders = $orders->countDocuments(['status' => 'Cancelled']);

// Revenue this month
$startOfMonth = date('Y-m-01 00:00:00');
$endOfMonth = date('Y-m-t 23:59:59');
$monthlyRevenueCursor = $orders->find([
    'status' => 'Delivered',
    'ordered_at' => [
        '$gte' => $startOfMonth,
        '$lte' => $endOfMonth
    ]
]);

$monthlyRevenue = 0;
foreach ($monthlyRevenueCursor as $order) {
    $monthlyRevenue += $order['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard | KusinaGo</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" href="uploads/favicon.svg">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</head>

<body>

    <?php include 'include/header.php'; ?>

    <main>
    <div class="kg-dashboard-wrapper">
        <div class="kg-dashboard-header">
            <h2 class="kg-dashboard-welcome">Welcome, <?= htmlspecialchars($adminName) ?></h2>
        </div>

        <div class="kg-dashboard-stats">
            <div class="kg-dashboard-card">
                <iconify-icon icon="ph:shopping-cart-bold"></iconify-icon>
                <div class="kg-dashboard-card-label">Total Orders</div>
                <div class="kg-dashboard-card-value"><?= $totalOrders ?></div>
            </div>
            <div class="kg-dashboard-card">
                <iconify-icon icon="mdi:clock-outline"></iconify-icon>
                <div class="kg-dashboard-card-label">Pending Orders</div>
                <div class="kg-dashboard-card-value"><?= $pendingOrders ?></div>
            </div>
            <div class="kg-dashboard-card">
                <iconify-icon icon="mdi:truck-delivery-outline"></iconify-icon>
                <div class="kg-dashboard-card-label">Delivered</div>
                <div class="kg-dashboard-card-value"><?= $deliveredOrders ?></div>
            </div>
            <div class="kg-dashboard-card">
                <iconify-icon icon="mdi:food"></iconify-icon>
                <div class="kg-dashboard-card-label">Menu Items</div>
                <div class="kg-dashboard-card-value"><?= $menuCount ?></div>
            </div>
            <div class="kg-dashboard-card">
                <iconify-icon icon="mdi:account-group-outline"></iconify-icon>
                <div class="kg-dashboard-card-label">Customers</div>
                <div class="kg-dashboard-card-value"><?= $customerCount ?></div>
            </div>
            <div class="kg-dashboard-card">
                <iconify-icon icon="mdi:cancel"></iconify-icon>
                <div class="kg-dashboard-card-label">Cancelled Orders</div>
                <div class="kg-dashboard-card-value"><?= $cancelledOrders ?></div>
            </div>
            <div class="kg-dashboard-card">
                <iconify-icon icon="mdi:cash-multiple"></iconify-icon>
                <div class="kg-dashboard-card-label">Revenue (This Month)</div>
                <div class="kg-dashboard-card-value">₱<?= number_format($monthlyRevenue, 2) ?></div>
            </div>
            <div class="kg-dashboard-card">
                <iconify-icon icon="mdi:alert-octagon-outline"></iconify-icon>
                <div class="kg-dashboard-card-label">Low Stock Items</div>
                <div class="kg-dashboard-card-value"><?= $lowStockCount ?></div>
            </div>
        </div>

        <div class="kg-dashboard-section">
            <h3 class="kg-dashboard-section-title">Recent Pending Orders</h3>
            <?php foreach ($recentPending as $order): ?>
                <div class="kg-dashboard-pending-order">
                    <span>Order #<?= $order['_id'] ?> — ₱<?= number_format($order['total'], 2) ?> — <?= $order['username'] ?></span>
                    <span><?= date('g:i A', strtotime($order['ordered_at'])) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="kg-dashboard-actions">
                <a href="admin_orders.php" class="kg-dashboard-btn">View All Orders</a>
            </div>
        </div>

        <div class="kg-dashboard-section">
            <h3 class="kg-dashboard-section-title">Low Inventory Alert</h3>

            <?php if ($lowStockCount > 0): ?>
                <?php foreach ($lowInventory as $item): ?>
                    <div class="kg-dashboard-low-stock">
                        <span><?= htmlspecialchars($item['name']) ?></span>
                        <span><?= $item['stock'] ?> left</span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="kg-dashboard-low-stock-message">
                    <span>All items are sufficiently stocked.</span>
                </div>
            <?php endif; ?>
        </div>


        <div class="kg-dashboard-section">
            <h3 class="kg-dashboard-section-title">Quick Actions</h3>
            <div class="kg-dashboard-actions">
                <a href="menu_list.php" class="kg-dashboard-btn">View Menu</a>
                <a href="add_item.php" class="kg-dashboard-btn">Add New Item</a>
                <a href="admin_report.php" class="kg-dashboard-btn">Sales Report</a>
                <a href="admin_users.php" class="kg-dashboard-btn">User Stats</a>
                <a href="admin_inventory.php" class="kg-dashboard-btn">Inventory</a>
            </div>
        </div>
    </div>

    <?php include 'include/footer_admin.php'; ?>

    </main>
    
</body>

</html>