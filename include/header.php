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

<link rel="stylesheet" href="css/main.css">
<link rel="stylesheet" href="css/responsive.css">
<header class="header">
  <div class="header-container">
    <!-- Left Nav -->
    <div class="nav-left">
      <?php if ($loggedInUser): ?>
        <a href="menu.php">Menu</a>
        <a class="login-btn btn-badge" href="cart.php">
          Cart
          <?php if ($cartCount > 0): ?>
            <span class="badge cart-badge"><?= $cartCount ?></span>
          <?php endif; ?>
        </a>
      <?php elseif ($loggedInAdmin): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="admin_orders.php" class="btn-badge">
          Orders
          <?php if ($pendingCount > 0): ?>
            <span class="badge"><?= $pendingCount ?></span>
          <?php endif; ?>
        </a>
      <?php endif; ?>
    </div>

    <!-- Logo -->
    <div class="nav-center">
      <a href="index.php">
        <img src="uploads/KusinoGo Logo.svg" alt="KusinaGo Logo" class="logo">
      </a>
    </div>

    <!-- Right Nav -->
    <div class="nav-right">
      <?php if ($loggedInUser): ?>
        <a href="user_orders.php">My Orders</a>
        <a href="logout.php">Logout</a>
      <?php elseif ($loggedInAdmin): ?>
        <a href="admin_report.php">Sales</a>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
      <?php endif; ?>
    </div>
  </div>
</header>