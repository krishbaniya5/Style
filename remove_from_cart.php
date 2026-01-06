<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?")
    ->execute([$data['id'], $_SESSION['user_id']]);

echo json_encode(['success' => true]);
