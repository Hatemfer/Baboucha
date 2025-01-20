<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
  die("Erreur : Vous devez être connecté.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $articleId = $_POST['article_id'];
  $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
  $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
  $etat = filter_input(INPUT_POST, 'etat', FILTER_SANITIZE_STRING);
  $prix = filter_input(INPUT_POST, 'prix', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
  $categoryId = filter_input(INPUT_POST, 'categorie', FILTER_SANITIZE_NUMBER_INT);
  $subcategoryId = filter_input(INPUT_POST, 'subcategorie', FILTER_SANITIZE_NUMBER_INT);

  // Vérification des erreurs
  $errors = [];
  if (empty($title)) $errors[] = "Le titre est requis.";
  if (empty($description)) $errors[] = "La description est requise.";
  if (empty($etat)) $errors[] = "L'état est requis.";
  if (empty($categoryId)) $errors[] = "La catégorie principale est requise.";
  if (empty($subcategoryId)) $errors[] = "La sous-catégorie est requise.";

  // Gestion de l'image
  $imagePath = null;
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
  }

  // Mettre à jour l'article dans la base de données si aucune erreur
  if (empty($errors)) {
    if ($imagePath) {
      $stmt = $pdo->prepare("
        UPDATE article 
        SET title = ?, description = ?, etat = ?, prix = ?, category_id = ?, subcategory_id = ?, image_path = ?
        WHERE id = ? AND userId = ?
      ");
      $stmt->execute([$title, $description, $etat, $prix, $categoryId, $subcategoryId, $imagePath, $articleId, $_SESSION['user_id']]);
    } else {
      $stmt = $pdo->prepare("
        UPDATE article 
        SET title = ?, description = ?, etat = ?, prix = ?, category_id = ?, subcategory_id = ?
        WHERE id = ? AND userId = ?
      ");
      $stmt->execute([$title, $description, $etat, $prix, $categoryId, $subcategoryId, $articleId, $_SESSION['user_id']]);
    }

    header("Location: profile.php?success=true");
    exit();
  } else {
    // Afficher les erreurs
    echo json_encode(["errors" => $errors]);
    exit();
  }
}
?>