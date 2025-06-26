<?php
require __DIR__ . '/vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");

$db = $client->food_ordering;
$collection = $db->menu;

?>