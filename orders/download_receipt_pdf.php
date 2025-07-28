<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use MongoDB\Client;

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$client = new Client("mongodb://localhost:27017");
$ordersCollection = $client->food_ordering->orders;

// Get the latest order
$order = $ordersCollection->findOne(
    ['username' => $_SESSION['user']],
    ['sort' => ['ordered_at' => -1]]
);

if (!$order) {
    echo "❌ No order found.";
    exit;
}

// Start HTML for the receipt
$html = '<h2 style="text-align:center;">Order Receipt</h2>';
$html .= '<p><strong>Order ID:</strong> ' . $order['_id'] . '</p>';
$html .= '<p><strong>Date:</strong> ' . $order['ordered_at'] . '</p>';
$html .= '<p><strong>Status:</strong> ' . ($order['status'] ?? 'Pending') . '</p>';
$html .= '<hr><ul>';

foreach ($order['items'] as $item) {
    $html .= '<li>' . htmlspecialchars($item['name']) . ' — Qty: ' . $item['quantity'] . ' — $' . number_format($item['subtotal'], 2) . '</li>';
}

$html .= '</ul><hr>';
$html .= '<p><strong>Total:</strong> $' . number_format($order['total'], 2) . '</p>';

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("order_receipt.pdf", ["Attachment" => true]); // forces download
exit;
