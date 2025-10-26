<?php
require_once("../config/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $mensaje = $_POST['mensaje'];

    $sql = "INSERT INTO contactos (nombre, correo, mensaje) VALUES (:nombre, :correo, :mensaje)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $nombre,
        ':correo' => $correo,
        ':mensaje' => $mensaje
    ]);

    echo "<script>alert('✅ Mensaje enviado correctamente'); window.location='../views/index.php';</script>";
}
?>
