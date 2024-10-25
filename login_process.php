<?php
session_start();
require_once('db.php');

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM user_info WHERE username = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$username]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$row){
    header('location: login.php?error=User not found');
    exit;
}else{
    if(!password_verify($password, $row['password'])){
        header('location: login.php?error=Wrong password');
        exit;
    }else{
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['email'] = $row['email'];
        header('location: index.php');
    }
}
?>