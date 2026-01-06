<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}

if (isset($_GET['delete_contact'])) {
    $pdo->prepare("DELETE FROM contacts WHERE id=?")
        ->execute([$_GET['delete_contact']]);
    header("Location: admin.php");
    exit;
}

if (isset($_POST['update_contact'])) {
    $pdo->prepare("
        UPDATE contacts 
        SET name=?, email=?, subject=?, message=? 
        WHERE id=?
    ")->execute([
        $_POST['name'],
        $_POST['email'],
        $_POST['subject'],
        $_POST['message'],
        $_POST['id']
    ]);
    header("Location: admin.php");
    exit;
}

if (isset($_GET['delete_cart'])) {
    $pdo->prepare("DELETE FROM cart WHERE id=?")
        ->execute([$_GET['delete_cart']]);
    header("Location: admin.php");
    exit;
}

$contacts = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();

$cart = $pdo->query("
    SELECT c.*, u.email 
    FROM cart c 
    JOIN users u ON c.user_id = u.id 
    ORDER BY c.added_at DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table { width:100%; border-collapse:collapse; margin:20px 0; }
        th, td { border:1px solid #ccc; padding:8px; }
        th { background:#f4f4f4; }
        .btn { padding:5px 10px; background:#3498db; color:#fff; text-decoration:none; }
        .btn.red { background:#e74c3c; }
    </style>
</head>
<body>

<nav>
    <a href="index.html">StyleJackets</a>
    <a href="logout.php">Logout</a>
</nav>

<h2>Manage Contacts</h2>
<table>
<tr>
<th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Date</th><th>Action</th>
</tr>
<?php foreach($contacts as $c): ?>
<tr>
<form method="post">
<td><?= $c['id'] ?><input type="hidden" name="id" value="<?= $c['id'] ?>"></td>
<td><input name="name" value="<?= htmlspecialchars($c['name']) ?>"></td>
<td><input name="email" value="<?= htmlspecialchars($c['email']) ?>"></td>
<td><input name="subject" value="<?= htmlspecialchars($c['subject']) ?>"></td>
<td><textarea name="message"><?= htmlspecialchars($c['message']) ?></textarea></td>
<td><?= $c['created_at'] ?></td>
<td>
<button class="btn" name="update_contact">Update</button>
<a class="btn red" href="?delete_contact=<?= $c['id'] ?>">Delete</a>
</td>
</form>
</tr>
<?php endforeach; ?>
</table>

<h2>User Cart Items</h2>
<table>
<tr><th>User</th><th>Product</th><th>Size</th><th>Qty</th><th>Action</th></tr>
<?php foreach($cart as $c): ?>
<tr>
<td><?= $c['email'] ?></td>
<td><?= $c['product_name'] ?></td>
<td><?= $c['size'] ?></td>
<td><?= $c['quantity'] ?></td>
<td>
<a class="btn red" href="?delete_cart=<?= $c['id'] ?>">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>
