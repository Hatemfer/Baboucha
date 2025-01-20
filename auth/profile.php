<?php
session_start();
require 'db.php';

// Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur connecté
$stmt = $pdo->prepare("SELECT id, prenom, nom, email, role, adresse, telephone, dateinscription, notificationsActives, avatar FROM user WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur non trouvé.");
}

// Récupérer les articles de l'utilisateur connecté
$stmt = $pdo->prepare("SELECT * FROM article WHERE userId = ?");
$stmt->execute([$user_id]);
$userArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la mise à jour des notifications
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_notifications'])) {
    $isActive = $_POST['notificationsActives'] === '1' ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE user SET notificationsActives = ? WHERE id = ?");
    $stmt->execute([$isActive, $user_id]);
    header("Location: profile.php");
    exit();
}

// Traitement de la mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $notificationsActives = isset($_POST['notificationsActives']) ? 1 : 0;

    $avatarPath = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) { // Limite de 2 Mo
            echo "La taille de l'image ne doit pas dépasser 2 Mo.";
            exit();
        }

        $uploadDir = 'assets/images/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = uniqid() . '_' . basename($_FILES['avatar']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
            $avatarPath = $uploadFile;
        } else {
            echo "Erreur lors de l'upload du fichier.";
            exit();
        }
    }

    if ($avatarPath) {
        $stmt = $pdo->prepare("UPDATE user SET prenom = ?, nom = ?, email = ?, telephone = ?, notificationsActives = ?, avatar = ? WHERE id = ?");
        $stmt->execute([$prenom, $nom, $email, $telephone, $notificationsActives, $avatarPath, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE user SET prenom = ?, nom = ?, email = ?, telephone = ?, notificationsActives = ? WHERE id = ?");
        $stmt->execute([$prenom, $nom, $email, $telephone, $notificationsActives, $user_id]);
    }

    $_SESSION['success_message'] = "Profil mis à jour avec succès.";
    header("Location: profile.php");
    exit();
}

// Traitement de la suppression du compte
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    // Supprimer les articles de l'utilisateur avant de supprimer le compte
    $stmt = $pdo->prepare("DELETE FROM article WHERE userId = ?");
    $stmt->execute([$user_id]);

    // Supprimer l'utilisateur
    $stmt = $pdo->prepare("DELETE FROM user WHERE id = ?");
    if ($stmt->execute([$user_id])) {
        session_destroy(); // Détruire la session
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression du compte.";
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fontawesome.com/search?ic=free" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="assets/css/profile.css">
</head>

<body>
    <div class="button_retour"><a href="dashboard.php"> Retour</a></div>

    <div class="container">
        <!-- En-tête du profil -->
        <div class="profile-header">
            <div class="profile-info">
                <img src="<?= htmlspecialchars($user['avatar'] ?? 'assets/images/logo/images.png') ?>"
                    alt="Photo de profil" class="profile-pic">
                <div class="profile-details">
                    <h1><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h1>
                    <div class="details">
                        <p><span class="icon">📍</span> <?= htmlspecialchars($user['adresse'] ) ?></p>
                        <p> <span class="icon">
                                <?php if ($user['role'] === 'admin') : ?>
                                👤
                                <?php else : ?>
                                👔
                                <?php endif; ?>
                            </span>
                            <?= htmlspecialchars($user['role']) ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="btn-group">
                <button class="edit-profile-btn" onclick="openEditModal()"><span style="color: blue;">✏️</span> Modifier
                    mon profil</button>
                <button class="delete-profile-btn" onclick="openDeleteModal()">
                    <span class="modif-icon">🗑️</span> Supprimer mon compte</button>
            </div>
        </div>

        <!-- Informations vérifiées -->
        <div class="verified-info">
            <h2>Informations vérifiées</h2>
            <div class="info-item">
                <span class="icon">📧</span> Email: <?= htmlspecialchars($user['email']) ?>
            </div>
            <div class="info-item">
                <span class="icon">📞</span> Téléphone: <?= htmlspecialchars($user['telephone'] ?? '+216 12 345 678') ?>
            </div>
            <div class="info-item">
                <span class="icon">🔔</span> Notifications:
                <form method="POST" style="display:inline;">
                    <label class="switch">
                        <input type="checkbox" name="notificationsActives" value="1"
                            <?= $user['notificationsActives'] ? 'checked' : '' ?> onchange="this.form.submit()">
                        <span class="slider round"></span>
                    </label>
                    <input type="hidden" name="update_notifications" value="1">
                </form>
                <span id="notificationStatus">
                    <?= $user['notificationsActives'] ? 'Activées' : 'Désactivées' ?>
                </span>
            </div>
        </div>
        <!--toost-->
        <!-- Toast Notification -->
        <div id="toast" class="toast">
            <p>Article ajouté avec succès !</p>
        </div>

        <script>
        // Check if the URL contains the 'success=true' parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') === 'true') {
            // Show the toast notification
            const toast = document.getElementById('toast');
            toast.classList.add('show');

            // Hide the toast after 3 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                //remove the 'success=true' parameter from the URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }, 3000);
        }
        </script>
        <!--fin toast -->
        <!-- Onglets -->
        <div class="tabs">
            <div class="tab-links">
                <button class="tab-link active" onclick="openTab('articles')">Articles</button>

            </div>
            <div id="articles" class="tab-content">

                <?php if (count($userArticles) > 0) : ?>
                <button class="add-item-btn" onclick="window.location.href = 'addarticle.php';">Ajouter un
                    article</button>
                <div class="product-grid">
                    <?php foreach ($userArticles as $article) : ?>
                    <div class="showcase">
                        <div class="showcase-banner">
                            <?php if (!empty($article['image_path'])) : ?>
                            <img src="<?php echo htmlspecialchars($article['image_path']); ?>"
                                alt="<?php echo htmlspecialchars($article['etat']); ?>" width="300"
                                class="product-img default">
                            <?php endif; ?>
                            <p class="showcase-badge"><?php echo htmlspecialchars($article['etat']); ?></p>
                            <div class="showcase-actions">
                                <button class="btn-action edit" data-article-id="<?= $article['id'] ?>">
                                    ✏️
                                </button>
                                <button class="btn-action delete" data-article-id="<?= $article['id'] ?>">
                                    🗑️
                                </button>
                            </div>
                        </div>

                        <div class="showcase-content">
                            <h1 class="showcase-category" style='font-weight:bold;'>
                                <?php echo htmlspecialchars($article['title']); ?>
                            </h1>
                            <h1 class="showcase-title">
                                <?php echo htmlspecialchars($article['description']); ?></h1>

                            <div class="price-box">
                                <p class="price" style='color:#ec5a13'><?php echo htmlspecialchars($article['prix']); ?>
                                    TND
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else : ?>
                <div class="empty-state">
                    <div class="icon">👕</div>
                    <h3>Ajoute des articles pour commencer à vendre</h3>
                    <p>Vends les vêtements que tu ne portes plus. C'est facile et sécurisé !</p>
                    <button class="add-item-btn" onclick="window.location.href = 'addarticle.php';">Ajouter un
                        article</button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modale pour modifier le profil -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Modifier le profil</h2>
            <form method="POST" enctype="multipart/form-data">
                <!-- Section pour changer l'avatar -->
                <label for="avatar">Avatar:</label>
                <div class="avatar-upload">
                    <label for="avatar">
                        <img id="avatarPreview"
                            src="<?= htmlspecialchars($user['avatar'] ?? 'assets/images/logo/images.png') ?>"
                            alt="Avatar actuel" class="current-avatar">
                        <span>Choisir une image</span>
                    </label>
                    <input type="file" id="avatar" name="avatar" accept="image/*" onchange="previewImage(event)">
                </div>

                <!-- Autres champs du formulaire -->
                <label for="prenom">Prénom:</label>
                <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>

                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                <label for="telephone">Téléphone:</label>
                <input type="text" id="telephone" name="telephone"
                    value="<?= htmlspecialchars($user['telephone'] ?? '') ?>" required>

                <button type="submit" name="update_profile">Enregistrer</button>
            </form>
        </div>
    </div>

    <!-- Modale pour confirmer la suppression du compte -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteModal()">&times;</span>
            <h2>Confirmer la suppression du compte</h2>
            <p>Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.</p>
            <form method="POST">
                <button type="submit" name="delete_account" class="confirm-delete-btn">Oui, supprimer mon
                    compte</button>
                <button type="button" onclick="closeDeleteModal()" class="cancel-delete-btn">Annuler</button>
            </form>
        </div>
    </div>

    <!-- Modale pour modifier un article -->
    <div id="editArticleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditArticleModal()">&times;</span>
            <h2 style='color: #ec5a13'>Modifier l'article</h2>

            <!-- Affichage des erreurs -->
            <div class="error" id="editArticleErrors" style="display: none;"></div>

            <!-- Formulaire de modification -->
            <form id="editArticleForm" action="edit_article.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="editArticleId" name="article_id">

                <!-- Ajout d'image -->
                <div class="image-upload" id="editImageUpload" onclick="triggerEditImageUpload()">
                    <img id="editPreviewImage" src="#" alt="Aperçu de l'image" style="display: none;" />
                    <p>Cliquez ici pour changer l'image</p>
                    <input type="file" id="editImage" name="image" accept="image/*" />
                </div>

                <!-- Titre -->
                <label for="editTitle">Titre</label>
                <input type="text" id="editTitle" name="title" placeholder="ex : Chemise Sézane verte" required />

                <!-- Description -->
                <label for="editDescription">Décrire ton article</label>
                <textarea id="editDescription" name="description" rows="4"
                    placeholder="ex : porté quelques fois, taille correctement" required></textarea>

                <!-- Catégorie -->
                <label for="editCategorie">Catégorie</label>
                <select id="editCategorie" name="categorie" required onchange="loadEditSubcategories(this.value)">
                    <option value="">Sélectionnez une catégorie</option>
                </select>

                <!-- Sous-Catégorie -->
                <label for="editSubcategorie">Sous-Catégorie</label>
                <select id="editSubcategorie" name="subcategorie" required>
                    <option value="">Sélectionnez une sous-catégorie</option>
                </select>

                <!-- État -->
                <label for="editEtat">État</label>
                <select id="editEtat" name="etat" required>
                    <option value="Neuf">Neuf</option>
                    <option value="Bon état">Bon état</option>
                    <option value="Usagé">Usagé</option>
                </select>

                <!-- Prix -->
                <label for="editPrix">Prix</label>
                <input type="number" id="editPrix" name="prix" step="0.01" min="0" placeholder="0.00 TND" required />

                <!-- Bouton de soumission -->
                <input type="submit" value="Enregistrer les modifications" />
            </form>
        </div>
    </div>
    <!-- Modale pour confirmer la suppression d'un article -->
    <div id="deleteArticleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteArticleModal()">&times;</span>
            <h2>Confirmer la suppression de l'article</h2>
            <p>Êtes-vous sûr de vouloir supprimer cet article ? Cette action est irréversible.</p>
            <form id="deleteArticleForm" action="delete_article.php" method="POST">
                <input type="hidden" id="deleteArticleId" name="article_id">
                <button type="submit" class="confirm-delete-btn">Oui, supprimer l'article</button>
                <button type="button" onclick="closeDeleteArticleModal()" class="cancel-delete-btn">Annuler</button>
            </form>
        </div>
    </div>

    <script src="assets/js/profile.js"></script>
    <script>
    // Fonctions pour gérer les modales
    function openEditModal() {
        document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function openDeleteModal() {
        document.getElementById('deleteModal').style.display = 'block';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    // Prévisualisation de l'image
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('avatarPreview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
    </script>
</body>

</html>