<?php
session_start();
require 'vendor/autoload.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$ordersCollection = $client->food_ordering->orders;

// MongoDB aggregation to group and count
$pipeline = [
    ['$unwind' => '$items'],
    [
        '$group' => [
            '_id' => '$items.name',
            'total_quantity' => ['$sum' => '$items.quantity'],
            'total_sales' => ['$sum' => '$items.subtotal']
        ]
    ],
    ['$sort' => ['total_quantity' => -1]]
];

$report = $ordersCollection->aggregate($pipeline);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Sales Report</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'include/header.php'; ?>

<div class="container">
    <h2>📊 Sales Report</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>Item Name</th>
            <th>Total Sold</th>
            <th>Total Revenue (₱)</th>
        </tr>
        <?php foreach ($report as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row->_id) ?></td>
                <td><?= $row->total_quantity ?></td>
                <td>₱<?= number_format($row->total_sales, 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
