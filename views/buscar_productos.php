<?php
// php/buscar_productos.php
header('Content-Type: application/json; charset=utf-8');
require_once("../config/db.php");

$query = $_GET['q'] ?? '';
$query = trim($query);

if (strlen($query) < 2) {
    echo json_encode(['ok' => false, 'msg' => 'Query muy corta']);
    exit;
}

try {
    $sql = "SELECT id, nombre, descripcion, precio, imagen, categoria 
            FROM productos 
            WHERE activo = 1 
            AND (nombre LIKE :query OR descripcion LIKE :query OR categoria LIKE :query)
            LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $searchTerm = "%{$query}%";
    $stmt->execute([':query' => $searchTerm]);
    
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'ok' => true,
        'productos' => $productos,
        'count' => count($productos)
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['ok' => false, 'msg' => 'Error en búsqueda']);
}