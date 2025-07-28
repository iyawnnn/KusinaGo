<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../config.php';

$loggedInUser = $_SESSION['user'] ?? null;
$loggedInAdmin = $_SESSION['admin'] ?? null;

$cartCount = 0;
if (isset($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $item) {
    $cartCount += $item['quantity'];
  }
}

$pendingCount = 0;
if ($loggedInAdmin) {
  $ordersCollection = $db->orders;
  $pendingCount = $ordersCollection->countDocuments(['status' => 'Pending']);
}
?>

<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
<link rel="stylesheet" href="<?= CSS_PATH ?>main.css">
<link rel="stylesheet" href="<?= CSS_PATH ?>responsive.css">

<header class="header">
  <div class="header-container">

    <!-- Left Navigation -->
    <div class="nav-left">
      <?php if ($loggedInUser): ?>
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <a href="<?= BASE_URL ?>menu/menu.php">Menu</a>
      <?php elseif ($loggedInAdmin): ?>
        <a href="<?= BASE_URL ?>admin/dashboard.php">Dashboard</a>
        <a href="<?= BASE_URL ?>admin/menu_list.php">Menu</a>
        <a href="<?= BASE_URL ?>admin/admin_orders.php" class="btn-badge">
          Orders
          <?php if ($pendingCount > 0): ?>
            <span class="badge"><?= $pendingCount ?></span>
          <?php endif; ?>
        </a>
      <?php endif; ?>
    </div>

    <!-- Center Logo -->
    <div class="nav-center">
      <?php $logoLink = $loggedInAdmin ? 'admin/dashboard.php' : 'index.php'; ?>
      <a href="<?= BASE_URL . $logoLink ?>">
        <img src="<?= ICON_PATH ?>KusinaGo-Logo.svg" alt="KusinaGo Logo" class="logo">
      </a>
    </div>

    <!-- Right Navigation -->
    <div class="nav-right">
      <?php if ($loggedInUser): ?>
        <a href="<?= BASE_URL ?>cart/cart.php" class="btn-badge">
          Cart
          <?php if ($cartCount > 0): ?>
            <span class="badge cart-badge"><?= $cartCount ?></span>
          <?php endif; ?>
        </a>
        <a href="<?= BASE_URL ?>orders/user_orders.php">My Orders</a>
      <?php elseif ($loggedInAdmin): ?>
        <a href="<?= BASE_URL ?>admin/admin_inventory.php">Inventory</a>
        <a href="<?= BASE_URL ?>admin/admin_report.php">Sales</a>
        <a href="<?= BASE_URL ?>admin/admin_users.php">User Stats</a>
      <?php endif; ?>
    </div>

    <!-- Logout Icon -->
    <div class="nav-logout">
      <?php if ($loggedInUser || $loggedInAdmin): ?>
        <a href="<?= BASE_URL ?>auth/logout.php" class="logout-icon" title="Logout">
          <iconify-icon icon="mdi:logout"></iconify-icon>
        </a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>auth/login.php">Login</a>
      <?php endif; ?>
    </div>

  </div>
</header>
