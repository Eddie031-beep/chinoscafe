<?php
// includes/helpers.php

/**
 * Iniciar sesión de manera segura
 */
function safe_session_start() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

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
    safe_session_start();
    
    $cart_count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cart_count += $item['cantidad'];
        }
    }
    return $cart_count;
}

/**
 * Sanitizar entrada
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
 * Función alternativa con nombre común
 */
function sanitize($data) {
    return sanitizar($data);
}

/**
 * Verificar si el usuario está logueado
 */
function usuarioLogueado() {
    safe_session_start();
    return isset($_SESSION['usuario_id']);
}

/**
 * Verificar si el usuario es administrador
 */
function esAdmin() {
    safe_session_start();
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
}

/**
 * Redirigir si no es administrador
 */
function requerirAdmin() {
    if (!esAdmin()) {
        header("Location: ../views/login.php");
        exit;
    }
}

/**
 * Redirigir si no está logueado
 */
function requerirLogin() {
    if (!usuarioLogueado()) {
        header("Location: ../views/login.php");
        exit;
    }
}
?>