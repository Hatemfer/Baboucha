<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
  die("Erreur : Vous devez être connecté.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $articleId = $_POST['article_id'];

  // Supprimer l'article de la base de données
  $stmt = $pdo->prepare("DELETE FROM article WHERE id = ? AND userId = ?");
  $stmt->execute([$articleId, $_SESSION['user_id']]);

  header("Location: profile.php");
  exit();
}
?>