<?php
session_start(); 
require_once('db.php');

if (isset($_POST['email'], $_POST['username'], $_POST['password'])) {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $checkUserSql = "SELECT * FROM user_info WHERE username = ?";
    $checkStmt = $db->prepare($checkUserSql);
    $checkStmt->execute([$username]);

    if ($checkStmt->rowCount() > 0) {
        $_SESSION['errorMessage'] = "Username sudah digunakan.";
        header('Location: register.php'); 
        exit(); 
    } else {
        $en_pass = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO user_info (email, username, password) VALUES(?, ?, ?)";
        $result = $db->prepare($sql);
        
        if ($result->execute([$email, $username, $en_pass])) {
            header('Location: login.php');
            exit();
        } else {
            $_SESSION['errorMessage'] = "Error: Failed to register.";
        }
    }
} else {
    $_SESSION['errorMessage'] = "Error: All fields are required.";
}
?>
