<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM favorites WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['count' => $result['count']]);
} catch (PDOException $e) {
    echo json_encode(['count' => 0]);
}
?>