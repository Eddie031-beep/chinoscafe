<?php
// includes/header.php
session_start(); // ← AGREGAR ESTA LÍNEA AL INICIO

$base = '/chinoscafe';
$current = $_SERVER['REQUEST_URI'] ?? '';
function active($path){ 
    global $current; 
    return strpos($current, $path) !== false ? 'class="active"' : ''; 
}

// Usar función helper para el contador del carrito
require_once("helpers.php");
$cart_count = getCartCount();
?>
<header class="navbar">
  <div class="nav-container">
    <a href="<?= $base ?>/views/index.php" class="logo" aria-label="Chinos Café">
      <img src="<?= $base ?>/assets/img/Logo.jpg" alt="Chinos Café">
    </a>
    <nav class="menu">
      <a <?= active('/views/index.php') ?> href="<?= $base ?>/views/index.php">Inicio</a>
      <a <?= active('/views/tienda.php') ?> href="<?= $base ?>/views/tienda.php">Tienda</a>
      <a <?= active('/views/inventario.php') ?> href="<?= $base ?>/views/inventario.php">Inventario</a>
      <a <?= active('/views/proveedores.php') ?> href="<?= $base ?>/views/proveedores.php">Proveedores</a>
      <a <?= active('/views/ventas.php') ?> href="<?= $base ?>/views/ventas.php">Ventas</a>
      <a href="<?= $base ?>/views/cart.php" class="cart-link">
        🛒 Carrito 
        <?php if ($cart_count > 0): ?>
          <span class="cart-count"><?= $cart_count ?></span>
        <?php endif; ?>
      </a>
      <a <?= active('/views/contacto.php') ?> href="<?= $base ?>/views/contacto.php">Contacto</a>
    </nav>
  </div>
</header>

<style>
.cart-count {
  background: var(--cafe-claro);
  color: #fff;
  border-radius: 50%;
  padding: 2px 6px;
  font-size: 0.75rem;
  margin-left: 5px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 18px;
  height: 18px;
  line-height: 1;
  position: relative;
  top: -1px;
}

/* Mejorar el enlace del carrito */
.cart-link {
  display: flex;
  align-items: center;
  gap: 5px;
}

/* Asegurar que los enlaces activos se vean bien */
.menu a.active {
  color: var(--cafe-claro) !important;
  opacity: 1 !important;
}

.menu a:hover {
  color: var(--cafe-claro) !important;
  opacity: 1 !important;
}
</style>