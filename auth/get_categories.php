<?php
require 'db.php';

// Récupérer les catégories depuis la base de données
$stmt = $pdo->query("SELECT id, name FROM categorie");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retourner les catégories au format JSON
header('Content-Type: application/json');
echo json_encode($categories);
?>