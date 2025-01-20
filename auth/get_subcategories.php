<?php
require 'db.php';

if (!isset($_GET['category_id'])) {
  die("Erreur : ID de la catégorie manquant.");
}

$categoryId = $_GET['category_id'];

// Récupérer les sous-catégories depuis la base de données
$stmt = $pdo->prepare("SELECT id, name FROM subcategorie WHERE category_id = ?");
$stmt->execute([$categoryId]);
$subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retourner les sous-catégories au format JSON
header('Content-Type: application/json');
echo json_encode($subcategories);
?>