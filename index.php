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
  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
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
        <h2>Our Story</h2>
        <p>
          KusinaGo was born from the belief that Filipino cuisine can be both traditional and timelessly elegant.
        </p>
        <p>
          Rooted in cherished family recipes and inspired by the art of fine dining, we bring each dish to life with a balance of warmth and sophistication.
        </p>
        <p>
          From the careful selection of local ingredients to the graceful presentation on your plate, every experience is curated to reflect not just our food — but our heritage, our passion, and our pride as Filipinos.
        </p>

      </div>
      <div class="story-image">
        <img src="uploads/Chef.jpg" alt="Elegant Filipino Dish">
      </div>
    </div>
  </section>

  <section class="how-it-works-section">
    <div class="how-it-works-container">
      <h2 class="section-heading">How It Works</h2>
      <div class="steps">

        <div class="step">
          <span class="step-icon">
            <iconify-icon icon="mdi:food-outline" width="48" height="48"></iconify-icon>
          </span>
          <h3>Curate Your Cravings</h3>
          <p>Discover a refined selection of Filipino cuisine — each dish a blend of tradition and culinary elegance.</p>
        </div>

        <div class="step">
          <span class="step-icon">
            <iconify-icon icon="mdi:clipboard-text-outline" width="48" height="48"></iconify-icon>
          </span>
          <h3>Effortless Ordering</h3>
          <p>With a few simple clicks, reserve your favorites. Our seamless process puts comfort and convenience first.</p>
        </div>

        <div class="step">
          <span class="step-icon">
            <iconify-icon icon="mdi:truck-delivery-outline" width="48" height="48"></iconify-icon>
          </span>
          <h3>Dine in Elegance</h3>
          <p>Your selections arrive carefully prepared and beautifully packed — ready to serve, savor, and celebrate.</p>
        </div>

      </div>
    </div>
  </section>

  <section class="testimonials-section">
    <h2 class="section-heading">What Our Customers Say</h2>
    <div class="testimonial-cards">

      <div class="testimonial">
        <img src="uploads/customer1.jpg" alt="Customer 1" class="profile-pic">
        <div class="customer-name">Isabella Cruz</div>
        <div class="customer-role">Food Blogger</div>
        <p>“From packaging to taste — KusinaGo delivered a fine dining experience right to our doorstep.”</p>
      </div>

      <div class="testimonial">
        <img src="uploads/customer2.webp" alt="Customer 2" class="profile-pic">
        <div class="customer-name">Trisha Dela Rosa</div>
        <div class="customer-role">Corporate Chef</div>
        <p>“Truly elegant. The balance of flavor and presentation is unmatched.”</p>
      </div>

      <div class="testimonial">
        <img src="uploads/customer3.jpg" alt="Customer 3" class="profile-pic">
        <div class="customer-name">Clarisse Tan</div>
        <div class="customer-role">Interior Designer</div>
        <p>“It's not just food — it's a full luxurious experience. Everything feels premium.”</p>
      </div>

    </div>
  </section>




</body>

</html>