<?php
// includes/helpers.php

/**
 * Calcula totales del carrito
 */
function calcularTotalesCarrito($cart) {
    $subtotal = 0;
    $total_items = 0;
    
    if (!empty($cart)) {
        foreach($cart as $item) {
            $subtotal += $item['precio'] * $item['cantidad'];
            $total_items += $item['cantidad'];
        }
    }
    
    $impuesto = $subtotal * 0.07;
    $total = $subtotal + $impuesto;
    
    return [
        'subtotal' => $subtotal,
        'impuesto' => $impuesto,
        'total' => $total,
        'total_items' => $total_items
    ];
}

/**
 * Obtener contador del carrito
 */
function getCartCount() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    $cart_count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cart_count += $item['cantidad'];
        }
    }
    return $cart_count;
}

/**
 * Sanitizar entrada - NOMBRE CORREGIDO
 */
function sanitizar($data) {
    if ($data === null) {
        return '';
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Formatear precio
 */
function formatPrecio($precio) {
    return '$' . number_format($precio, 2);
}

/**
 * Verificar si un producto existe y tiene stock
 */
function verificarProducto($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ? AND activo = 1");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Función alternativa con nombre común - por si hay confusión
 */
function sanitize($data) {
    return sanitizar($data);
}
?>