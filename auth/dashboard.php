<?php
session_start();
require 'db.php';

// Récupérer les informations de l'utilisateur connecté
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT id, prenom, nom, email, role, adresse, telephone, dateinscription, notificationsActives, avatar FROM user WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['user_role'] = $user['role']; // Stocker le rôle dans la session
}

// Récupérer le terme de recherche
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Récupérer la catégorie et la sous-catégorie
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
$subcategory_id = isset($_GET['subcategory_id']) ? $_GET['subcategory_id'] : '';

// Construire la requête SQL en fonction du terme de recherche, de la catégorie et de la sous-catégorie
$sql = 'SELECT * FROM Article WHERE 1=1';
$params = [];

if (!empty($searchTerm)) {
    $sql .= ' AND title LIKE :searchTerm';
    $params['searchTerm'] = '%' . $searchTerm . '%';
}

if (!empty($category_id)) {
    $sql .= ' AND category_id = :category_id';
    $params['category_id'] = $category_id;
}

if (!empty($subcategory_id)) {
    $sql .= ' AND subcategory_id = :subcategory_id';
    $params['subcategory_id'] = $subcategory_id;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

// Fetch category and subcategory names for the message
$category_name = '';
$subcategory_name = '';

if (!empty($category_id)) {
    $stmt = $pdo->prepare("SELECT name FROM categorie WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    $category_name = $category['name'] ?? '';
}

if (!empty($subcategory_id)) {
    $stmt = $pdo->prepare("SELECT name FROM subcategorie WHERE id = ?");
    $stmt->execute([$subcategory_id]);
    $subcategory = $stmt->fetch(PDO::FETCH_ASSOC);
    $subcategory_name = $subcategory['name'] ?? '';
}

// Generate the no-results message
$no_results_message = '';
if (empty($articles)) {
    if (!empty($category_name) && !empty($subcategory_name)) {
        $no_results_message = "Aucun article trouvé pour '$category_name - $subcategory_name'";
    } elseif (!empty($category_name)) {
        $no_results_message = "Aucun article trouvé pour '$category_name'";
    } elseif (!empty($searchTerm)) {
        $no_results_message = "Aucun article trouvé pour '$searchTerm'";
    } else {
        $no_results_message = "Aucun article trouvé";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>baboucha - Sell & Buy Clothes</title>

    <!--
    - favicon
  -->
    <link rel="shortcut icon" href="./assets/images/logo/favicon.ico" type="image/x-icon">

    <!--
    - custom css link
  -->
    <link rel="stylesheet" href="./assets/css/style-prefix.css">
    <link rel="stylesheet" href="./assets/css/header.css">

    <!--
    - google font link
  -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <link rel="icon" href="img/icon.png">




    <!-- Link Swiper's CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">


</head>

<body>


    <div class="overlay" data-overlay></div>


    <!--
    - HEADER
  -->

    <header>


        <div class="header-main">

            <div class="container">

                <a href="?" class="header-logo">
                    <img src="./assets/images/logo/baboucha.png" alt="leboncoin's logo" width="120" height="33">
                </a>

                <a href="addarticle.php" class="btn"><i class="fa-regular fa-square-plus"></i>Déposer un article </a>
                <!-- Afficher le bouton "Manage" uniquement si l'utilisateur est un admin -->
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') : ?>
                <a href="manage.php" class="btn"><i class="fa-regular fa-square-plus"></i>Manage </a>
                <?php endif; ?>


                <div class="header-search-container">
                    <form action="" method="GET">

                        <input type="search" name="search" class="search-field" placeholder="Rechercher sur leboncoin">

                        <button type="submit" class="search-btn">
                            <i class="fa-solid fa-magnifying-glass"></i> </button>
                    </form>

                </div>

                <div class="header-user-actions">
                    <?php if (isset($_SESSION['user_id'])) : ?>
                    <div class="profile-dropdown">
                        <button class="action-btn profile-btn">
                            <ion-icon name="person-outline"></ion-icon>
                        </button>
                        <div class="dropdown-content">
                            <a href="profile.php">Profil</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                    <?php else : ?>
                    <a href="index.php" class="btn">Login</a>
                    <?php endif; ?>

                    <button class="action-btn" id="header-fav-btn" type="button">
                        <ion-icon name="heart-outline"></ion-icon>
                        <span class="count">0</span>
                    </button>
                </div>

            </div>

        </div>

        <nav class="desktop-navigation-menu">
            <div class="container">
                <ul class="desktop-menu-category-list">
                    <!-- Home Link -->
                    <li class="menu-category">
                        <a href="?" class="menu-title">Home</a> <!-- Reset filters by linking to the base URL -->
                    </li>

                    <!-- Hommes Category -->
                    <li class="menu-category">
                        <a href="#" class="menu-title">Hommes</a>
                        <ul class="dropdown-list">
                            <li class="dropdown-item">
                                <a href="?category_id=1&subcategory_id=3">Chemises</a>
                            </li>
                            <li class="dropdown-item">
                                <a href="?category_id=1&subcategory_id=1">Jeans</a>
                            </li>
                            <li class="dropdown-item">
                                <a href="?category_id=1&subcategory_id=2">Chaussures</a>
                            </li>
                            <li class="dropdown-item">
                                <a href="?category_id=1&subcategory_id=5">Vestes</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Femmes Category -->
                    <li class="menu-category">
                        <a href="#" class="menu-title">Femmes</a>
                        <ul class="dropdown-list">
                            <li class="dropdown-item">
                                <a href="?category_id=2&subcategory_id=8">Robes</a>
                            </li>
                            <li class="dropdown-item">
                                <a href="?category_id=2&subcategory_id=9">Jupes</a>
                            </li>
                            <li class="dropdown-item">
                                <a href="?category_id=2&subcategory_id=10">Blouses</a>
                            </li>
                            <li class="dropdown-item">
                                <a href="?category_id=2&subcategory_id=11">Accessoires</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Enfants Category -->
                    <li class="menu-category">
                        <a href="#" class="menu-title">Enfants</a>
                        <ul class="dropdown-list">
                            <li class="dropdown-item">
                                <a href="?category_id=3&subcategory_id=12">Vêtements</a>
                            </li>
                            <li class="dropdown-item">
                                <a href="?category_id=3&subcategory_id=13">Chaussures</a>
                            </li>
                            <li class="dropdown-item">
                                <a href="?category_id=3&subcategory_id=14">Jouets</a>
                            </li>
                            <li class="dropdown-item">
                                <a href="?category_id=3&subcategory_id=15">Accessoires</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="mobile-bottom-navigation">

            <button class="action-btn" data-mobile-menu-open-btn>
                <ion-icon name="menu-outline"></ion-icon>
            </button>

            <button class="action-btn">
                <ion-icon name="bag-handle-outline"></ion-icon>

                <span class="count">0</span>
            </button>

            <button class="action-btn">
                <ion-icon name="home-outline"></ion-icon>
            </button>

            <button class="action-btn">
                <ion-icon name="heart-outline"></ion-icon>

                <span class="count">0</span>
            </button>

            <button class="action-btn" data-mobile-menu-open-btn>
                <ion-icon name="grid-outline"></ion-icon>
            </button>

        </div>

        <nav class="mobile-navigation-menu  has-scrollbar" data-mobile-menu>

            <div class="menu-top">
                <h2 class="menu-title">Menu</h2>

                <button class="menu-close-btn" data-mobile-menu-close-btn>
                    <ion-icon name="close-outline"></ion-icon>
                </button>
            </div>

            <ul class="mobile-menu-category-list">

                <li class="menu-category">
                    <a href="#" class="menu-title">Home</a>
                </li>

                <li class="menu-category">

                    <button class="accordion-menu" data-accordion-btn>
                        <p class="menu-title">Hommes</p>

                        <div>
                            <ion-icon name="add-outline" class="add-icon"></ion-icon>
                            <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
                        </div>
                    </button>

                    <ul class="submenu-category-list" data-accordion>

                        <li class="submenu-category">
                            <a href="#" class="submenu-title">Shirt</a>
                        </li>

                        <li class="submenu-category">
                            <a href="#" class="submenu-title">Shorts & Jeans</a>
                        </li>

                        <li class="submenu-category">
                            <a href="#" class="submenu-title">Safety Shoes</a>
                        </li>

                        <li class="submenu-category">
                            <a href="#" class="submenu-title">Wallet</a>
                        </li>

                    </ul>

                </li>

                <li class="menu-category">

                    <button class="accordion-menu" data-accordion-btn>
                        <p class="menu-title">Femmes</p>

                        <div>
                            <ion-icon name="add-outline" class="add-icon"></ion-icon>
                            <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
                        </div>
                    </button>

                    <ul class="submenu-category-list" data-accordion>

                        <li class="submenu-category">
                            <a href="#" class="submenu-title">Dress & Frock</a>
                        </li>

                        <li class="submenu-category">
                            <a href="#" class="submenu-title">Earrings</a>
                        </li>

                        <li class="submenu-category">
                            <a href="#" class="submenu-title">Necklace</a>
                        </li>

                        <li class="submenu-category">
                            <a href="#" class="submenu-title">Makeup Kit</a>
                        </li>

                    </ul>

                </li>

                <li class="menu-category">

                    <button class="accordion-menu" data-accordion-btn>
                        <p class="menu-title">Enfants</p>

                        <div>
                            <ion-icon name="add-outline" class="add-icon"></ion-icon>
                            <ion-icon name="remove-outline" class="remove-icon"></ion-icon>
                        </div>
                    </button>

                    <ul class="submenu-category-list" data-accordion>

                        <li class="submenu-category">
                            <a href="#" class="submenu-title">Earrings</a>
                        </li>

                        <li class="submenu-category">
                            <a href="#" class="submenu-title">Couple Rings</a>
                        </li>

                        <li class="submenu-category">
                            <a href="#" class="submenu-title">Necklace</a>
                        </li>

                        <li class="submenu-category">
                            <a href="#" class="submenu-title">Bracelets</a>
                        </li>

                    </ul>

                </li>

            </ul>

        </nav>

    </header>





    <!--
    - MAIN
  -->

    <main>

        <!--
      - BANNER
    -->

        <div class="banner">

            <div class="container">

                <div class="slider-container has-scrollbar">

                    <div class="slider-item">

                        <img src="./assets/images/banner-1.jpg" alt="women's latest fashion sale" class="banner-img">

                        <div class="banner-content">

                            <p class="banner-subtitle">Article tendance</p>

                            <h2 class="banner-title">prix incroyables !</h2>

                            <p class="banner-text">
                                à partir de <b>0</b>.00 TND
                            </p>

                            <a href="addarticle.php" class="banner-btn">Déposer un article</a>

                        </div>

                    </div>

                    <div class="slider-item">

                        <img src="./assets/images/banner-2.jpg" alt="modern sunglasses" class="banner-img">

                        <div class="banner-content">

                            <p class="banner-subtitle">Accessoires tendance</p>

                            <h2 class="banner-title">Achetez malin, portez mieux</h2>

                            <p class="banner-text">
                                à partir de <b>0</b>.00 TND
                            </p>

                            <a href="addarticle.php" class="banner-btn">Déposer un article</a>

                        </div>

                    </div>

                    <div class="slider-item">

                        <img src="./assets/images/banner-3.jpg" alt="new fashion summer sale" class="banner-img">

                        <div class="banner-content">

                            <p class="banner-subtitle">Offre de vente</p>

                            <h2 class="banner-title">Dénichez des trésors</h2>

                            <p class="banner-text">
                                à partir de <b>0</b>.00 TND
                            </p>

                            <a href="addarticle.php" class="banner-btn">Déposer un article</a>

                        </div>

                    </div>

                </div>

            </div>

        </div>





        <!--
      - CATEGORY
    -->

        <div class="category">

            <div class="container">

                <div class="category-item-container has-scrollbar">

                    <div class="category-item">

                        <div class="category-img-box">
                            <img src="./assets/images/icons/dress.svg" alt="dress & frock" width="30">
                        </div>

                        <div class="category-content-box">

                            <div class="category-content-flex">
                                <h3 class="category-item-title">Dress & frockk</h3>

                                <p class="category-item-amount">(53)</p>
                            </div>

                            <a href="#" class="category-btn">Show all</a>

                        </div>

                    </div>

                    <div class="category-item">

                        <div class="category-img-box">
                            <img src="./assets/images/icons/coat.svg" alt="winter wear" width="30">
                        </div>

                        <div class="category-content-box">

                            <div class="category-content-flex">
                                <h3 class="category-item-title">Winter wear</h3>

                                <p class="category-item-amount">(58)</p>
                            </div>

                            <a href="#" class="category-btn">Show all</a>

                        </div>

                    </div>

                    <div class="category-item">

                        <div class="category-img-box">
                            <img src="./assets/images/icons/glasses.svg" alt="glasses & lens" width="30">
                        </div>

                        <div class="category-content-box">

                            <div class="category-content-flex">
                                <h3 class="category-item-title">Glasses & lens</h3>

                                <p class="category-item-amount">(68)</p>
                            </div>

                            <a href="#" class="category-btn">Show all</a>

                        </div>

                    </div>

                    <div class="category-item">

                        <div class="category-img-box">
                            <img src="./assets/images/icons/shorts.svg" alt="shorts & jeans" width="30">
                        </div>

                        <div class="category-content-box">

                            <div class="category-content-flex">
                                <h3 class="category-item-title">Shorts & jeans</h3>

                                <p class="category-item-amount">(84)</p>
                            </div>

                            <a href="#" class="category-btn">Show all</a>

                        </div>

                    </div>

                    <div class="category-item">

                        <div class="category-img-box">
                            <img src="./assets/images/icons/tee.svg" alt="t-shirts" width="30">
                        </div>

                        <div class="category-content-box">

                            <div class="category-content-flex">
                                <h3 class="category-item-title">T-shirts</h3>

                                <p class="category-item-amount">(35)</p>
                            </div>

                            <a href="#" class="category-btn">Show all</a>

                        </div>

                    </div>

                    <div class="category-item">

                        <div class="category-img-box">
                            <img src="./assets/images/icons/jacket.svg" alt="jacket" width="30">
                        </div>

                        <div class="category-content-box">

                            <div class="category-content-flex">
                                <h3 class="category-item-title">Jacket</h3>

                                <p class="category-item-amount">(16)</p>
                            </div>

                            <a href="#" class="category-btn">Show all</a>

                        </div>

                    </div>

                    <div class="category-item">

                        <div class="category-img-box">
                            <img src="./assets/images/icons/watch.svg" alt="watch" width="30">
                        </div>

                        <div class="category-content-box">

                            <div class="category-content-flex">
                                <h3 class="category-item-title">Watch</h3>

                                <p class="category-item-amount">(27)</p>
                            </div>

                            <a href="#" class="category-btn">Show all</a>

                        </div>

                    </div>

                    <div class="category-item">

                        <div class="category-img-box">
                            <img src="./assets/images/icons/hat.svg" alt="hat & caps" width="30">
                        </div>

                        <div class="category-content-box">

                            <div class="category-content-flex">
                                <h3 class="category-item-title">Hat & caps</h3>

                                <p class="category-item-amount">(39)</p>
                            </div>

                            <a href="#" class="category-btn">Show all</a>

                        </div>

                    </div>

                </div>

            </div>

        </div>




        <!-- - PRODUCT -->

        <div class="product-container">

            <div class="container">

                <div class="product-box">

                    <!--
                     - PRODUCT MINIMAL
                     -->

                    <!-- <div class="product-minimal"> -->



                    <!--
                        - PRODUCT GRID
                         -->

                    <div class="product-main">
                        <?php if (count($articles) > 0) : ?>
                        <h2 class="title">New Products</h2>
                        <div class="product-grid">
                            <?php foreach ($articles as $article) : ?>
                            <div class="showcase" data-article-id="<?php echo $article['id']; ?>">
                                <div class="showcase-banner">
                                    <?php if (!empty($article['image_path'])) : ?>
                                    <img src="<?php echo htmlspecialchars($article['image_path']); ?>"
                                        alt="<?php echo htmlspecialchars($article['etat']); ?>" width="300"
                                        class="product-img default">
                                    <?php endif; ?>
                                    <p class="showcase-badge"><?php echo htmlspecialchars($article['etat']); ?></p>
                                    <div class="showcase-actions">
                                        <?php
                    // Check if article is in favorites
                    $is_favorite = false;
                    if (isset($_SESSION['user_id'])) {
                        $check_stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND article_id = ?");
                        $check_stmt->execute([$_SESSION['user_id'], $article['id']]);
                        $is_favorite = $check_stmt->fetch() ? true : false;
                    }
                    ?>
                                        <button class="btn-action favorite-btn"
                                            data-article-id="<?php echo $article['id']; ?>">
                                            <ion-icon name="<?php echo $is_favorite ? 'heart' : 'heart-outline'; ?>"
                                                style="<?php echo $is_favorite ? 'color: #ff0000;' : ''; ?>">
                                            </ion-icon>
                                        </button>
                                        <a href="article-details.php?id=<?php echo urlencode($article['id']); ?>">
                                            <button class="btn-action">
                                                <ion-icon name="eye-outline"></ion-icon>
                                            </button>
                                        </a>
                                    </div>
                                </div>
                                <div class="showcase-content">
                                    <h1 class="showcase-category"><?php echo htmlspecialchars($article['title']); ?>
                                    </h1>
                                    <h1 class="showcase-title"><?php echo htmlspecialchars($article['description']); ?>
                                    </h1>
                                    <div class="showcase-rating">
                                        <ion-icon name="star"></ion-icon>
                                        <ion-icon name="star"></ion-icon>
                                        <ion-icon name="star"></ion-icon>
                                        <ion-icon name="star"></ion-icon>
                                        <ion-icon name="star"></ion-icon>
                                    </div>
                                    <div class="price-box">
                                        <p class="price" style='color:#ec5a13'>
                                            <?php echo htmlspecialchars($article['prix']); ?> TND
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else : ?>
                        <p class="no-results"><?php echo htmlspecialchars($no_results_message); ?></p>
                        <?php endif; ?>
                    </div>


    </main>

    <!--
    - FOOTER
  -->

    <footer>

        <div class="footer-nav">

            <div class="container">

                <ul class="footer-nav-list">

                    <li class="footer-nav-item">
                        <h2 class="nav-title">Popular Categories</h2>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Fashion</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Electronic</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Cosmetic</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Health</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Watches</a>
                    </li>

                </ul>

                <ul class="footer-nav-list">

                    <li class="footer-nav-item">
                        <h2 class="nav-title">Products</h2>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Prices drop</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">New products</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Best sales</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Contact us</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Sitemap</a>
                    </li>

                </ul>

                <ul class="footer-nav-list">

                    <li class="footer-nav-item">
                        <h2 class="nav-title">Our Company</h2>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Delivery</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Legal Notice</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Terms and conditions</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">About us</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Secure payment</a>
                    </li>

                </ul>

                <ul class="footer-nav-list">

                    <li class="footer-nav-item">
                        <h2 class="nav-title">Services</h2>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Prices drop</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">New products</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Best sales</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Contact us</a>
                    </li>

                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link">Sitemap</a>
                    </li>

                </ul>

                <ul class="footer-nav-list">

                    <li class="footer-nav-item">
                        <h2 class="nav-title">Contact</h2>
                    </li>

                    <li class="footer-nav-item flex">
                        <i class="fa-solid fa-location-dot"></i>

                        <address class="content">
                            Rue **** 2078, La marsa Tunis, Tunisie
                            </adress>
                    </li>

                    <li class="footer-nav-item flex">
                        <i class="fa-solid fa-phone"></i> <a href="tel:+607936-8058" class="footer-nav-link">+216
                            00000000 / +216 1111111111</a>
                    </li>

                    <li class="footer-nav-item flex">
                        <i class="fa-solid fa-envelope"></i> <a href="mailto:example@gmail.com"
                            class="footer-nav-link">leboncoin@gmail.com</a>
                    </li>

                </ul>

            </div>

        </div>

        <div class="footer-bottom">
            <p class="copyright">
                Copyright &copy; <a href="#">leboncoin</a> all rights reserved.
            </p>
        </div>

    </footer>






    <!--
    - custom js link
  -->


    <!--
    - ionicon link
  -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add click handlers to all category and subcategory links
        document.querySelectorAll('.dropdown-item a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                window.location.href = url;
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        // Update favorite count on page load
        updateFavoriteCount();

        // Add click handlers to all favorite buttons
        document.querySelectorAll('.favorite-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const articleId = this.getAttribute('data-article-id');
                toggleFavorite(articleId, this);
            });
        });
    });

    // Modal functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Open favorites modal
        const headerFavBtn = document.getElementById('header-fav-btn');
        const modal = document.getElementById('favorites-modal');
        const closeBtn = document.querySelector('.close-modal');

        if (headerFavBtn) {
            headerFavBtn.addEventListener('click', function(e) {
                e.preventDefault();
                //add by me
                e.stopPropagation();
                console.log('Header favorite button clicked');
                //to 
                loadFavorites();
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            });
        }

        // Close modal
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    });

    // Function to toggle favorite
    function toggleFavorite(articleId, buttonElement) {
        const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

        if (!isLoggedIn) {
            alert('Please login first to add favorites');
            window.location.href = 'index.php';
            return;
        }

        fetch('toggle_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'article_id=' + articleId
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update the favorite icon in the modal
                    const icon = buttonElement.querySelector('ion-icon');
                    if (data.action === 'added') {
                        icon.setAttribute('name', 'heart');
                        icon.style.color = '#ff0000';
                    } else {
                        icon.setAttribute('name', 'heart-outline');
                        icon.style.color = '';
                        // Remove the favorite item from the modal if it's open
                        const favoriteItem = document.querySelector(
                            `.favorite-item[data-article-id="${articleId}"]`);
                        if (favoriteItem) {
                            favoriteItem.remove();
                        }
                    }

                    // Update the favorite icon on the main page
                    const mainPageIcon = document.querySelector(
                        `.favorite-btn[data-article-id="${articleId}"] ion-icon`);
                    if (mainPageIcon) {
                        if (data.action === 'added') {
                            mainPageIcon.setAttribute('name', 'heart');
                            mainPageIcon.style.color = '#ff0000';
                        } else {
                            mainPageIcon.setAttribute('name', 'heart-outline');
                            mainPageIcon.style.color = '';
                        }
                    }

                    // Update the favorite count
                    updateFavoriteCount();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
    }

    // Function to load favorites into the modal
    function loadFavorites() {
        const modalBody = document.querySelector('.favorites-grid');
        if (!modalBody) return;

        // Show the loading spinner immediately
        modalBody.innerHTML = `
        <div class="loading-spinner">
            <ion-icon name="reload-outline"></ion-icon>
            <p>Loading favorites...</p>
        </div>
    `;

        // Use setTimeout to allow the UI to update before making the fetch request
        setTimeout(() => {
            fetch('get_favorites.php')
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        modalBody.innerHTML = `
                        <div class="empty-favorites">
                            <p>You haven't added any favorites yet.</p>
                        </div>`;
                        return;
                    }

                    // Populate the modal with favorites
                    modalBody.innerHTML = data.map(item => `
                    <div class="favorite-item" data-article-id="${item.id}">
                        <img src="${item.image_path || 'placeholder-image.jpg'}" 
                             alt="${item.title}" 
                             class="favorite-img">
                        <button class="favorite-remove" onclick="toggleFavorite(${item.id}, this)">
                            <ion-icon name="heart"></ion-icon>
                        </button>
                        <div class="favorite-content">
                            <h3 class="favorite-title">${item.title}</h3>
                            <p class="favorite-price">${item.prix} TND</p>
                        </div>
                    </div>
                `).join('');
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = `
                    <div class="empty-favorites">
                        <p>Error loading favorites. Please try again.</p>
                    </div>`;
                });
        }, 10); // A small delay to ensure the UI updates
    }

    // Modal functionality
    document.addEventListener('DOMContentLoaded', function() {
        const headerFavBtn = document.getElementById('header-fav-btn');
        const modal = document.getElementById('favorites-modal');
        const closeBtn = document.querySelector('.close-modal');

        if (headerFavBtn) {
            headerFavBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Show the modal immediately
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';

                // Load favorites after the modal is displayed
                loadFavorites();
            });
        }

        // Close modal
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    });

    // Function to update the favorite count
    function updateFavoriteCount() {
        fetch('get_favorite_count.php')
            .then(response => response.json())
            .then(data => {
                const headerCount = document.querySelector('.header-user-actions .count');
                if (headerCount) {
                    headerCount.textContent = data.count;
                }
            })
            .catch(error => console.error('Error:', error));
    }
    </script>


    <!-- Favorites Modal -->
    <div id="favorites-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Mes favoris</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="favorites-grid">
                    <!-- Favorites will be loaded here dynamically -->
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    .modal-content {
        background-color: #fff;
        margin: 50px auto;
        max-width: 700px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h2 {
        color: #ec5a13;
        margin: 0;
        font-size: 1.5rem;
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 28px;
        cursor: pointer;
        color: #666;
    }

    .close-modal:hover {
        color: #ec5a13;
    }

    .modal-body {
        padding: 20px;
        max-height: 70vh;
        overflow-y: auto;
    }

    .favorites-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
    }

    .favorite-item {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .favorite-img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 8px 8px 0 0;
    }

    .favorite-content {
        padding: 15px;
    }

    .favorite-title {
        font-size: 1rem;
        margin: 0 0 10px 0;
        color: #333;
    }

    .favorite-price {
        color: #ec5a13;
        font-weight: bold;
        font-size: 1.1rem;
        margin: 0;
    }

    .favorite-remove {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .favorite-remove ion-icon {
        color: #ff0000;
        font-size: 20px;
    }

    .empty-favorites {
        text-align: center;
        padding: 40px 20px;
        color: #666;
    }

    .loading-spinner {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
        color: #666;
    }

    .loading-spinner ion-icon {
        font-size: 40px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
    </style>

</body>

</html>