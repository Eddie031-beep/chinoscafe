<?php
require_once '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM usuarios WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        if ($user['is_admin']) {
            header('Location: ../views/admin/dashboard.php');
        } else {
            header('Location: ../views/tienda.php');
        }
    } else {
        header('Location: ../views/login.php?error=1');
    }
}
?>