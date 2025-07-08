<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$menuCollection = $client->food_ordering->menu;
$ordersCollection = $client->food_ordering->orders;

try {
  // Step 1: Aggregate total quantity sold per item
  $topSelling = $ordersCollection->aggregate([
    ['$unwind' => '$items'],
    ['$group' => [
      '_id' => '$items._id',  // ✅ Use '_id' from item object
      'totalSold' => ['$sum' => '$items.quantity']
    ]],
    ['$sort' => ['totalSold' => -1]],
    ['$limit' => 4]
  ])->toArray();

  // Step 2: Extract item IDs (as strings)
  $topItemIds = array_map(fn($doc) => $doc['_id'], $topSelling);

  // Step 3: Fetch menu items where _id matches (convert string to ObjectId)
  $objectIds = array_map(fn($id) => new MongoDB\BSON\ObjectId($id), $topItemIds);

  $featuredItems = $menuCollection->find([
    '_id' => ['$in' => $objectIds]
  ])->toArray();
} catch (Exception $e) {
  $featuredItems = []; // fallback
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Home | KusinaGo</title>
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/responsive.css">
  <link rel="icon" href="uploads/favicon.svg">
</head>

<body>

  <?php include 'include/header.php'; ?>

  <section class="home-hero">
    <div class="hero-img-wrapper">
      <img src="uploads/header.svg" alt="Luxury Filipino Dish">

      <a href="menu.php" class="hero-image-btn">
        <span class="hero-image-btn__icon-wrapper">
          <svg
            viewBox="0 0 14 15"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            class="hero-image-btn__icon-svg"
            width="10">
            <path
              d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
              fill="currentColor"></path>
          </svg>
          <svg
            viewBox="0 0 14 15"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            class="hero-image-btn__icon-svg hero-image-btn__icon-svg--copy"
            width="10">
            <path
              d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
              fill="currentColor"></path>
          </svg>
        </span>
        Explore Menu
      </a>
    </div>
  </section>

  <hr class="section-divider">

  <section class="featured-section">
    <h2 class="section-heading"><span>Best Sellers</span></h2>
    <div class="featured-container">
      <?php foreach ($featuredItems as $item): ?>
        <div class="featured-item">
          <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="featured-img">
          <div class="featured-info">
            <span class="category"><?= htmlspecialchars($item['category'] ?? 'Uncategorized') ?></span>
            <h3><?= htmlspecialchars($item['name']) ?></h3>
            <p>₱<?= htmlspecialchars($item['price']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="our-story-section">
  <div class="our-story-container">
    <div class="story-text">
      <h2 class="section-heading">Our Story</h2>
      <p>
        Born from the heart of Filipino tradition and elevated with refined taste, <strong>KusinaGo</strong> blends cultural heritage with culinary excellence. Each dish tells a story — a journey from our family kitchen to your elegant table. 
      </p>
      <p>
        With hand-selected ingredients, artisanal techniques, and a commitment to timeless flavor, we invite you to experience Filipino cuisine like never before — reimagined with sophistication and soul.
      </p>
    </div>
    <div class="story-image">
      <img src="uploads/story-plate.png" alt="Elegant Filipino Dish">
    </div>
  </div>
</section>


</body>

</html>