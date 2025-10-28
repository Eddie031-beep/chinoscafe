<?php
// includes/header.php
$base = '/chinoscafe';
$current = $_SERVER['REQUEST_URI'] ?? '';
function active($path){ 
    global $current; 
    return strpos($current, $path) !== false ? 'class="active"' : ''; 
}

// Requerir helpers
require_once(__DIR__ . "/helpers.php");
safe_session_start();

$cart_count = getCartCount();
$usuario_logueado = isset($_SESSION['usuario_id']);
$usuario_rol = $_SESSION['usuario_rol'] ?? '';
$usuario_nombre = $_SESSION['usuario_nombre'] ?? '';
?>
<header class="navbar">
  <div class="nav-container">
    <!-- LOGO -->
    <a href="<?= $base ?>/views/index.php" class="logo" aria-label="Chinos Café">
      <img src="<?= $base ?>/assets/img/Logo.jpg" alt="Chinos Café">
    </a>

    <!-- MENÚ PRINCIPAL (desktop/mobile) -->
    <nav class="menu" id="mainMenu">
      <a <?= active('/views/index.php') ?> href="<?= $base ?>/views/index.php">Inicio</a>
      <a <?= active('/views/tienda.php') ?> href="<?= $base ?>/views/tienda.php">Tienda</a>
      <a <?= active('/views/sucursales.php') ?> href="<?= $base ?>/views/sucursales.php">Sucursales</a>
      <a <?= active('/views/historia.php') ?> href="<?= $base ?>/views/historia.php">Nuestra Historia</a>

      <?php if ($usuario_logueado && $usuario_rol === 'admin'): ?>
        <a <?= active('/views/inventario.php') ?> href="<?= $base ?>/views/inventario.php">Inventario</a>
        <a <?= active('/views/proveedores.php') ?> href="<?= $base ?>/views/proveedores.php">Proveedores</a>
        <a <?= active('/views/ventas.php') ?> href="<?= $base ?>/views/ventas.php">Ventas</a>
        <a <?= active('/views/admin_dashboard.php') ?> href="<?= $base ?>/views/admin_dashboard.php">Admin</a>
      <?php endif; ?>
    </nav>

    <!-- ACCIONES DERECHA -->
    <div class="nav-actions">
      <!-- 🔍 BUSCADOR -->
      <div class="search-container">
        <button class="search-toggle" id="searchToggle" aria-label="Buscar">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="9" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
          </svg>
        </button>
        <div class="search-panel" id="searchPanel">
          <input type="text" id="searchInput" placeholder="Buscar productos..." class="search-input">
          <div id="searchResults" class="search-results"></div>
        </div>
      </div>

      <!-- 🛒 CARRITO -->
      <a href="<?= $base ?>/views/cart.php" class="cart-link" aria-label="Carrito">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="9" cy="21" r="1"></circle>
          <circle cx="20" cy="21" r="1"></circle>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <?php if ($cart_count > 0): ?>
          <span class="cart-badge"><?= $cart_count ?></span>
        <?php endif; ?>
      </a>

      <!-- 👤 USUARIO -->
      <?php if ($usuario_logueado): ?>
        <div class="user-menu">
          <button class="user-toggle" id="userToggle">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span class="user-name"><?= htmlspecialchars($usuario_nombre) ?></span>
          </button>
          <div class="user-dropdown" id="userDropdown">
            <a href="<?= $base ?>/views/perfil.php">👤 Mi Perfil</a>
            <a href="<?= $base ?>/views/mis_pedidos.php">📦 Mis Pedidos</a>
            <a href="<?= $base ?>/php/logout.php" class="logout">🚪 Cerrar Sesión</a>
          </div>
        </div>
      <?php else: ?>
        <a href="<?= $base ?>/views/login.php" class="btn-login">Iniciar Sesión</a>
      <?php endif; ?>

      <!-- 🍔 HAMBURGUESA -->
      <button class="mobile-toggle" id="mobileToggle" aria-label="Menú">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </div>
</header>

<style>
/* ========================================= */
/* NAVBAR MODERNA CON HAMBURGUESA */
/* ========================================= */
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  background: rgba(43, 30, 23, 0.98);
  backdrop-filter: blur(10px);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  z-index: 1000;
  transition: all 0.3s ease;
}

.nav-container {
  height: 80px;
  padding: 0 6%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}

/* LOGO */
.logo img {
  height: 55px;
  width: auto;
  border-radius: 12px;
  transition: transform 0.3s ease;
}

.logo img:hover {
  transform: scale(1.05);
}

/* MENÚ PRINCIPAL */
.menu {
  display: flex;
  gap: 20px;
  align-items: center;
  flex: 1;
  justify-content: center;
  margin: 0 20px;
}

