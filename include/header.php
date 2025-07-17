<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

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
  $client = new MongoDB\Client("mongodb://localhost:27017");
  $ordersCollection = $client->food_ordering->orders;
  $pendingCount = $ordersCollection->countDocuments(['status' => 'Pending']);
}
?>

<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
<link rel="stylesheet" href="css/main.css">
<link rel="stylesheet" href="css/responsive.css">

<header class="header">
  <div class="header-container">

    <!-- Left Navigation -->
    <div class="nav-left">
      <?php if ($loggedInUser): ?>
        <a href="index.php">Home</a>
        <a href="menu.php">Menu</a>
      <?php elseif ($loggedInAdmin): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="menu_list.php">Menu</a>
        <a href="admin_orders.php" class="btn-badge">
          Orders
          <?php if ($pendingCount > 0): ?>
            <span class="badge"><?= $pendingCount ?></span>
          <?php endif; ?>
        </a>
      <?php endif; ?>
    </div>

    <!-- Center Logo -->
    <div class="nav-center">
      <?php $logoLink = $loggedInAdmin ? 'dashboard.php' : 'index.php'; ?>
      <a href="<?= $logoLink ?>">
        <img src="uploads/KusinoGo Logo.svg" alt="KusinaGo Logo" class="logo">
      </a>
    </div>

    <!-- Right Navigation -->
    <div class="nav-right">
      <?php if ($loggedInUser): ?>
        <a href="cart.php" class="btn-badge">
          Cart
          <?php if ($cartCount > 0): ?>
            <span class="badge cart-badge"><?= $cartCount ?></span>
          <?php endif; ?>
        </a>
        <a href="user_orders.php">My Orders</a>
      <?php elseif ($loggedInAdmin): ?>
        <a href="admin_inventory.php">Inventory</a>
        <a href="admin_report.php">Sales</a>
        <a href="admin_users.php">User Stats</a>
      <?php endif; ?>
    </div>

    <!-- Logout Icon -->
    <div class="nav-logout">
      <?php if ($loggedInUser || $loggedInAdmin): ?>
        <a href="logout.php" class="logout-icon" title="Logout">
          <iconify-icon icon="mdi:logout"></iconify-icon>
        </a>
      <?php else: ?>
        <a href="login.php">Login</a>
      <?php endif; ?>
    </div>

  </div>
</header>