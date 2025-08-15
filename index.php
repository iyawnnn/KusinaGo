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
      '_id' => '$items._id',
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
  <link rel="icon" href="assets/icons/favicon.svg">
  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</head>

<body>

  <?php include 'include/header.php'; ?>

  <section class="home-hero">
    <div class="hero-img-wrapper">
      <img src="<?= UPLOADS_PATH ?>hero-picture.svg" alt="Luxury Filipino Dish">

      <div class="hero-text">
        <h1 class="hero-title">
          Savor Luxury,<br>
          Filipino Style
        </h1>
        <p class="hero-subtitle">
          Where timeless Filipino flavors meet modern elegance — thoughtfully crafted from the finest ingredients and delivered to your table for an unforgettable dining experience.
        </p>
      </div>


      <a href="menu/menu.php" class="hero-image-btn">
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

  <section class="heritage-section">
    <div class="heritage-container">
      <!-- Left: Image -->
      <div class="heritage-image">
        <img src="assets/uploads/about-section.svg" alt="KusinaGo Kitchen">
      </div>

      <!-- Right: Text -->
      <div class="heritage-text">
        <h2>Where Home Meets Flavor</h2>
        <p>
          From our humble beginnings, KusinaGo has been about more than just food — it's about
          keeping the warmth of the Filipino home alive. Every dish carries the taste of
          cherished family recipes, prepared with fresh ingredients and a heart for tradition.
          Whether it's a quick solo meal or a spread for loved ones, every bite is made to feel
          like home.
        </p>
      </div>
    </div>
  </section>


  <section class="featured-section">
    <h2 class="section-heading"><span>Where Every Bite Wins Hearts</span></h2>
    <div class="featured-container">
      <?php
      $count = 0;
      foreach ($featuredItems as $item):
        if ($count >= 3) break;
      ?>
        <div class="featured-item">
          <img src="assets/item-pictures/<?= htmlspecialchars($item['image']) ?>"
            alt="<?= htmlspecialchars($item['name']) ?>"
            class="featured-img">
          <div class="featured-info">
            <h3><?= htmlspecialchars($item['name']) ?></h3>
            <span class="category"><?= htmlspecialchars($item['category'] ?? 'Uncategorized') ?></span>
          </div>
        </div>
      <?php
        $count++;
      endforeach;
      ?>
    </div>

    <div class="menu-btn-container">
      <a href="menu/menu.php" class="menu-btn">SEE WHAT'S ON THE TABLE</a>
    </div>
  </section>

<section class="testimonials-section">
  <div class="testimonials-container">
    <h2 class="testimonial-heading">What our customers say</h2>
    <div class="testimonial-cards">

      <div class="testimonial">
        <img src="assets/uploads/customer1.svg" alt="Customer 1" class="profile-pic-illustration">
        <p class="testimonial-text">
          "It turned an ordinary dinner into something special. Every dish had rich flavors, refined details, and flawless presentation."
        </p>
        <p class="customer-name">- Adrian Velasco</p>
      </div>

      <div class="testimonial">
        <img src="assets/uploads/customer2.svg" alt="Customer 2" class="profile-pic-illustration">
        <p class="testimonial-text">
          "Each order feels like it's been crafted just for me. The flavors are authentic yet refined, and the presentation always exceeds expectations."
        </p>
        <p class="customer-name">- Clarisse Tan</p>
      </div>

      <div class="testimonial">
        <img src="assets/uploads/customer3.svg" alt="Customer 3" class="profile-pic-illustration">
        <p class="testimonial-text">
          "From the first bite to the last, every dish was a delight. KusinaGo blends tradition and elegance in a way that makes dining at home feel indulgent."
        </p>
        <p class="customer-name">- Lorenzo Villanueva</p>
      </div>

    </div>
  </div>
</section>


</body>

<?php include 'include/footer.php'; ?>

</html>