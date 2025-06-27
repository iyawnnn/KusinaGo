<?php
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
?>

<header class="header">
    <h2>Food Ordering System</h2>
    <div>
        <?php if ($loggedInUser): ?>
            👤 Hi, <?= htmlspecialchars($loggedInUser) ?> 
            <a class="login-btn" href="cart.php">🛒 Cart (<?= $cartCount ?>)</a> 
            <a class="login-btn" href="user_orders.php">🧾 Order History</a> 
            <a class="login-btn" href="logout.php">Logout</a>
        <?php elseif ($loggedInAdmin): ?>
            👤 Hi, Admin
            <a class="login-btn" href="admin_dashboard.php">Dashboard</a> 
            <a class="login-btn" href="admin_orders.php">🧾 Orders</a>
            <a class="login-btn" href="logout.php">Logout</a>
        <?php else: ?>
            <a class="login-btn" href="login.php">Login</a>
        <?php endif; ?>
    </div>
</header>
