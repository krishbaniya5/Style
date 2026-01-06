<?php
session_start();
require 'vendor/autoload.php';
require 'db.php';

$client = new Google_Client();
$client->setClientId('YOUR_GOOGLE_CLIENT_ID');
$client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
$client->setRedirectUri('http://yourdomain.com/google_login.php');
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        die("Error fetching token: " . $token['error_description']);
    }
    $client->setAccessToken($token);

    $oauth = new Google_Service_Oauth2($client);
    $userInfo = $oauth->userinfo->get();
    $email = $userInfo->email;

    $stmt = $pdo->prepare("SELECT id, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, '', 'user')");
        $stmt->execute([$email]);
        $user_id = $pdo->lastInsertId();
        $role = 'user';
    } else {
        $user_id = $user['id'];
        $role = $user['role'];
    }

    $_SESSION['user_id'] = $user_id;
    $_SESSION['role'] = $role;

    if ($role === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: user.php");
    }
    exit;
} else {
    $authUrl = $client->createAuthUrl();
    header("Location: $authUrl");
    exit;
}
?>
