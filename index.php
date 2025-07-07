<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Home | KusinaGo</title>
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/responsive.css">
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
          width="10"
        >
          <path
            d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
            fill="currentColor"
          ></path>
        </svg>
        <svg
          viewBox="0 0 14 15"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
          class="hero-image-btn__icon-svg hero-image-btn__icon-svg--copy"
          width="10"
        >
          <path
            d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
            fill="currentColor"
          ></path>
        </svg>
      </span>
      Explore Menu
    </a>
  </div>
</section>

</body>
</html>