.menu a {
  color: #f8efe2;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.95rem;
  padding: 8px 15px;
  border-radius: 20px;
  transition: all 0.3s ease;
  position: relative;
}

.menu a::after {
  content: '';
  position: absolute;
  bottom: 5px;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  height: 2px;
  background: var(--cafe-claro);
  transition: width 0.3s ease;
}

.menu a:hover::after,
.menu a.active::after {
  width: 60%;
}

.menu a:hover,
.menu a.active {
  color: var(--cafe-claro);
}

/* ACCIONES DERECHA */
.nav-actions {
  display: flex;
  align-items: center;
  gap: 15px;
}

/* 🔍 BUSCADOR */
.search-container {
  position: relative;
}

.search-toggle {
  background: rgba(210, 166, 121, 0.15);
  border: 2px solid rgba(210, 166, 121, 0.3);
  color: #f8efe2;
  cursor: pointer;
  padding: 10px;
  border-radius: 50%;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
}

.search-toggle:hover {
  background: rgba(210, 166, 121, 0.3);
  border-color: var(--cafe-claro);
  transform: scale(1.05);
}

.search-panel {
  position: absolute;
  top: 120%;
  right: 0;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  padding: 15px;
  min-width: 380px;
  max-width: 90vw;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.3s ease;
  z-index: 1001;
}

.search-panel.active {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.search-input {
  width: 100%;
  padding: 12px 15px;
  border: 2px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.search-input:focus {
  outline: none;
  border-color: var(--cafe-medio);
}

.search-results {
  max-height: 400px;
  overflow-y: auto;
  margin-top: 10px;
}

.search-result-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s;
  text-decoration: none;
  color: inherit;
}

.search-result-item:hover {
  background: #f8f6f1;
}

.search-result-img {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 8px;
}

.search-result-info {
  flex: 1;
}

.search-result-name {
  font-weight: 600;
  color: var(--cafe-oscuro);
  margin-bottom: 4px;
}

.search-result-price {
  color: var(--cafe-medio);
  font-weight: 700;
}

/* 🛒 CARRITO */
.cart-link {
  position: relative;
  color: #f8efe2;
  text-decoration: none;
  padding: 10px;
  border-radius: 50%;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
}

.cart-link:hover {
  background: rgba(210, 166, 121, 0.2);
  transform: scale(1.1);
}

.cart-badge {
  position: absolute;
  top: 0;
  right: 0;
  background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
  color: #fff;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
  font-weight: 700;
  border: 2px solid var(--cafe-oscuro);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.1); }
}

/* 👤 USUARIO */
.user-menu {
  position: relative;
}

.user-toggle {
  background: rgba(210, 166, 121, 0.15);
  border: none;
  color: #f8efe2;
  padding: 8px 15px;
  border-radius: 20px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 500;
  transition: all 0.3s ease;
}

.user-toggle:hover {
  background: rgba(210, 166, 121, 0.3);
  transform: translateY(-2px);
}

.user-name {
  max-width: 120px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.user-dropdown {
  position: absolute;
  top: 120%;
  right: 0;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  padding: 10px;
  min-width: 200px;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.3s ease;
}

.user-dropdown.active {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.user-dropdown a {
  display: block;
  padding: 10px 15px;
  color: var(--texto);
  text-decoration: none;
  border-radius: 8px;
  transition: background 0.2s;
}

.user-dropdown a:hover {
  background: #f8f6f1;
}

.user-dropdown a.logout {
  color: #dc3545;
  border-top: 1px solid #eee;
  margin-top: 5px;
  padding-top: 15px;
}

.btn-login {
  background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro));
  padding: 8px 20px !important;
  border-radius: 20px;
  font-weight: 600;
  transition: all 0.3s ease;
  color: #fff;
  text-decoration: none;
}

.btn-login:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(210, 166, 121, 0.4);
}

/* 🍔 HAMBURGUESA */
.mobile-toggle {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 5px;
  z-index: 1002;
}

.mobile-toggle span {
  width: 25px;
  height: 3px;
  background: #f8efe2;
  border-radius: 3px;
  transition: all 0.3s ease;
}

.mobile-toggle.active span:nth-child(1) {
  transform: rotate(45deg) translate(7px, 7px);
}

.mobile-toggle.active span:nth-child(2) {
  opacity: 0;
}

.mobile-toggle.active span:nth-child(3) {
  transform: rotate(-45deg) translate(7px, -7px);
}

