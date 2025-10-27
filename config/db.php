<?php
$host = 'localhost';
$db   = 'chinoscafe_db'; // 👈 debe incluir "_db"
$user = 'root';
$pass = '';
$port = '3306';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}
?>
