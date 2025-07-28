<?php
require 'vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->food_ordering;

$folderName = 'FoodOrderingSystem';
$basePath = '/' . $folderName . '/';

define('BASE_URL', $basePath);
define('CSS_PATH', BASE_URL . 'css/');
define('IMG_PATH', BASE_URL . 'assets/');
define('ICON_PATH', BASE_URL . 'assets/icons/');
define('UPLOADS_PATH', BASE_URL . 'assets/uploads/');
?>
