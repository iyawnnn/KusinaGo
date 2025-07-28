<?php
require __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use MongoDB\Client;

// Connect to MongoDB
$client = new Client("mongodb://localhost:27017");
$ordersCollection = $client->food_ordering->orders;

// Fetch all delivered orders
$orders = $ordersCollection->find([
    'status' => 'Delivered'
]);

// Build HTML content
$html = '<h2 style="text-align:center;">Sales Report</h2>';
$html .= '<table border="1" cellpadding="10" cellspacing="0" width="100%">';
$html .= '<tr><th>Order ID</th><th>User</th><th>Ordered On</th><th>Total ($)</th></tr>';

$totalSales = 0;

foreach ($orders as $order) {
    $html .= '<tr>';
    $html .= '<td>' . $order['_id'] . '</td>';
    $html .= '<td>' . htmlspecialchars($order['username']) . '</td>';
    $html .= '<td>' . $order['ordered_at'] . '</td>';
    $html .= '<td>' . number_format($order['total'], 2) . '</td>';
    $html .= '</tr>';

    $totalSales += $order['total'];
}

$html .= '<tr><td colspan="3"><strong>Total Sales</strong></td>';
$html .= '<td><strong>$' . number_format($totalSales, 2) . '</strong></td></tr>';
$html .= '</table>';

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Download PDF
$dompdf->stream("sales_report_" . date("Ymd") . ".pdf", ["Attachment" => 1]);
exit;
