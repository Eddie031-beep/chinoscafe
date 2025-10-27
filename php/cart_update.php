<?php
// php/cart_update.php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Recibir datos del producto
$id  = isset($_POST['id'])  ? (int)$_POST['id']  : 0;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;

// Validar
if (!isset($_SESSION['cart'][$id]) || $qty < 0) {
    echo json_encode(['ok' => false, 'msg' => 'Petición inválida']);
    exit;
}

// Actualizar o eliminar
if ($qty === 0) {
    unset($_SESSION['cart'][$id]);
} else {
    $_SESSION['cart'][$id]['cantidad'] = $qty;
}

// Calcular total actualizado del carrito
$total = 0;
$cantidad_total = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['precio'] * $item['cantidad'];
        $cantidad_total += $item['cantidad'];
    }
}

// Devolver respuesta en JSON
echo json_encode([
    'ok' => true,
    'total' => number_format($total, 2, '.', ''),
    'count' => $cantidad_total
]);
