<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login first']);
    exit;
}

if (!isset($_POST['article_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Article ID is required']);
    exit;
}

$user_id = $_SESSION['user_id'];
$article_id = $_POST['article_id'];

try {
    // Check if favorite already exists
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND article_id = ?");
    $stmt->execute([$user_id, $article_id]);
    $favorite = $stmt->fetch();

    if ($favorite) {
        // Remove favorite
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND article_id = ?");
        $stmt->execute([$user_id, $article_id]);
        echo json_encode(['status' => 'success', 'action' => 'removed']);
    } else {
        // Add favorite
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, article_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $article_id]);
        echo json_encode(['status' => 'success', 'action' => 'added']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>