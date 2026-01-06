<?php

session_start();
require 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role']; 

 
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "Email already exists.";
        exit;
    }

  
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  
    $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
    if ($stmt->execute([$email, $hashedPassword, $role])) {
        echo "User created successfully!";
    } else {
        echo "Failed to create user.";
    }
} else {

    echo '
    <form method="POST">
        Email: <input type="email" name="email" required><br><br>
        Password: <input type="text" name="password" required><br><br>
        Role: <select name="role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select><br><br>
        <button type="submit">Create User</button>
    </form>';
}
?>
