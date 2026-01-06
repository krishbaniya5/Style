<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Fetch cart items for the logged-in user
$cartStmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ?");
$cartStmt->execute([$_SESSION['user_id']]);
$cartItems = $cartStmt->fetchAll();

if(!$cartItems){
    echo "<p>Your cart is empty. <a href='index.html'>Go back to shop</a></p>";
    exit;
}

// Calculate total
$totalAmount = 0;
foreach($cartItems as $item){
    $totalAmount += $item['product_price'] * $item['quantity'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout - StyleJackets</title>
<link rel="stylesheet" href="style.css">
<script src="https://khalti.com/static/khalti-checkout.js"></script>
<style>
#success-message { display:none; text-align:center; margin-top:20px; padding:20px; border:2px solid #4CAF50; border-radius:10px; background:#f0fff0; }
.btn { margin:10px; padding:10px 20px; cursor:pointer; border-radius:5px; color:white; border:none; }
.btn-cod { background:#2196F3; }
.btn-khalti { background:#6f42c1; }
table { width:100%; border-collapse: collapse; margin-top: 20px; }
th, td { border:1px solid #ccc; padding:8px; text-align:center; }
th { background:#f4f4f4; }
</style>
</head>
<body>
<h2>Checkout</h2>

<h3>Order Summary</h3>
<table>
<tr>
<th>Product</th><th>Size</th><th>Qty</th><th>Price</th><th>Total</th>
</tr>
<?php foreach($cartItems as $c): ?>
<tr>
<td><?= htmlspecialchars($c['product_name']) ?></td>
<td><?= htmlspecialchars($c['size']) ?></td>
<td><?= htmlspecialchars($c['quantity']) ?></td>
<td><?= htmlspecialchars($c['product_price']) ?></td>
<td><?= $c['product_price'] * $c['quantity'] ?></td>
</tr>
<?php endforeach; ?>
<tr>
<th colspan="4">Grand Total</th>
<th><?= $totalAmount ?></th>
</tr>
</table>

<h3>Shipping Details</h3>
<form id="checkout-form">
  <label>Name:</label><br>
  <input type="text" id="shipping-name" required><br><br>
  <label>Address:</label><br>
  <textarea id="shipping-address" required></textarea><br><br>

  <button type="button" class="btn btn-khalti" id="submit-btn">Pay with Khalti</button>
  <button type="button" class="btn btn-cod" id="cod-btn">Cash on Delivery</button>
</form>

<div id="success-message">
<h3>Order Placed Successfully!</h3>
<p>Thank you for shopping with us.</p>
<a href="user.php">Go to My Orders</a>
</div>

<script>
// Get cart items from PHP
const cartItems = <?php echo json_encode($cartItems); ?>;
const totalAmount = <?= $totalAmount ?>;

// Khalti Checkout
var config = {
  publicKey: "YOUR_KHALTI_PUBLIC_KEY",
  productIdentity: "1234567890",
  productName: "StyleJackets Order",
  productUrl: window.location.href,
  eventHandler: {
    onSuccess(payload){
      const orderData = {
        items: cartItems,
        total: totalAmount,
        shipping_name: document.getElementById('shipping-name').value,
        shipping_address: document.getElementById('shipping-address').value,
        payment_method: 'Khalti',
        khalti_token: payload.token,
        khalti_amount: payload.amount
      };
      fetch("checkout_process.php", {
        method: "POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify(orderData)
      }).then(res => res.json())
        .then(data => {
          if(data.status==='success'){
            document.getElementById("checkout-form").style.display="none";
            document.getElementById("success-message").style.display="block";
          } else { alert("Payment verification failed!"); }
        });
    },
    onError(error){ alert("Payment failed!"); console.log(error); },
    onClose(){ console.log("Khalti widget closed"); }
  }
};
var checkout = new KhaltiCheckout(config);
document.getElementById("submit-btn").addEventListener("click", function(){
  checkout.show({amount: totalAmount*100});
});

// Cash on Delivery
document.getElementById("cod-btn").addEventListener("click", function(){
  const orderData = {
    items: cartItems,
    total: totalAmount,
    shipping_name: document.getElementById('shipping-name').value,
    shipping_address: document.getElementById('shipping-address').value,
    payment_method: 'COD'
  };
  fetch("checkout_process.php", {
    method: "POST",
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify(orderData)
  }).then(res => res.json())
    .then(data => {
      if(data.status==='success'){
        document.getElementById("checkout-form").style.display="none";
        document.getElementById("success-message").style.display="block";
      } else { alert("Order failed: "+data.message); }
    });
});
</script>
</body>
</html>
