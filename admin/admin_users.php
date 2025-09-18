<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->food_ordering;

$usersCursor = $db->users->find();
$users = iterator_to_array($usersCursor); // now $users is a reusable array
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <div class="page-wrapper">
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

                <!-- Mobile Card View -->
                <div class="customer-card-container">
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
                        <div class="customer-card">
                            <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
                            <p><strong>Total Orders:</strong> <?= $orderCount ?></p>
                            <p><strong>Total Spent:</strong> ₱<?= number_format($totalSpent, 2) ?></p>
                            <p>
                                <a href="admin_user_orders.php?username=<?= urlencode($username) ?>" class="menu-action view">View Orders</a>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </section>

        <?php include '../include/footer_admin.php'; ?>

    </div>

</body>

</html>