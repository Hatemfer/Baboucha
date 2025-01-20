<?php
session_start();
require 'db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Erreur : Vous devez être connecté pour ajouter un article.");
}

$userId = $_SESSION['user_id']; // ID de l'utilisateur connecté

// Gestion de l'ajout d'article
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $etat = filter_input(INPUT_POST, 'etat', FILTER_SANITIZE_STRING);
    $prix = filter_input(INPUT_POST, 'prix', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $mainCategory = filter_input(INPUT_POST, 'categorie', FILTER_SANITIZE_NUMBER_INT);
    $subCategory = filter_input(INPUT_POST, 'subcategorie', FILTER_SANITIZE_NUMBER_INT);

    // Vérification des erreurs
    $errors = [];
    if (empty($title)) $errors[] = "Le titre est requis.";
    if (empty($description)) $errors[] = "La description est requise.";
    if (empty($etat)) $errors[] = "L'état est requis.";
    if (empty($mainCategory)) $errors[] = "La catégorie principale est requise.";
    if (empty($subCategory)) $errors[] = "La sous-catégorie est requise.";

    // Gestion de l'image
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image'];
        $imageName = uniqid() . "-" . basename($image['name']);
        $imagePath = "uploads/" . $imageName;

        // Vérification et téléchargement de l'image
        if ($image['size'] > 2000000) { // Limite de 2 Mo
            $errors[] = "L'image est trop grande (maximum 2 Mo).";
        }
        if (!in_array($image['type'], ['image/jpeg', 'image/png', 'image/gif'])) {
            $errors[] = "Seules les images JPEG, PNG et GIF sont acceptées.";
        }

        if (empty($errors) && !move_uploaded_file($image['tmp_name'], $imagePath)) {
            $errors[] = "Erreur lors de l'upload de l'image.";
        }
    } else {
        $errors[] = "L'image est requise.";
    }

    // Insertion dans la base de données si aucune erreur
    if (empty($errors)) {
        $stmt = $pdo->prepare('
            INSERT INTO article (title, description, image_path, etat, prix, userId, category_id, subcategory_id)
            VALUES (:title, :description, :image_path, :etat, :prix, :userId, :category_id, :subcategory_id)
        ');

        if ($stmt->execute([
            'title' => $title,
            'description' => $description,
            'image_path' => $imagePath,
            'etat' => $etat,
            'prix' => $prix,
            'userId' => $userId,
            'category_id' => $mainCategory,
            'subcategory_id' => $subCategory
        ])) {
                // Redirection vers le tableau de bord
    header('Location: profile.php?success=true');
    exit; // Toujours arrêter le script après une redirection
        } else {
            $errors[] = "Erreur lors de l'ajout de l'article.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Article</title>
    <link rel="stylesheet" href="assets/css/add-article.css">
</head>

<body>
    <div class="button"><a href="dashboard.php"> Retour</a></div>

    <form id="addArticleForm" action="addarticle.php" method="post" enctype="multipart/form-data">
        <h2 style='color: #ec5a13'>Ajouter un Article</h2>

        <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
        <div class="success">
            <p><?= htmlspecialchars($success) ?></p>
        </div>
        <?php endif; ?>

        <!-- Ajout d'image -->
        <div class="image-upload" id="imageUpload" onclick="triggerImageUpload()">
            <img id="previewImage" src="#" alt="Aperçu de l'image" style="display: none;" />
            <p>Cliquez ici pour ajouter une image</p>
            <input type="file" id="image" name="image" accept="image/*" required />
        </div>

        <!-- Titre -->
        <label for="title">Titre</label>
        <input type="text" id="title" name="title" placeholder="ex : Chemise Sézane verte" required />

        <!-- Description -->
        <label for="description">Décrire ton article</label>
        <textarea id="description" name="description" rows="4"
            placeholder="ex : porté quelques fois, taille correctement" required></textarea>

        <!-- Catégorie -->
        <label for="categorie">Catégorie</label>
        <select id="categorie" name="categorie" required onchange="loadSubcategories(this.value)">
            <option value="">Sélectionnez une catégorie</option>
        </select>

        <label for="subcategorie">Sous-Catégorie</label>
        <select id="subcategorie" name="subcategorie" required>
            <option value="">Sélectionnez une sous-catégorie</option>
        </select>

        <script>
        fetch('get_categories.php')
            .then(response => response.json())
            .then(categories => {
                const categorieSelect = document.getElementById('categorie');
                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    categorieSelect.appendChild(option);
                });
            });

        function loadSubcategories(categoryId) {
            fetch(`get_subcategories.php?category_id=${categoryId}`)
                .then(response => response.json())
                .then(subcategories => {
                    const subcategorieSelect = document.getElementById('subcategorie');
                    subcategorieSelect.innerHTML = '<option value="">Sélectionnez une sous-catégorie</option>';
                    subcategories.forEach(subcategory => {
                        const option = document.createElement('option');
                        option.value = subcategory.id;
                        option.textContent = subcategory.name;
                        subcategorieSelect.appendChild(option);
                    });
                });
        }
        </script>

        <!-- État -->
        <label for="etat">État</label>
        <select classe="etat" id="etat" name="etat" required>
            <option selected value="Neuf">Neuf</option>
            <option value="Bon état">Bon état</option>
            <option value="Usagé">Usagé</option>
        </select>

        <!-- Prix -->
        <label for="prix">Prix</label>
        <input type="number" id="prix" name="prix" step="0.01" min="0" placeholder="0.00 TND" required />

        <input type="submit" value="Ajouter l'article" />
    </form>

    <script>
    function triggerImageUpload() {
        document.getElementById('image').click();
    }

    const imageInput = document.getElementById('image');
    const previewImage = document.getElementById('previewImage');
    imageInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
    </script>
</body>

</html>