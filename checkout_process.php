<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])){
    echo json_encode(['status'=>'failed','message'=>'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if(!isset($data['items']) || !is_array($data['items'])){
    echo json_encode(['status'=>'failed','message'=>'No cart items']);
    exit;
}

try{
    $pdo->beginTransaction();

    foreach($data['items'] as $item){
        $stmt = $pdo->prepare("
            INSERT INTO orders 
            (user_id, product_name, product_price, size, quantity, total, shipping_name, shipping_address, payment_method, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $status = $data['payment_method'] === 'COD' ? 'pending' : 'paid';
        $stmt->execute([
            $_SESSION['user_id'],
            $item['product_name'],
            $item['product_price'],
            $item['size'],
            $item['quantity'],
            $item['product_price']*$item['quantity'],
            $data['shipping_name'],
            $data['shipping_address'],
            $data['payment_method'],
            $status
        ]);

        $orderId = $pdo->lastInsertId();

        // Insert payment
        $stmt2 = $pdo->prepare("INSERT INTO payments (order_id, amount, status) VALUES (?, ?, ?)");
        $stmt2->execute([$orderId, $item['product_price']*$item['quantity'], $status]);
    }

    // Clear cart
    $stmt3 = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt3->execute([$_SESSION['user_id']]);

    $pdo->commit();
    echo json_encode(['status'=>'success']);
}catch(Exception $e){
    $pdo->rollBack();
    echo json_encode(['status'=>'failed','message'=>$e->getMessage()]);
}
