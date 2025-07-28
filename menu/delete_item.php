<?php
include 'admin_auth.php';

require __DIR__ . '/vendor/autoload.php';
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

$client = new Client("mongodb://localhost:27017");
$collection = $client->food_ordering->menu;

$id = $_GET['id'] ?? '';
if (!$id) {
    header("Location: menu_list.php");
    exit;
}

// No image deletion here — just delete the DB document
$collection->deleteOne(['_id' => new ObjectId($id)]);

header("Location: menu_list.php");
exit;
