<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$loggedInUser = $_SESSION['user'] ?? null;
$loggedInAdmin = $_SESSION['admin'] ?? null;

//
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}

// 
$pendingCount = 0;
if ($loggedInAdmin) {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $ordersCollection = $client->food_ordering->orders;
    $pendingCount = $ordersCollection->countDocuments(['status' => 'Pending']);
}
?>

<link rel="stylesheet" href="../css/style.css">
<header class="header">
    <h2>🍽️ Food Ordering System</h2>
    <div>
        <?php if ($loggedInUser): ?>
            👤 Hi, <?= htmlspecialchars($loggedInUser) ?>
            <a class="login-btn" href="index.php">🏠 Menu</a>
            <a class="login-btn" href="cart.php">🛒 Cart (<?= $cartCount ?>)</a>
            <a class="login-btn" href="user_orders.php">🧾 My Orders</a>
            <a class="login-btn" href="logout.php">Logout</a>

            <?php
            // Count pending orders (only for admin)
            $pendingCount = 0;
            if ($loggedInAdmin) {
                $client = new MongoDB\Client("mongodb://localhost:27017");
                $ordersCollection = $client->food_ordering->orders;
                $pendingCount = $ordersCollection->countDocuments(['status' => 'Pending']);
            }
            ?>

        <?php elseif ($loggedInAdmin): ?>
            👤 Hi, Admin
            <a class="login-btn" href="dashboard.php">📊 Dashboard</a>
            <a class="login-btn btn-badge" href="admin_orders.php">
                🧾 Orders
                <?php if ($pendingCount > 0): ?>
                    <span class="badge"><?= $pendingCount ?></span>
                <?php endif; ?>
            </a>
            <a class="login-btn" href="admin_sales_report.php">📈 Sales</a>
            <a class="login-btn" href="logout.php">Logout</a>

        <?php else: ?>
            <a class="login-btn" href="login.php">🔐 Login</a>
        <?php endif; ?>
    </div>
</header>