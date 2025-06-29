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
