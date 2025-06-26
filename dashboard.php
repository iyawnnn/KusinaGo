<?php include 'admin_auth.php'; ?>


<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Welcome, <?= htmlspecialchars($_SESSION['admin']) ?></h2>
    <p>Choose an action:</p>

    <a href="menu_list.php">View Menu Items</a><br>
    <a href="add_item.php">Add New Item</a><br>
    <!-- You can add this once we build it -->
    <!-- <a href="orders_list.php">📦 View Orders</a><br> -->
    <a href="logout.php">Logout</a>
</body>
</html>
