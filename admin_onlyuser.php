<?php
session_start();
require 'db.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null; 
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; 
    $role = $_POST['role'];

    if ($id) {
        
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id = ?");
            $stmt->execute([$name, $email, $hashedPassword, $role, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
            $stmt->execute([$name, $email, $role, $id]);
        }
        $msg = "User updated successfully!";
    } else {
       
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword, $role]);
        $msg = "User created successfully!";
    }
}


$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users - Admin</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Manage Users</h2>

<?php if(isset($msg)) echo "<p style='color:green;'>$msg</p>"; ?>

<table border="1" style="width:100%; margin-bottom:20px;">
<thead>
<tr>
<th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Password</th><th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach ($users as $user): ?>
<tr>
<form method="POST">
<td><?php echo $user['id']; ?><input type="hidden" name="id" value="<?php echo $user['id']; ?>"></td>
<td><input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required></td>
<td><input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></td>
<td>
<select name="role">
<option value="user" <?php if($user['role']=='user') echo 'selected'; ?>>User</option>
<option value="admin" <?php if($user['role']=='admin') echo 'selected'; ?>>Admin</option>
</select>
</td>
<td><input type="password" name="password" placeholder="Leave blank to keep"></td>
<td><button type="submit">Update</button></td>
</form>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<h3>Create New User</h3>
<form method="POST">
<input type="text" name="name" placeholder="Name" required>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<select name="role">
<option value="user">User</option>
<option value="admin">Admin</option>
</select>
<button type="submit">Create User</button>
</form>

</body>
</html>
