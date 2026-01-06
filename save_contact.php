<?php
require "db.php";
$pdo->prepare("INSERT INTO contacts(name,email,subject,message) VALUES (?,?,?,?)")
->execute([$_POST['name'],$_POST['email'],$_POST['subject'],$_POST['message']]);
header("Location: index.html#contact");
