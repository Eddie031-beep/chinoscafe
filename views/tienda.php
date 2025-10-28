<?php 
session_start();
require_once("../config/db.php");
global $pdo;
?>

<?php
// Base URL del proyecto: de /chinoscafe/views -> /chinoscafe
$__web_current = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');   // /chinoscafe/views
$__web_root    = rtrim(dirname($__web_current), '/');            // /chinoscafe
$IMG_BASE      = $__web_root . '/img/';                          // /chinoscafe/img/
$ASSETS_IMG    = $__web_root . '/assets/img/';                   // fallback

// helper para pintar <img> con fallback silencioso
function img_src_producto($fname, $IMG_BASE, $ASSETS_IMG) {
    $fname = htmlspecialchars($fname ?: 'default.jpg', ENT_QUOTES, 'UTF-8');
    // Usamos ruta absoluta desde raíz del proyecto para evitar problemas de niveles
    $primary = $IMG_BASE . $fname;       // /chinoscafe/img/archivo.jpg
    $fallback = $ASSETS_IMG . $fname;    // /chinoscafe/assets/img/archivo.jpg
    $placeholder = $ASSETS_IMG . 'placeholder.jpg';
    // onerror va encadenando fallbacks
    return $primary . "\" onerror=\"this.onerror=null;this.src='" . $fallback .
           "';this.onerror=function(){this.src='" . $placeholder . "';}";
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/tienda.css">
    <style>

        
        /* FILTROS MODERNOS */
        .filtros-modernos {
            background: #fff;
            padding: 30px 7%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 80px;
            z-index: 100;
        }
        
        .filtros-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .filtros-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .filtros-title {
            font-size: 1.2rem;
            color: var(--cafe-oscuro);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .search-box {
            position: relative;
            flex: 1;
            max-width: 400px;
        }
        
        .search-input {
            width: 100%;
            padding: 12px 45px 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--cafe-medio);
            box-shadow: 0 0 0 3px rgba(210, 166, 121, 0.1);
        }
        
        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--cafe-medio);
            pointer-events: none;
        }
        
        .filtros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
        }
        
        .filtro-card {
            background: linear-gradient(135deg, #f8f6f1 0%, #fff 100%);
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .filtro-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--cafe-medio), var(--cafe-claro));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .filtro-card.activo {
            background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro));
            border-color: var(--cafe-medio);
            color: #fff;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(210, 166, 121, 0.4);
        }
        
        .filtro-card.activo::before {
            transform: scaleX(1);
        }
        
        .filtro-card:hover:not(.activo) {
            border-color: var(--cafe-medio);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .filtro-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .filtro-nombre {
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .filtro-count {
            font-size: 0.85rem;
            opacity: 0.8;
            margin-top: 5px;
        }
        
        /* PRODUCTOS GRID MEJORADO */
        .productos-section {
            padding: 50px 7%;
            background: var(--crema);
        }
        
        .productos-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .productos-info {
            font-size: 1.1rem;
            color: #666;
        }
        
        .sort-select {
            padding: 10px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 1rem;
            cursor: pointer;
            background: #fff;
            transition: all 0.3s ease;
        }
        
        .sort-select:focus {
            outline: none;
            border-color: var(--cafe-medio);
        }
        
        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .producto-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            cursor: pointer;
        }
        
        .producto-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .producto-image-wrapper {
            position: relative;
            height: 250px;
            overflow: hidden;
            background: var(--crema);
        }
        
        .producto-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        
        .producto-card:hover .producto-image {
            transform: scale(1.1);
        }
        
        .producto-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--cafe-medio);
            color: #fff;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(139, 94, 60, 0.4);
        }
        
        .producto-actions-quick {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 10px;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        
        .producto-card:hover .producto-actions-quick {
            opacity: 1;
            transform: translateY(0);
        }
        
        .btn-quick {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.95);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        .btn-quick:hover {
            background: var(--cafe-medio);
            color: #fff;
            transform: scale(1.1);
        }
        
        .producto-info {
            padding: 25px;
        }
        
        .producto-categoria {
            color: var(--cafe-medio);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        
        .producto-nombre {
            font-size: 1.3rem;
            color: var(--cafe-oscuro);
            margin: 0 0 10px 0;
            font-weight: 600;
            min-height: 50px;
        }
        
        .producto-descripcion {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 45px;
        }
        
        .producto-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .producto-precio {
            font-size: 1.8rem;
            color: var(--cafe-medio);
            font-weight: 700;
        }
        
        .btn-add-cart {
            background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro));
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(210, 166, 121, 0.3);
        }
        
        .btn-add-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(210, 166, 121, 0.5);
        }
        
        .no-results {
            text-align: center;
            padding: 80px 20px;
            color: #999;
        }
        
        .no-results svg {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
        
        @media (max-width: 768px) {
            .filtros-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .search-box {
                max-width: 100%;
            }
        }
            /* 🔧 Forzar la ruta del hero a /img/ (tu CSS en assets usa ../img relativo a assets) */
    .page-tienda .hero-tienda{
    background: url('../img/hero-cafe2.jpg') center/cover no-repeat fixed !important;
    }
    </style>
</head>
<body class="page-tienda">
    <?php include("../includes/header.php"); ?>

    <!-- HERO -->
    <section class="hero-tienda">
        <div class="contenido-hero">
            <h1>Explora Nuestro Menú</h1>
            <p>Encuentra tu bebida perfecta entre nuestra selecta variedad</p>
        </div>
    </section>

    <!-- FILTROS MODERNOS -->
    <section class="filtros-modernos">
        <div class="filtros-container">
            <div class="filtros-header">
                <h2 class="filtros-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtrar por Categoría
                </h2>
                
                <div class="search-box">
                    <input type="text" id="searchProducts" class="search-input" placeholder="Buscar productos...">
                    <svg class="search-icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </div>
            </div>
            
            <div class="filtros-grid">
                <div class="filtro-card activo" data-categoria="todos">
                    <div class="filtro-icon">🎯</div>
                    <div class="filtro-nombre">Todos</div>
                    <div class="filtro-count" id="count-todos">0 productos</div>
                </div>
                
                <div class="filtro-card" data-categoria="Bebida Caliente">
                    <div class="filtro-icon">☕</div>
                    <div class="filtro-nombre">Bebidas Calientes</div>
                    <div class="filtro-count" id="count-calientes">0</div>
                </div>
                
                <div class="filtro-card" data-categoria="Bebida Fría">
                    <div class="filtro-icon">🧊</div>
                    <div class="filtro-nombre">Bebidas Frías</div>
                    <div class="filtro-count" id="count-frias">0</div>
                </div>
                
                <div class="filtro-card" data-categoria="Postre">
                    <div class="filtro-icon">🍰</div>
                    <div class="filtro-nombre">Postres</div>
                    <div class="filtro-count" id="count-postres">0</div>
                </div>
                
                <div class="filtro-card" data-categoria="Panadería">
                    <div class="filtro-icon">🥐</div>
                    <div class="filtro-nombre">Panadería</div>
                    <div class="filtro-count" id="count-panaderia">0</div>
                </div>
            </div>
        </div>
    </section>

    <!-- PRODUCTOS -->
    <main id="productos" class="productos-section">
        <div class="productos-header">
            <p class="productos-info">
                Mostrando <strong id="productos-count">0</strong> producto(s)
            </p>
            
            <select class="sort-select" id="sortSelect">
                <option value="default">Ordenar por</option>
                <option value="price-asc">Precio: Menor a Mayor</option>
                <option value="price-desc">Precio: Mayor a Menor</option>
                <option value="name-asc">Nombre: A-Z</option>
                <option value="name-desc">Nombre: Z-A</option>
            </select>
        </div>

        <!-- 🔽 BLOQUE NUEVO PARA MOSTRAR PRODUCTOS -->
        <?php
        try {
            $stmt = $pdo->query("SELECT * FROM productos WHERE activo = 1");
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<p class='no-results'>Error al cargar productos: " . $e->getMessage() . "</p>";
            $productos = [];
        }
        ?>

        <div class="productos-grid">
        <?php if (count($productos) > 0): ?>
            <?php foreach ($productos as $p): ?>
                <div class="producto-card" data-categoria="<?= htmlspecialchars($p['categoria']) ?>">
                    <div class="producto-image-wrapper">
                        <img src="<?= img_src_producto($p['imagen'], $IMG_BASE, $ASSETS_IMG) ?>" 
                            alt="<?= htmlspecialchars($p['nombre']) ?>" class="producto-image">

                        <span class="producto-badge"><?= htmlspecialchars($p['categoria']) ?></span>
                    </div>
                    <div class="producto-info">
                        <p class="producto-categoria"><?= htmlspecialchars($p['categoria']) ?></p>
                        <h3 class="producto-nombre"><?= htmlspecialchars($p['nombre']) ?></h3>
                        <p class="producto-descripcion"><?= htmlspecialchars($p['descripcion']) ?></p>
                        <div class="producto-footer">
                            <span class="producto-precio">$<?= number_format($p['precio'], 2) ?></span>
                            <form method="post" action="../php/cart_add.php">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" class="btn-add-cart">🛒 Añadir</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 105.656-5.656L12 8.343l-2.828 2.829a4 4 0 000 5.656z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12v.01"/>
                </svg>
                <p>No se encontraron productos disponibles.</p>
            </div>
        <?php endif; ?>
        </div>
        <!-- 🔼 FIN BLOQUE NUEVO -->

    </main>

    <?php include("../includes/footer.php"); ?>
    <script src="../js/tienda.js"></script>

    <script>
    /* ===========================
   PARCHE LIGERO PARA TIENDA
   - Debounce en búsqueda
   - Añadir al carrito vía fetch (sin navegar)
   =========================== */

/* ---- 1) BÚSQUEDA CON DEBOUNCE (evita lag) ---- */
(function() {
  const input = document.getElementById('searchProducts');
  if (!input) return;

  const grid = document.querySelector('.productos-grid');
  if (!grid) return;

  const items = Array.from(grid.querySelectorAll('.producto-card'));
  const nombreSel = '.producto-nombre';
  const descSel = '.producto-descripcion';

  let t = null;
  const doFilter = () => {
    const q = (input.value || '').trim().toLowerCase();
    let visibles = 0;
    items.forEach(card => {
      const nom = (card.querySelector(nombreSel)?.textContent || '').toLowerCase();
      const des = (card.querySelector(descSel)?.textContent || '').toLowerCase();
      const show = !q || nom.includes(q) || des.includes(q);
      card.style.display = show ? '' : 'none';
      if (show) visibles++;
    });
    const countEl = document.getElementById('productos-count');
    if (countEl) countEl.textContent = visibles;
    // Actualiza contadores por categoría
    updateCategoryCounters();
  };

  // Debounce: ejecuta 250ms después de dejar de teclear
  const debounced = () => {
    if (t) clearTimeout(t);
    t = setTimeout(doFilter, 250);
  };

  // Forzamos nuestro handler (captura) para que tenga prioridad
  input.addEventListener('input', debounced, true);

  function updateCategoryCounters() {
    const cats = {
      'Bebida Caliente': 0,
      'Bebida Fría': 0,
      'Postre': 0,
      'Panadería': 0
    };
    let totalVisibles = 0;

    items.forEach(card => {
      if (card.style.display !== 'none') {
        totalVisibles++;
        const c = card.getAttribute('data-categoria') || '';
        if (c in cats) cats[c]++;
      }
    });

    const setTxt = (id, txt) => {
      const el = document.getElementById(id);
      if (el) el.textContent = txt;
    };

    setTxt('count-todos', totalVisibles + ' producto(s)');
    setTxt('count-calientes', cats['Bebida Caliente']);
    setTxt('count-frias', cats['Bebida Fría']);
    setTxt('count-postres', cats['Postre']);
    setTxt('count-panaderia', cats['Panadería']);
  }

  // Inicial: cuenta visibles actuales
  doFilter();
})();

/* ---- 2) AÑADIR AL CARRITO POR AJAX (evita ver JSON en pantalla) ---- */
(function () {
  // Intercepta cualquier form que apunte a php/cart_add.php
  document.addEventListener('submit', async function(e) {
    const form = e.target;
    if (!form.matches('form[action$="php/cart_add.php"]')) return;

    e.preventDefault(); // Evita navegar a la respuesta JSON

    try {
      const fd = new FormData(form);
      const res = await fetch(form.action, {
        method: 'POST',
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });

      // Intenta parsear a JSON; si no, cae a texto
      let data;
      const txt = await res.text();
      try { data = JSON.parse(txt); } catch { data = { ok: false, msg: txt }; }

      if (data && data.ok) {
        toast('Producto añadido al carrito');
        // Si tienes un badge de carrito con id="cart-count", lo actualiza
        const badge = document.querySelector('#cart-count, .cart-count, [data-cart-count]');
        if (badge && typeof data.count !== 'undefined') badge.textContent = data.count;
      } else {
        toast('No se pudo añadir al carrito', true);
        console.warn('Respuesta carrito:', data);
      }
    } catch (err) {
      console.error(err);
      toast('Error de red al añadir', true);
    }
  }, true); // captura: prioridad sobre otros listeners

  // Mini toast sin dependencias
  function toast(msg, error=false) {
    const t = document.createElement('div');
    t.textContent = msg;
    t.style.position = 'fixed';
    t.style.right = '20px';
    t.style.bottom = '20px';
    t.style.padding = '12px 16px';
    t.style.borderRadius = '10px';
    t.style.boxShadow = '0 8px 24px rgba(0,0,0,.15)';
    t.style.background = error ? '#b00020' : '#2e7d32';
    t.style.color = '#fff';
    t.style.zIndex = '9999';
    t.style.fontWeight = '600';
    document.body.appendChild(t);
    setTimeout(()=>{ t.style.opacity='0'; t.style.transition='opacity .3s'; }, 1600);
    setTimeout(()=> t.remove(), 2000);
  }
})();
</script>

<script>
/* ==========================================
   PARCHE LÓGICA TIENDA (sin borrar nada)
   - Filtro por categoría funciona y actualiza conteos
   - Buscador con debounce + lista y scroll al producto
   - Añadir al carrito por AJAX y sube el contador
   - Imágenes con fallback si falta archivo
========================================== */

/* ---------- Utilidades ---------- */
function $all(sel, root=document){ return Array.from(root.querySelectorAll(sel)); }
function setText(el, txt){ if(el) el.textContent = txt; }

/* ---------- 0) Mejora: fallback de imágenes ---------- */
(function attachImageFallbacks(){
  $all('.producto-card img').forEach(img=>{
    img.addEventListener('error', ()=>{
      // Fallback (ajusta si tienes un placeholder propio)
      img.src = '../assets/img/placeholder.jpg';
    }, {once:true});
  });
})();

/* ---------- 1) Filtro por categoría + conteos ---------- */
(function categoryFilter(){
  const grid = document.querySelector('.productos-grid');
  if(!grid) return;
  const cards = ()=> $all('.producto-card', grid);
  const btns  = $all('.filtro-card');

  function applyFilter(cat){
    let visibles = 0;
    cards().forEach(card=>{
      const c = card.getAttribute('data-categoria') || '';
      const show = (cat === 'todos') || (c === cat);
      card.style.display = show ? '' : 'none';
      if (show) visibles++;
    });
    const countEl = document.getElementById('productos-count');
    setText(countEl, visibles);

    // actualizar contadores por categoría visibles
    const cats = {
      'Bebida Caliente': 0,
      'Bebida Fría': 0,
      'Postre': 0,
      'Panadería': 0
    };
    cards().forEach(card=>{
      if (card.style.display !== 'none') {
        const c = card.getAttribute('data-categoria') || '';
        if (c in cats) cats[c]++;
      }
    });
    setText(document.getElementById('count-todos'),    visibles + ' producto(s)');
    setText(document.getElementById('count-calientes'), cats['Bebida Caliente']);
    setText(document.getElementById('count-frias'),     cats['Bebida Fría']);
    setText(document.getElementById('count-postres'),   cats['Postre']);
    setText(document.getElementById('count-panaderia'), cats['Panadería']);
  }

  btns.forEach(btn=>{
    btn.addEventListener('click', (e)=>{
      btns.forEach(b=>b.classList.remove('activo'));
      btn.classList.add('activo');
      applyFilter(btn.dataset.categoria || 'todos');
    }, true); // captura para tener prioridad
  });

  // inicial (Todos)
  applyFilter('todos');
})();

/* ---------- 2) Buscador con debounce + navegación al producto ---------- */
(function searchableJump(){
  const input = document.getElementById('searchProducts');
  const grid  = document.querySelector('.productos-grid');
  if (!input || !grid) return;

  const cards = $all('.producto-card', grid);
  // Asegura IDs para poder hacer scroll
  cards.forEach((card, idx)=>{
    if (!card.id) card.id = 'prod-' + (card.dataset.id || idx + 1);
  });

  // Crea lista de sugerencias simple
  const list = document.createElement('div');
  list.style.position = 'absolute';
  list.style.left = '0';
  list.style.right = '0';
  list.style.top = '110%';
  list.style.background = '#fff';
  list.style.border = '1px solid #eee';
  list.style.borderRadius = '10px';
  list.style.boxShadow = '0 12px 30px rgba(0,0,0,.08)';
  list.style.padding = '6px';
  list.style.zIndex = '9999';
  list.style.display = 'none';
  input.parentElement.style.position = 'relative';
  input.parentElement.appendChild(list);

  let t = null;
  function renderSuggestions(q){
    list.innerHTML = '';
    if (!q) { list.style.display = 'none'; return; }
    const LQ = q.toLowerCase();
    const results = cards
      .map(card=>{
        const name = (card.querySelector('.producto-nombre')?.textContent || '').trim();
        const desc = (card.querySelector('.producto-descripcion')?.textContent || '').trim();
        return {card, name, desc};
      })
      .filter(o=> o.name.toLowerCase().includes(LQ) || o.desc.toLowerCase().includes(LQ))
      .slice(0, 6);

    if (!results.length){ list.style.display='none'; return; }

    results.forEach(({card, name})=>{
      const item = document.createElement('div');
      item.textContent = name;
      item.style.padding = '10px 12px';
      item.style.borderRadius = '8px';
      item.style.cursor = 'pointer';
      item.addEventListener('mouseenter', ()=> item.style.background = '#f6f2ec');
      item.addEventListener('mouseleave', ()=> item.style.background = 'transparent');
      item.addEventListener('click', ()=>{
        list.style.display = 'none';
        input.blur();
        // Asegura que la tarjeta esté visible (por si hay filtro activo)
        $all('.filtro-card').forEach(b=> b.classList.remove('activo'));
        const allBtn = document.querySelector('.filtro-card[data-categoria="todos"]');
        if (allBtn) { allBtn.classList.add('activo'); }
        // Muestra todas
        $all('.producto-card').forEach(c=> c.style.display = '');

        // Scroll suave y foco
        card.scrollIntoView({behavior:'smooth', block:'center'});
        card.style.outline = '3px solid var(--cafe-medio)';
        setTimeout(()=> card.style.outline = 'none', 1200);
      });
      list.appendChild(item);
    });
    list.style.display = 'block';
  }

  input.addEventListener('input', ()=>{
    if (t) clearTimeout(t);
    t = setTimeout(()=> renderSuggestions(input.value.trim()), 250);
  }, true);

  document.addEventListener('click', (e)=>{
    if (!list.contains(e.target) && e.target !== input) list.style.display='none';
  });
})();

/* ---------- 3) Añadir al carrito por AJAX + actualizar dígito al instante ---------- */
(function cartAjax(){
  document.addEventListener('submit', async function(e){
    const form = e.target;
    if (!form.matches('form[action$="php/cart_add.php"]')) return;
    e.preventDefault();

    try {
      const fd = new FormData(form);
      const res = await fetch(form.action, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' }});
      const raw = await res.text();
      let data; try { data = JSON.parse(raw); } catch { data = { ok:false, msg:raw }; }

      if (data.ok){
        toast('Producto añadido al carrito');
        setCartCount(data.count);  // 🔥 sube el dígito de inmediato
      } else {
        toast(data.msg || 'No se pudo añadir', true);
      }
    } catch(err){
      console.error(err);
      toast('Error de red', true);
    }
  }, true);

  function setCartCount(n){
    let updated = false;
    // 1) IDs o data-attr comunes
    ['#cart-count', '.cart-count', '[data-cart-count]'].forEach(sel=>{
      $all(sel).forEach(el=>{ el.textContent = n; updated = true; });
    });
    // 2) Enlace al carrito "Carrito (X)"
    if (!updated) {
      const link = document.querySelector('a[href*="cart"]') || document.querySelector('a[href*="carrito"]');
      if (link) {
        const has = /\(\d+\)/.test(link.textContent);
        link.textContent = has ? link.textContent.replace(/\(\d+\)/, '('+n+')') : (link.textContent.trim() + ' ('+n+')');
        updated = true;
      }
    }
    // 3) Badge Bootstrap común
    if (!updated) {
      const badge = document.querySelector('.badge.rounded-pill') || document.querySelector('.badge');
      if (badge) badge.textContent = n;
    }
  }

  function toast(msg, error=false){
    const t = document.createElement('div');
    t.textContent = msg;
    Object.assign(t.style, {
      position:'fixed', right:'20px', bottom:'20px', padding:'12px 16px',
      borderRadius:'10px', boxShadow:'0 8px 24px rgba(0,0,0,.15)',
      background: error ? '#b00020' : '#2e7d32', color:'#fff', zIndex:'9999', fontWeight:'600'
    });
    document.body.appendChild(t);
    setTimeout(()=>{ t.style.opacity='0'; t.style.transition='opacity .3s'; }, 1600);
    setTimeout(()=> t.remove(), 2000);
  }
})();
</script>


</body>
</html>