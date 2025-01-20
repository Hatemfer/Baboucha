<?php
session_start();
require 'db.php';



// Récupérer tous les utilisateurs avec leurs avatars
$stmt = $pdo->query("SELECT id, prenom, nom, email,adresse,telephone,dateInscription, role, blocked, avatar FROM user WHERE role='member'");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer tous les articles avec leurs images et catégories et subCategorie
$stmt = $pdo->query("SELECT article.id, article.title, article.description, article.prix, article.createdAt, article.etat, article.image_path, user.prenom, user.nom, categorie.name as category_name , subcategorie.name as subcategory_name 
FROM article JOIN user ON article.userId = user.id JOIN categorie ON article.category_id = categorie.id JOIN subcategorie ON article.subcategory_id = subcategorie.id");
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le nombre d'articles par état
$stmt = $pdo->query("SELECT etat, COUNT(*) as count FROM article GROUP BY etat");
$articlesByEtat = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le nombre d'articles par utilisateur
$stmt = $pdo->query("SELECT user.id, user.prenom, user.nom, COUNT(article.id) as count FROM user LEFT JOIN article ON user.id = article.userId GROUP BY user.id");
$articlesByUser = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le nombre d'articles par catégorie
$stmt = $pdo->query("SELECT categorie.name, COUNT(article.id) as count FROM article JOIN categorie ON article.category_id = categorie.id GROUP BY categorie.name");
$articlesByCategorie = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement pour bloquer/débloquer un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['block_user'])) {
    $userId = $_POST['user_id'];
    $blocked = $_POST['blocked'] ? 0 : 1; // Inverser l'état actuel

    $stmt = $pdo->prepare("UPDATE user SET blocked = ? WHERE id = ?");
    $stmt->execute([$blocked, $userId]);

    header("Location: manage.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Admin</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- ApexCharts CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts/dist/apexcharts.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/manage.css">
</head>

<body>
    <!-- Bouton Retour -->
    <div class="button">
        <a href="dashboard.php">Retour</a>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Colonne gauche : Dashboard -->
            <div class="col-md-4">
                <h1 class="my-4">Tableau de Bord</h1>

                <!-- Graphique : Nombre d'articles par état -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="card-title">Articles par état</h2>
                    </div>
                    <div class="card-body">
                        <div id="articlesByEtatChart"></div>
                    </div>
                </div>

                <!-- Graphique : Nombre d'articles par utilisateur -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="card-title">Articles par utilisateur</h2>
                    </div>
                    <div class="card-body">
                        <div id="articlesByUserChart"></div>
                    </div>
                </div>

                <!-- Graphique : Nombre d'articles par Categorie -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="card-title">Articles par Categorie</h2>
                    </div>
                    <div class="card-body">
                        <div id="articlesByCategorieChart"></div>
                    </div>
                </div>
            </div>

            <!-- Colonne droite : Tables -->
            <div class="col-md-8">
                <!-- Tableau des utilisateurs -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="card-title">Utilisateurs</h2>
                        <div class="search-container">
                            <input type="text" id="searchUser" class="form-control"
                                placeholder="Rechercher par nom, prénom ou email...">
                            <i class="fas fa-search search-icon"></i>
                        </div>
                    </div>
                    <div class="card-body">

                        <table id="userTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Avatar</th>
                                    <th>Prénom</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>adresse</th>
                                    <th>telephone</th>
                                    <th>dateInscription</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id']) ?></td>
                                    <td>
                                        <img src="<?= htmlspecialchars($user['avatar'] ?? 'assets/images/logo/images.png') ?>"
                                            alt="Avatar" class="profile-pic">
                                    </td>
                                    <td><?= htmlspecialchars($user['prenom']) ?></td>
                                    <td><?= htmlspecialchars($user['nom']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['adresse']) ?></td>
                                    <td><?= htmlspecialchars($user['telephone']) ?></td>
                                    <td><?= htmlspecialchars($user['dateInscription']) ?></td>
                                    <td><?= htmlspecialchars($user['role']) ?></td>
                                    <td>
                                        <span class="badge <?= $user['blocked'] ? 'bg-danger' : 'bg-success' ?>">
                                            <?= $user['blocked'] ? 'Bloqué' : 'Actif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <input type="hidden" name="blocked" value="<?= $user['blocked'] ?>">
                                            <button type="submit" name="block_user" class="btn btn-warning btn-sm">
                                                <?= $user['blocked'] ? 'Débloquer' : 'Bloquer' ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tableau des articles -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="card-title">Articles</h2>
                        <div class="search-container">
                            <input type="text" id="searchArticle" class="form-control"
                                placeholder="Rechercher par titre ou description...">
                            <i class="fas fa-search search-icon"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="articleTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Titre</th>
                                    <th>Description</th>
                                    <th>Prix</th>
                                    <th>État</th>
                                    <th>Date Création</th>
                                    <th>Catégorie</th>
                                    <th>Sous-Catégorie</th>
                                    <th>Auteur</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($articles as $article): ?>
                                <tr>
                                    <td><?= htmlspecialchars($article['id']) ?></td>
                                    <td>
                                        <img src="<?= htmlspecialchars($article['image_path'] ?? 'assets/images/default-image.png') ?>"
                                            alt="Image de l'article" class="article-img">
                                    </td>
                                    <td><?= htmlspecialchars($article['title']) ?></td>
                                    <td><?= htmlspecialchars($article['description']) ?></td>
                                    <td><?= htmlspecialchars($article['prix']) ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= htmlspecialchars($article['etat']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($article['createdAt']) ?></td>
                                    <td><?= htmlspecialchars($article['category_name']) ?></td>
                                    <td><?= htmlspecialchars($article['subcategory_name']) ?></td>
                                    <td><?= htmlspecialchars($article['prenom'] . ' ' . $article['nom']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- ApexCharts JS -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Custom JS -->
    <script>
    // Initialisation de DataTables avec pagination personnalisée
    $(document).ready(function() {
        $('#userTable').DataTable({
            "pageLength": 5, // Afficher 5 lignes par défaut
            "lengthMenu": [3, 5, 10], // Options de pagination
        });

        $('#articleTable').DataTable({
            "pageLength": 5, // Afficher 5 lignes par défaut
            "lengthMenu": [3, 5, 10], // Options de pagination
        });
    });

    // Fonction de recherche pour le tableau des utilisateurs
    document.getElementById('searchUser').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#userTable tbody tr');

        rows.forEach(row => {
            const name = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const surname = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const email = row.querySelector('td:nth-child(5)').textContent.toLowerCase();

            if (name.includes(searchTerm) || surname.includes(searchTerm) || email.includes(
                    searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Fonction de recherche pour le tableau des articles
    document.getElementById('searchArticle').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#articleTable tbody tr');

        rows.forEach(row => {
            const title = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const description = row.querySelector('td:nth-child(4)').textContent.toLowerCase();

            if (title.includes(searchTerm) || description.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Données pour les graphiques
    const articlesByEtatData = {
        labels: <?= json_encode(array_column($articlesByEtat, 'etat')) ?>,
        series: <?= json_encode(array_column($articlesByEtat, 'count')) ?>,
    };

    const articlesByUserData = {
        labels: <?= json_encode(array_map(fn($user) => $user['prenom'] . ' ' . $user['nom'], $articlesByUser)) ?>,
        series: <?= json_encode(array_column($articlesByUser, 'count')) ?>,
    };

    const articlesByCategorieData = {
        labels: <?= json_encode(array_column($articlesByCategorie, 'name')) ?>,
        series: <?= json_encode(array_column($articlesByCategorie, 'count')) ?>,
    };
    // Graphique : Articles par état
    const articlesByEtatChart = new ApexCharts(document.querySelector("#articlesByEtatChart"), {
        chart: {
            type: 'bar'
        },
        series: [{
            name: 'Articles',
            data: articlesByEtatData.series
        }],
        xaxis: {
            categories: articlesByEtatData.labels
        },
    });
    articlesByEtatChart.render();

    // Graphique : Articles par utilisateur
    const articlesByUserChart = new ApexCharts(document.querySelector("#articlesByUserChart"), {
        chart: {
            type: 'pie'
        },
        series: articlesByUserData.series,
        labels: articlesByUserData.labels,
    });
    articlesByUserChart.render();

    // Graphique : Articles par Categorie
    const articlesByCategorieChart = new ApexCharts(document.querySelector("#articlesByCategorieChart"), {
        chart: {
            type: 'pie'
        },
        series: articlesByCategorieData.series,
        labels: articlesByCategorieData.labels,
    });
    articlesByCategorieChart.render();
    </script>
</body>

</html>