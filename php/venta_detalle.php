<?php
// php/venta_detalle.php
header('Content-Type: application/json; charset=utf-8');
require_once("../config/db.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'ID inválido']);
    exit;
}

try {
    // Obtener información de la venta
    $stmtVenta = $pdo->prepare("SELECT * FROM ventas WHERE id = ?");
    $stmtVenta->execute([$id]);
    $venta = $stmtVenta->fetch(PDO::FETCH_ASSOC);
    
    if (!$venta) {
        echo json_encode(['ok' => false, 'msg' => 'Venta no encontrada']);
        exit;
    }
    
    // Obtener detalle de productos
    $stmtProductos = $pdo->prepare("SELECT * FROM venta_detalle WHERE id_venta = ? ORDER BY id");
    $stmtProductos->execute([$id]);
    $productos = $stmtProductos->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'ok' => true,
        'venta' => $venta,
        'productos' => $productos
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['ok' => false, 'msg' => 'Error: ' . $e->getMessage()]);
}