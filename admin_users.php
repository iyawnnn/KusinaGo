<?php
session_start();
require 'vendor/autoload.php';

// Redirect non-admins
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->food_ordering;

$users = $db->users->find();
$orders = $db->orders;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Users</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'include/header.php'; ?>

<div class="container">
    <h2>👥 Customer Summary</h2>

    <table border="1" cellpadding="8">
        <tr>
            <th>Username</th>
            <th>Total Orders</th>
            <th>Total Spent</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($users as $user): 
            $username = $user['username'];

            // Find this user's orders
            $userOrders = $orders->find(['username' => $username]);

            $orderCount = 0;
            $totalSpent = 0;

            foreach ($userOrders as $order) {
                $orderCount++;
                $totalSpent += $order['total'];
            }
        ?>
            <tr>
                <td><?= htmlspecialchars($username) ?></td>
                <td><?= $orderCount ?></td>
                <td>₱<?= number_format($totalSpent, 2) ?></td>
                <td>
                    <a href="admin_user_orders.php?username=<?= urlencode($username) ?>">📋 View Orders</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
