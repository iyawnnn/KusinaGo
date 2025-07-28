<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Summary | KusinaGo</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <link rel="icon" href="../assets/icons/favicon.svg">
</head>

<body>

    <?php include '../include/header.php'; ?>

    <section class="menu-list-section">
        <div class="menu-list-inner">
            <!-- Header -->
            <div class="menu-list-header">
                <h2 class="menu-list-title">Customer Summary</h2>
            </div>

            <!-- Table -->
            <div class="menu-table-container">
                <table class="menu-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Total Orders</th>
                            <th>Total Spent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user):
                            $username = $user['username'];
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
                                    <a href="admin_user_orders.php?username=<?= urlencode($username) ?>" class="menu-action view">View Orders</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <?php include '../include/footer_admin.php'; ?>

</body>

</html>