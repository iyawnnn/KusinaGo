<?php
require_once __DIR__ . '/vendor/autoload.php'; // ✅ Add this at the top

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$loggedInUser = $_SESSION['user'] ?? null;
$loggedInAdmin = $_SESSION['admin'] ?? null;

// Count total cart items
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}

// ✅ Always define $pendingCount (default to 0)
$pendingCount = 0;
if ($loggedInAdmin) {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $ordersCollection = $client->food_ordering->orders;
    $pendingCount = $ordersCollection->countDocuments(['status' => 'Pending']);
}
?>



<?php include 'admin_auth.php'; ?>


<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'include/header.php'; ?>
    <h2>Welcome, <?= htmlspecialchars($_SESSION['admin']) ?></h2>
    <p>Choose an action:</p>

    <a href="menu_list.php">View Menu Items</a><br>
    <a href="add_item.php">Add New Item</a><br>
    <a href="admin_view_orders.php" class="btn">View All Orders</a><br>
    <a href="admin_report.php" class="btn">Sales Report</a><br>
    <a href="admin_users.php" class="btn">View User Order Stats</a><br>
    <a href="admin_inventory.php" class="btn">View Inventory</a><br>
    <a href="logout.php">Logout</a><br>
</body>
</html>
