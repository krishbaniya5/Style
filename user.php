<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Fetch user information
$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$_SESSION['user_id']]);
$user = $userStmt->fetch();

// Fetch cart items
$cartStmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? ORDER BY added_at DESC");
$cartStmt->execute([$_SESSION['user_id']]);
$cartItems = $cartStmt->fetchAll();

// Fetch orders
$orderStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orderStmt->execute([$_SESSION['user_id']]);
$orders = $orderStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Dashboard</title>
<style>
table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
th { background: #f4f4f4; }
button, .btn { padding: 8px 15px; cursor: pointer; border-radius: 5px; text-decoration: none; color: white; background: #2196F3; border: none; }
button:hover, .btn:hover { background: #0b7dda; }
.container { max-width: 900px; margin: auto; padding: 20px; }
</style>
</head>
<body>
<div class="container">

<h2>Welcome <?= htmlspecialchars($user['name'] ?? $user['username'] ?? 'Guest') ?></h2>

<!-- Order More Products Button -->
<a href="index.html" class="btn" style="margin-bottom:20px; display:inline-block;">Order More Products</a>

<h3>My Cart</h3>
<?php if(count($cartItems) > 0): ?>
<table>
<tr>
<th>Product</th>
<th>Size</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
<th>Action</th>
</tr>
<?php foreach($cartItems as $c): ?>
<tr id="row<?= htmlspecialchars($c['id']) ?>">
<td><?= htmlspecialchars($c['product_name']) ?></td>
<td><?= htmlspecialchars($c['size']) ?></td>
<td><?= htmlspecialchars($c['quantity']) ?></td>
<td><?= htmlspecialchars($c['product_price']) ?></td>
<td><?= htmlspecialchars($c['product_price'] * $c['quantity']) ?></td>
<td>
<button onclick="removeItem(<?= htmlspecialchars($c['id']) ?>)">Remove</button>
<a href="checkout.php">Checkout All</a>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>Your cart is empty.</p>
<?php endif; ?>

<h3>My Orders</h3>
<?php if(count($orders) > 0): ?>
<table>
<tr><th>Product</th><th>Qty</th><th>Total</th><th>Status</th></tr>
<?php foreach($orders as $o): ?>
<tr>
<td><?= htmlspecialchars($o['product_name']) ?></td>
<td><?= htmlspecialchars($o['quantity']) ?></td>
<td><?= htmlspecialchars($o['total']) ?></td>
<td><?= htmlspecialchars($o['status']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>You have no orders yet.</p>
<?php endif; ?>

<script>
function removeItem(id) {
    fetch("remove_from_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            document.getElementById("row"+id).remove();
        } else {
            alert("Failed to remove item.");
        }
    })
    .catch(() => alert("Error removing item."));
}
</script>

</div>
</body>
</html>
