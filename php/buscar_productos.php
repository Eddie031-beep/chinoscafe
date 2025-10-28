<?php
// php/buscar_productos.php
header('Content-Type: application/json; charset=utf-8');
require_once("../config/db.php");

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($query) < 2) {
    echo json_encode(['ok' => false, 'msg' => 'Query muy corta']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT id, nombre, precio, imagen, categoria
        FROM productos 
        WHERE activo = 1 
        AND (nombre LIKE :query OR descripcion LIKE :query OR categoria LIKE :query)
        ORDER BY nombre ASC
        LIMIT 10
    ");
    
    $searchTerm = '%' . $query . '%';
    $stmt->execute([':query' => $searchTerm]);
    
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'ok' => true,
        'productos' => $productos
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'ok' => false,
        'msg' => 'Error en la búsqueda'
    ]);
}
?>