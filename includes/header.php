<?php
// includes/header.php
$base = '/chinoscafe'; // <-- ajusta si tu carpeta raíz cambia
$current = $_SERVER['REQUEST_URI'] ?? '';
function active($path){ global $current; return strpos($current, $path)!==false ? 'class="active"' : ''; }
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
      <a href="<?= $base ?>/views/cart.php">Carrito</a>
      <a href="<?= $base ?>/php/guardar_contacto.php">Contacto</a>
    </nav>
  </div>
</header>
