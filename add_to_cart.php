<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$pdo->prepare("
INSERT INTO cart (user_id, product_name, product_price, size, quantity)
VALUES (?, ?, ?, ?, ?)
")->execute([
$_SESSION['user_id'],
$data['name'],
$data['price'],
$data['size'],
$data['quantity']
]);

echo json_encode(['success' => true]);
