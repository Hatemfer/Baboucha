<?php 
require 'db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $articleId = intval($_GET['id']);

    // Fetch article details
    $query = $pdo->prepare("SELECT a.*, u.prenom, u.nom, u.telephone, u.adresse FROM Article a
                            JOIN user u ON a.userId = u.id WHERE a.id = :id");
    $query->bindParam(':id', $articleId, PDO::PARAM_INT);
    $query->execute();
    $article = $query->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        die("Article not found");
    }
} else {
    die("Invalid article ID");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article Details</title>
    <link rel="stylesheet" href="assets/css/article-details.css">
</head>

<body>

    <div class="button"><a href="dashboard.php"> Retour</a></div>

    <div class="container">
        <div class="image-section">
            <img src="<?php echo htmlspecialchars($article['image_path']); ?>" alt="Article Image">
        </div>
        <div class="details">
            <div class="title">
                <div style='font-size: 24px'><strong>Titre: </strong><?php echo htmlspecialchars($article['title']);?>
                </div>
                <div><strong>Description: </strong><?php echo nl2br(htmlspecialchars($article['description']));?></div>
                <div><strong>État: </strong><?php echo htmlspecialchars($article['etat']);?></div>
            </div>
            <div class='price-data'>
                <div class="price"><?php echo htmlspecialchars($article['prix']);?>TND</div>
                <div class='protection-acheteurs'>
                    <span>
                        Inclut la Protection acheteurs
                    </span>
                    <span class="service-fee-included-title--icon"><span
                            class="web_ui__Icon__icon web_ui__Icon__primary-default"
                            data-testid="service-fee-included-icon" style="width:12px"><svg fill="none"
                                viewBox="0 0 12 12" width="12" height="12">
                                <path fill="currentColor"
                                    d="m7.924 4.114.708.707-2.829 2.828-2.121-2.121.707-.707 1.414 1.414 2.121-2.121Z">
                                </path>
                                <path fill="currentColor" fill-rule="evenodd"
                                    d="M11 6c0 4.2-5 6-5 6s-5-1.8-5-6V1.8L6 0l5 1.8V6ZM2 6V2.503l4-1.44 4 1.44V6c0 1.66-.98 2.902-2.115 3.787A9.368 9.368 0 0 1 6 10.916a9.368 9.368 0 0 1-1.885-1.13C2.981 8.902 2 7.66 2 6Zm3.66 5.06c-.001 0 0 0 0 0Z"
                                    clip-rule="evenodd"></path>
                            </svg></span></span>
                </div>
            </div>

            <div class="seller">
                <div><strong>Vendeur: </strong><?php echo htmlspecialchars($article['prenom'] . ' '. $article['nom']);?>
                </div>
                <div><strong>Adresse:
                    </strong><?php echo htmlspecialchars($article['adresse']);?>
                </div>
                <div><strong>Téléphone: </strong><?php echo htmlspecialchars($article['telephone']);?></div>
                <div><strong>Date d'ajout: </strong> <?php echo htmlspecialchars($article['createdAt']); ?></div>
            </div>
            <div class="button"><a href="#">Contacter le vendeur</a></div>
        </div>
    </div>
</body>

</html>