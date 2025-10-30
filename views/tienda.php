<?php 
session_start();
require_once("../config/db.php");
global $pdo;

// Base URL
$__web_current = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$__web_root    = rtrim(dirname($__web_current), '/');
$IMG_BASE      = $__web_root . '/img/';
$ASSETS_IMG    = $__web_root . '/assets/img/';

function img_src_producto($fname, $IMG_BASE, $ASSETS_IMG) {
    $fname = htmlspecialchars($fname ?: 'default.jpg', ENT_QUOTES, 'UTF-8');
    $primary = $IMG_BASE . $fname;
    $fallback = $ASSETS_IMG . $fname;
    $placeholder = $ASSETS_IMG . 'placeholder.jpg';
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
    <style>
        /* HERO */
        .hero-tienda {
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            background: url('../img/hero-cafe2.jpg') center/cover no-repeat fixed;
            position: relative;
            margin-top: -90px;
        }
        
        .hero-tienda::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
        }
        
        .contenido-hero {
            position: relative;
            z-index: 1;
            padding: 20px;
        }
        
        .contenido-hero h1 {
            font-size: clamp(2.5rem, 6vw, 4rem);
            margin: 0 0 15px 0;
        }
        
        .contenido-hero p {
            font-size: clamp(1rem, 2vw, 1.3rem);
            opacity: 0.95;
        }

        /* SECCIÓN PRINCIPAL */
        .tienda-container {
            padding: 60px 7%;
            background: var(--crema);
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-title {
            font-size: clamp(2rem, 5vw, 3rem);
            color: var(--cafe-oscuro);
            margin: 0 0 15px 0;
        }
        
        .section-subtitle {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 40px;
        }

        /* FILTROS MEJORADOS - DEBAJO DEL TÍTULO */
        .filtros-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 50px;
            padding: 0 20px;
        }
        
        .filtro-btn {
            background: #fff;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            padding: 12px 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            color: var(--cafe-oscuro);
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        
        .filtro-btn:hover {
            border-color: var(--cafe-medio);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }
        
        .filtro-btn.activo {
            background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro));
            border-color: var(--cafe-medio);
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(210, 166, 121, 0.4);
        }
        
        .filtro-icon {
            font-size: 1.3rem;
        }

        /* GRID DE PRODUCTOS CON SCROLL VERTICAL */
        .productos-wrapper {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            max-height: 800px; /* ✅ Altura máxima para el contenedor */
            overflow-y: auto; /* ✅ Scroll vertical */
            overflow-x: hidden;
        }
        
        /* ✅ Scrollbar personalizado */
        .productos-wrapper::-webkit-scrollbar {
            width: 10px;
        }
        
        .productos-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .productos-wrapper::-webkit-scrollbar-thumb {
            background: var(--cafe-medio);
            border-radius: 10px;
        }
        
        .productos-wrapper::-webkit-scrollbar-thumb:hover {
            background: var(--cafe-oscuro);
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
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }
        
        .producto-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            border-color: var(--cafe-claro);
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
            right: 15px;
            background: var(--cafe-medio);
            color: #fff;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(139, 94, 60, 0.4);
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
            padding: 12px 24px;
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

        @media (max-width: 768px) {
            .filtros-container {
                gap: 10px;
            }
            
            .filtro-btn {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
            
            .productos-wrapper {
                max-height: 600px; /* Menos altura en móvil */
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <!-- HERO -->
    <section class="hero-tienda">
        <div class="contenido-hero">
            <h1>Explora Nuestro Menú</h1>
            <p>Encuentra tu bebida perfecta entre nuestra selecta variedad</p>
        </div>
    </section>

    <!-- CONTENEDOR PRINCIPAL -->
    <main class="tienda-container">
        <!-- HEADER CON TÍTULO -->
        <div class="section-header">
            <h2 class="section-title">☕ Explorar Productos</h2>
            <p class="section-subtitle">Descubre nuestras deliciosas opciones</p>
        </div>

        <!-- FILTROS DEBAJO DEL TÍTULO -->
        <div class="filtros-container">
            <button class="filtro-btn activo" data-categoria="todos">
                <span class="filtro-icon">🎯</span>
                <span>Todos</span>
            </button>
            
            <button class="filtro-btn" data-categoria="Bebida Caliente">
                <span class="filtro-icon">☕</span>
                <span>Bebidas Calientes</span>
            </button>
            
            <button class="filtro-btn" data-categoria="Bebida Fría">
                <span class="filtro-icon">🧊</span>
                <span>Bebidas Frías</span>
            </button>
            
            <button class="filtro-btn" data-categoria="Postre">
                <span class="filtro-icon">🍰</span>
                <span>Postres</span>
            </button>
            
            <button class="filtro-btn" data-categoria="Panadería">
                <span class="filtro-icon">🥐</span>
                <span>Panadería</span>
            </button>
        </div>

        <!-- PRODUCTOS CON SCROLL -->
        <div class="productos-wrapper">
            <div class="productos-grid">
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM productos WHERE activo = 1 ORDER BY nombre ASC");
                    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($productos) > 0):
                        foreach ($productos as $p):
                ?>
                    <div class="producto-card" data-categoria="<?= htmlspecialchars($p['categoria']) ?>" data-id="<?= $p['id'] ?>">
                        <div class="producto-image-wrapper">
                            <img src="<?= img_src_producto($p['imagen'], $IMG_BASE, $ASSETS_IMG) ?>" 
                                 alt="<?= htmlspecialchars($p['nombre']) ?>" 
                                 class="producto-image">
                            <span class="producto-badge"><?= htmlspecialchars($p['categoria']) ?></span>
                        </div>
                        <div class="producto-info">
                            <p class="producto-categoria"><?= htmlspecialchars($p['categoria']) ?></p>
                            <h3 class="producto-nombre"><?= htmlspecialchars($p['nombre']) ?></h3>
                            <p class="producto-descripcion"><?= htmlspecialchars($p['descripcion']) ?></p>
                            <div class="producto-footer">
                                <span class="producto-precio">$<?= number_format($p['precio'], 2) ?></span>
                                <button class="btn-add-cart" data-id="<?= $p['id'] ?>">
                                    🛒 Añadir
                                </button>
                            </div>
                        </div>
                    </div>
                <?php 
                        endforeach;
                    else:
                ?>
                    <div class="no-results">
                        <p>No hay productos disponibles.</p>
                    </div>
                <?php 
                    endif;
                } catch (PDOException $e) {
                    echo "<p class='no-results'>Error al cargar productos.</p>";
                }
                ?>
            </div>
        </div>
    </main>

    <?php include("../includes/footer.php"); ?>

    <script>
        /* ===== FILTROS DE CATEGORÍA ===== */
        document.querySelectorAll('.filtro-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                // Actualizar botón activo
                document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('activo'));
                btn.classList.add('activo');
                
                const categoria = btn.getAttribute('data-categoria');
                const cards = document.querySelectorAll('.producto-card');
                
                cards.forEach(card => {
                    const cardCat = card.getAttribute('data-categoria');
                    if (categoria === 'todos' || cardCat === categoria) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                // Scroll al inicio de productos
                document.querySelector('.productos-wrapper').scrollTo({ top: 0, behavior: 'smooth' });
            });
        });

        /* ===== AÑADIR AL CARRITO CON AJAX ===== */
        document.addEventListener('click', async (e) => {
            const btn = e.target.closest('.btn-add-cart');
            if (!btn) return;
            
            const id = btn.getAttribute('data-id');
            if (!id) return;
            
            try {
                const response = await fetch('../php/cart_add.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}`
                });
                
                const data = await response.json();
                
                if (data.ok) {
                    showToast('✅ Producto agregado al carrito');
                    updateCartBadge(data.count);
                } else {
                    showToast('❌ ' + (data.msg || 'Error al agregar'), true);
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('❌ Error de conexión', true);
            }
        });

        /* ===== ACTUALIZAR BADGE DEL CARRITO ===== */
        function updateCartBadge(count) {
            // Buscar badge en el header
            let badge = document.querySelector('.cart-badge');
            const cartLink = document.querySelector('a[href*="cart.php"]');
            
            if (count > 0) {
                if (!badge && cartLink) {
                    badge = document.createElement('span');
                    badge.className = 'cart-badge';
                    cartLink.appendChild(badge);
                }
                if (badge) badge.textContent = count;
            } else {
                if (badge) badge.remove();
            }
        }

        /* ===== NOTIFICACIONES TOAST ===== */
        function showToast(message, isError = false) {
            const toast = document.createElement('div');
            toast.textContent = message;
            toast.style.cssText = `
                position: fixed;
                top: 100px;
                right: 20px;
                background: ${isError ? 'linear-gradient(135deg, #dc3545, #c82333)' : 'linear-gradient(135deg, #28a745, #20c997)'};
                color: white;
                padding: 16px 24px;
                border-radius: 12px;
                z-index: 10000;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                font-weight: 600;
                animation: slideIn 0.3s ease-out;
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Animaciones CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(400px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(400px); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>