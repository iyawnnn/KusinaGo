<?php
session_start();
require 'vendor/autoload.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$client = new MongoDB\Client("mongodb://localhost:27017");
$ordersCollection = $client->food_ordering->orders;

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
    <title>Sales Report | KusinaGo</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" href="uploads/favicon.svg">
</head>

<body>

    <?php include 'include/header.php'; ?>

    <main>
        <section class="report-section">
            <h2 class="report-heading">Sales Report</h2>

            <div class="table-container">
                <table class="sales-table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Total Sold</th>
                            <th>Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row->_id) ?></td>
                                <td><?= $row->total_quantity ?></td>
                                <td>₱<?= number_format($row->total_sales, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="report-footer">
                <a href="sales_report_pdf.php" class="download-btn">
                    <span class="download-text">Download PDF</span>
                    <span class="download-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 16 16" class="bi bi-download">
                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
                            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
                        </svg>
                    </span>
                </a>
            </div>
        </section>
    </main>

    <?php include 'include/footer_admin.php'; ?>

</body>

</html>