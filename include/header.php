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

    <!-- Left: Logo -->
    <div class="nav-logo">
      <?php $logoLink = $loggedInAdmin ? 'admin/dashboard.php' : 'index.php'; ?>
      <a href="<?= BASE_URL . $logoLink ?>">
        <img src="<?= ICON_PATH ?>KusinaGo-Logo.svg" alt="KusinaGo Logo" class="logo">
      </a>
    </div>

    <!-- Hamburger Button (mobile only) -->
    <button class="hamburger" aria-label="Menu" aria-expanded="false">
      <span></span>
      <span></span>
      <span></span>
    </button>

    <div class="nav-links">
      <?php if ($loggedInUser): ?>
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <a href="<?= BASE_URL ?>menu/menu.php">Menu</a>
        <a href="<?= BASE_URL ?>cart/cart.php" class="btn-badge">
          Cart
          <span class="badge cart-badge" id="cart-count"><?= $cartCount ?></span>
        </a>
        <a href="<?= BASE_URL ?>orders/user_orders.php">My Orders</a>

        <!-- Mobile-only logout -->
        <a href="<?= BASE_URL ?>auth/logout.php" class="mobile-logout">Logout</a>

      <?php elseif ($loggedInAdmin): ?>
        <a href="<?= BASE_URL ?>admin/dashboard.php">Dashboard</a>
        <a href="<?= BASE_URL ?>menu/menu_list.php">Menu</a>
        <a href="<?= BASE_URL ?>admin/admin_orders.php" class="btn-badge">
          Orders
          <?php if ($pendingCount > 0): ?>
            <span class="badge"><?= $pendingCount ?></span>
          <?php endif; ?>
        </a>
        <a href="<?= BASE_URL ?>admin/admin_inventory.php">Inventory</a>
        <a href="<?= BASE_URL ?>admin/admin_report.php">Sales</a>
        <a href="<?= BASE_URL ?>admin/admin_users.php">User Stats</a>

        <!-- Mobile-only logout -->
        <a href="<?= BASE_URL ?>auth/logout.php" class="mobile-logout">Logout</a>

      <?php else: ?>
        <a href="<?= BASE_URL ?>auth/login.php">Login</a>
      <?php endif; ?>
    </div>

    <!-- Right: Logout (desktop only) -->
    <div class="nav-logout">
      <?php if ($loggedInUser || $loggedInAdmin): ?>
        <a href="<?= BASE_URL ?>auth/logout.php" class="logout-icon" title="Logout">
          <iconify-icon icon="mdi:logout"></iconify-icon>
        </a>
      <?php endif; ?>
    </div>

  </div>
</header>

<script>
  const hamburger = document.querySelector('.hamburger');
  const navLinks = document.querySelector('.nav-links');

  hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('open');
    const expanded = hamburger.getAttribute('aria-expanded') === 'true' || false;
    hamburger.setAttribute('aria-expanded', !expanded);
  });
</script>