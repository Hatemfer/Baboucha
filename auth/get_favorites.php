<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT a.* 
        FROM Article a 
        JOIN favorites f ON a.id = f.article_id 
        WHERE f.user_id = ?
        ORDER BY f.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debugging: Log the fetched favorites
    error_log("Favorites: " . print_r($favorites, true));

    echo json_encode($favorites);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([]);
}
?>