/* ========================================= */
/* RESPONSIVE */
/* ========================================= */
@media (max-width: 1024px) {
  .menu {
    position: fixed;
    top: 80px;
    left: -100%;
    width: 280px;
    height: calc(100vh - 80px);
    flex-direction: column;
    justify-content: flex-start;
    align-items: stretch;
    background: rgba(43, 30, 23, 0.98);
    backdrop-filter: blur(10px);
    padding: 20px;
    gap: 10px;
    transition: left 0.3s ease;
    overflow-y: auto;
    box-shadow: 2px 0 20px rgba(0, 0, 0, 0.3);
  }
  
  .menu.active {
    left: 0;
  }
  
  .menu a {
    width: 100%;
    text-align: left;
    padding: 12px 15px;
  }
  
  .mobile-toggle {
    display: flex;
  }
  
  .user-name {
    display: none;
  }
  
  .search-panel {
    min-width: 280px;
    right: auto;
    left: 50%;
    transform: translateX(-50%) translateY(-10px);
  }
  
  .search-panel.active {
    transform: translateX(-50%) translateY(0);
  }
}

@media (max-width: 480px) {
  .nav-container {
    padding: 0 4%;
  }
  
  .logo img {
    height: 45px;
  }
  
  .search-panel {
    min-width: calc(100vw - 40px);
  }
  
  .nav-actions {
    gap: 10px;
  }
}
</style>

<script>
// 🔍 FUNCIONALIDAD DE BÚSQUEDA
const searchToggle = document.getElementById('searchToggle');
const searchPanel = document.getElementById('searchPanel');
const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');

searchToggle?.addEventListener('click', () => {
  searchPanel.classList.toggle('active');
  if (searchPanel.classList.contains('active')) {
    searchInput.focus();
  }
});

// Cerrar buscador al hacer clic fuera
document.addEventListener('click', (e) => {
  if (!searchPanel?.contains(e.target) && !searchToggle?.contains(e.target)) {
    searchPanel?.classList.remove('active');
  }
});

// Búsqueda en tiempo real
let searchTimeout;
searchInput?.addEventListener('input', (e) => {
  clearTimeout(searchTimeout);
  const query = e.target.value.trim();
  
  if (query.length < 2) {
    searchResults.innerHTML = '';
    return;
  }
  
  searchTimeout = setTimeout(async () => {
    try {
      const response = await fetch(`<?= $base ?>/php/buscar_productos.php?q=${encodeURIComponent(query)}`);
      const data = await response.json();
      
      if (data.ok && data.productos.length > 0) {
        searchResults.innerHTML = data.productos.map(p => `
          <a href="<?= $base ?>/views/tienda.php#producto-${p.id}" class="search-result-item">
            <img src="<?= $base ?>/assets/img/${p.imagen}" alt="${p.nombre}" class="search-result-img">
            <div class="search-result-info">
              <div class="search-result-name">${p.nombre}</div>
              <div class="search-result-price">$${parseFloat(p.precio).toFixed(2)}</div>
            </div>
          </a>
        `).join('');
      } else {
        searchResults.innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">No se encontraron productos</div>';
      }
    } catch (error) {
      console.error('Error en búsqueda:', error);
    }
  }, 300);
});

// 👤 MENÚ DE USUARIO
const userToggle = document.getElementById('userToggle');
const userDropdown = document.getElementById('userDropdown');

userToggle?.addEventListener('click', () => {
  userDropdown.classList.toggle('active');
});

document.addEventListener('click', (e) => {
  if (!userDropdown?.contains(e.target) && !userToggle?.contains(e.target)) {
    userDropdown?.classList.remove('active');
  }
});

// 🍔 MENÚ HAMBURGUESA
const mobileToggle = document.getElementById('mobileToggle');
const mainMenu = document.getElementById('mainMenu');

mobileToggle?.addEventListener('click', () => {
  mainMenu.classList.toggle('active');
  mobileToggle.classList.toggle('active');
  
  // Cerrar otros menús
  searchPanel?.classList.remove('active');
  userDropdown?.classList.remove('active');
});

// Cerrar menú al hacer clic en un enlace (mobile)
mainMenu?.querySelectorAll('a').forEach(link => {
  link.addEventListener('click', () => {
    if (window.innerWidth <= 1024) {
      mainMenu.classList.remove('active');
      mobileToggle.classList.remove('active');
    }
  });
});

// Actualizar contador del carrito
window.updateCartBadge = function(count) {
  const cartLink = document.querySelector('.cart-link');
  let badge = cartLink?.querySelector('.cart-badge');
  
  if (count > 0) {
    if (!badge) {
      badge = document.createElement('span');
      badge.className = 'cart-badge';
      cartLink.appendChild(badge);
    }
    badge.textContent = count;
  } else {
    badge?.remove();
  }
};
</script>