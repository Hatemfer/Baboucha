<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
  die("Erreur : Vous devez être connecté.");
}

if (!isset($_GET['id'])) {
  die("Erreur : ID de l'article manquant.");
}

$articleId = $_GET['id'];
$userId = $_SESSION['user_id'];

// Récupérer l'article depuis la base de données
$stmt = $pdo->prepare("SELECT * FROM article WHERE id = ? AND userId = ?");
$stmt->execute([$articleId, $userId]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
  die("Erreur : Article non trouvé.");
}

header('Content-Type: application/json');
echo json_encode($article);
?>