<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

use MongoDB\BSON\ObjectId;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    try {
        $orderId = new ObjectId($_POST['order_id']);

        $client = new MongoDB\Client("mongodb://localhost:27017");
        $collection = $client->food_ordering->orders;

        // Check if the order belongs to user and is still pending
        $order = $collection->findOne([
            '_id' => $orderId,
            'username' => $_SESSION['user'],
            'status' => 'Pending'
        ]);

        if ($order) {
            $collection->updateOne(
                ['_id' => $orderId],
                ['$set' => ['status' => 'Cancelled']]
            );
        }
    } catch (Exception $e) {
        // Log or handle error
    }
}

header("Location: user_orders.php");
exit;
