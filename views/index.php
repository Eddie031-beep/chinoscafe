<?php 
session_start();
require_once("../config/db.php");
global $pdo;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chinos Café | El Mejor Café Artesanal de Panamá</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* HERO MODERNO CON PARALLAX */
        .hero-modern {
            position: relative;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: linear-gradient(135deg, #2b1e17 0%, #3b2f2f 100%);
        }
        
        .hero-background {
            position: absolute;
            inset: 0;
            background: url('../assets/img/hero-cafe.jpg') center/cover no-repeat fixed;
            opacity: 0.4;
            animation: zoomIn 30s ease-in-out infinite alternate;
        }
        
        @keyframes zoomIn {
            from { transform: scale(1); }
            to { transform: scale(1.1); }
        }
        
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(43, 30, 23, 0.9) 0%,
                rgba(59, 47, 47, 0.7) 100%
            );
        }
        
        .hero-content-modern {
            position: relative;
            z-index: 2;
            text-align: center;
            color: #fff;
            max-width: 900px;
            padding: 0 20px;
            animation: fadeInUp 1s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .hero-badge {
            display: inline-block;
            background: rgba(210, 166, 121, 0.2);
            backdrop-filter: blur(10px);
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 20px;
            border: 1px solid rgba(210, 166, 121, 0.3);
            animation: fadeInUp 1s ease-out 0.2s both;
        }
        
        .hero-title {
            font-size: clamp(2.5rem, 8vw, 5rem);
            font-weight: 700;
            margin: 0 0 20px 0;
            line-height: 1.2;
            background: linear-gradient(135deg, #fff 0%, #d2a679 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: fadeInUp 1s ease-out 0.4s both;
        }
        
        .hero-subtitle {
            font-size: clamp(1.1rem, 2.5vw, 1.5rem);
            color: #f0e7dc;
            margin: 0 0 40px 0;
            opacity: 0.9;
            animation: fadeInUp 1s ease-out 0.6s both;
        }
        
        .hero-cta {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease-out 0.8s both;
        }
        
        .btn-hero-primary {
            background: linear-gradient(135deg, #d2a679, #8b5e3c);
            color: #fff;
            padding: 16px 40px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(210, 166, 121, 0.3);
        }
        
        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(210, 166, 121, 0.5);
        }
        
        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: #fff;
            padding: 16px 40px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-3px);
        }
        
        .scroll-indicator {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 3;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translate(-50%, 0); }
            50% { transform: translate(-50%, 10px); }
        }
        
        .scroll-indicator svg {
            width: 30px;
            height: 30px;
            color: #d2a679;
        }
        
        /* ESTADÍSTICAS FLOTANTES */
        .stats-section {
            background: var(--cafe-oscuro);
            color: #fff;
            padding: 60px 6%;
            margin-top: -50px;
            position: relative;
            z-index: 2;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--cafe-claro);
            margin-bottom: 10px;
            display: block;
        }
        
        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        /* SECCIÓN DE CARACTERÍSTICAS */
        .features-section {
            padding: 100px 6%;
            background: linear-gradient(180deg, var(--crema) 0%, #fff 100%);
        }
        
        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 60px;
        }
        
        .section-badge {
            display: inline-block;
            background: rgba(210, 166, 121, 0.1);
            color: var(--cafe-medio);
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .section-title {
            font-size: clamp(2rem, 5vw, 3rem);
            color: var(--cafe-oscuro);
            margin: 0 0 20px 0;
        }
        
        .section-description {
            font-size: 1.1rem;
            color: #666;
            line-height: 1.8;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .feature-card {
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
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
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .feature-card:hover::before {
            transform: scaleX(1);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(210, 166, 121, 0.3);
        }
        
        .feature-title {
            font-size: 1.5rem;
            color: var(--cafe-oscuro);
            margin: 0 0 15px 0;
            font-weight: 600;
        }
        
        .feature-description {
            color: #666;
            line-height: 1.8;
        }
        
        /* PRODUCTOS DESTACADOS */
        .productos-section {
            padding: 100px 6%;
            background: #fff;
        }
        
        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
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
            right: 15px;
            background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
            color: #fff;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
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
        }
        
        .producto-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        
        /* TESTIMONIOS */
        .testimonios-section {
            padding: 100px 6%;
            background: linear-gradient(135deg, var(--cafe-oscuro) 0%, var(--cafe-medio) 100%);
            color: #fff;
        }
        
        .testimonios-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 60px auto 0;
        }
        
        .testimonio-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 35px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .testimonio-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-5px);
        }
        
        .testimonio-stars {
            color: #ffd700;
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
        
        .testimonio-texto {
            font-size: 1rem;
            line-height: 1.8;
            margin-bottom: 25px;
            opacity: 0.95;
        }
        
        .testimonio-autor {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .testimonio-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--cafe-claro);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .testimonio-info h4 {
            margin: 0 0 5px 0;
            font-size: 1rem;
        }
        
        .testimonio-info p {
            margin: 0;
            opacity: 0.8;
            font-size: 0.9rem;
        }
        
        /* CTA FINAL */
        .cta-section {
            padding: 100px 6%;
            background: var(--crema);
            text-align: center;
        }
        
        .cta-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .cta-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            color: var(--cafe-oscuro);
            margin: 0 0 20px 0;
        }
        
        .cta-description {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 40px;
            line-height: 1.8;
        }
        
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            .hero-cta {
                flex-direction: column;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 30px;
            }
            
            .features-grid,
            .productos-grid,
            .testimonios-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <!-- HERO MODERNO -->
    <section class="hero-modern">
        <div class="hero-background"></div>
        <div class="hero-overlay"></div>
        <div class="hero-content-modern">
            <span class="hero-badge">✨ Café Premium desde 1995</span>
            <h1 class="hero-title">El Arte del Café Perfecto</h1>
            <p class="hero-subtitle">Descubre sabores únicos en cada taza. Granos seleccionados, tostados con pasión y servidos con excelencia.</p>
            <div class="hero-cta">
                <a href="tienda.php" class="btn-hero-primary">🛍️ Explorar Menú</a>
                <a href="historia.php" class="btn-hero-secondary">📖 Nuestra Historia</a>
            </div>
        </div>
        <div class="scroll-indicator">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </section>

    <!-- ESTADÍSTICAS -->
    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number">29+</span>
                <span class="stat-label">Años de Experiencia</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">50K+</span>
                <span class="stat-label">Clientes Satisfechos</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">15+</span>
                <span class="stat-label">Variedades de Café</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">100%</span>
                <span class="stat-label">Calidad Garantizada</span>
            </div>
        </div>
    </section>

    <!-- CARACTERÍSTICAS -->
    <section class="features-section">
        <div class="section-header">
            <span class="section-badge">💎 Nuestra Diferencia</span>
            <h2 class="section-title">¿Por Qué Elegirnos?</h2>
            <p class="section-description">Nos apasiona cada detalle para brindarte la mejor experiencia de café en Panamá</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">☕</div>
                <h3 class="feature-title">Granos Premium</h3>
                <p class="feature-description">Seleccionamos cuidadosamente los mejores granos de café de las fincas más prestigiosas de la región.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🔥</div>
                <h3 class="feature-title">Tostado Artesanal</h3>
                <p class="feature-description">Cada lote es tostado artesanalmente para resaltar los perfiles de sabor únicos de nuestros cafés.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">👨‍🍳</div>
                <h3 class="feature-title">Baristas Expertos</h3>
                <p class="feature-description">Nuestro equipo cuenta con certificaciones internacionales y años de experiencia en el arte del café.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🌱</div>
                <h3 class="feature-title">Sostenibilidad</h3>
                <p class="feature-description">Trabajamos directamente con productores locales, promoviendo prácticas agrícolas sostenibles.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🚚</div>
                <h3 class="feature-title">Entrega Rápida</h3>
                <p class="feature-description">Llevamos tu café favorito directamente a tu puerta con servicio de entrega en toda la ciudad.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">💯</div>
                <h3 class="feature-title">Garantía de Calidad</h3>
                <p class="feature-description">Si no estás 100% satisfecho, te devolvemos tu dinero. Esa es nuestra promesa.</p>
            </div>
        </div>
    </section>

    <!-- PRODUCTOS DESTACADOS -->
    <section class="productos-section">
        <div class="section-header">
            <span class="section-badge">⭐ Lo Más Popular</span>
            <h2 class="section-title">Nuestros Productos Estrella</h2>
            <p class="section-description">Descubre los favoritos de nuestros clientes</p>
        </div>
        
        <div class="productos-grid">
<?php
try {
    $query = $pdo->query("SELECT * FROM productos WHERE activo = 1 ORDER BY RAND() LIMIT 6");
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $id = (int)$row['id'];
        $nombre = htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8');
        $desc = htmlspecialchars($row['descripcion'], ENT_QUOTES, 'UTF-8');
        $precio = htmlspecialchars($row['precio'], ENT_QUOTES, 'UTF-8');
        $categoria = htmlspecialchars($row['categoria'] ?? 'Bebida', ENT_QUOTES, 'UTF-8');
        $imagen = htmlspecialchars($row['imagen']);
        
        // 🔧 RUTA CORREGIDA
        $img_src = "/chinoscafe/img/" . $imagen;
        
        echo "
        <div class='producto-card'>
            <div class='producto-image-wrapper'>
                <img src='$img_src' alt='$nombre' class='producto-image' onerror=\"this.src='/chinoscafe/img/default.jpg'\">
                <span class='producto-badge'>Nuevo</span>
            </div>
            <div class='producto-info'>
                <div class='producto-categoria'>$categoria</div>
                <h3 class='producto-nombre'>$nombre</h3>
                <p class='producto-descripcion'>$desc</p>
                <div class='producto-footer'>
                    <span class='producto-precio'>\$$precio</span>
                    <button class='btn-add-cart' onclick='addToCart($id)'>🛒 Agregar</button>
                </div>
            </div>
        </div>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red; text-align:center;'>Error al cargar productos</p>";
}
?>
        </div>
        
        <div style="text-align: center; margin-top: 50px;">
            <a href="tienda.php" class="btn-hero-primary">Ver Todos los Productos →</a>
        </div>
    </section>

    <!-- TESTIMONIOS -->
    <section class="testimonios-section">
        <div class="section-header">
            <span class="section-badge" style="background: rgba(255,255,255,0.2); color: #fff;">💬 Testimonios</span>
            <h2 class="section-title" style="color: #fff;">Lo Que Dicen Nuestros Clientes</h2>
            <p class="section-description" style="color: rgba(255,255,255,0.9);">Miles de clientes satisfechos respaldan nuestra calidad</p>
        </div>
        
        <div class="testimonios-grid">
            <div class="testimonio-card">
                <div class="testimonio-stars">⭐⭐⭐⭐⭐</div>
                <p class="testimonio-texto">"Sin duda el mejor café de Panamá. El latte es espectacular y el servicio siempre impecable. ¡Totalmente recomendado!"</p>
                <div class="testimonio-autor">
                    <div class="testimonio-avatar">MC</div>
                    <div class="testimonio-info">
                        <h4>María Carla</h4>
                        <p>Cliente desde 2020</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonio-card">
                <div class="testimonio-stars">⭐⭐⭐⭐⭐</div>
                <p class="testimonio-texto">"Los cold brew son increíbles. Perfecto para el calor de Panamá. La entrega siempre puntual y el café fresco."</p>
                <div class="testimonio-autor">
                    <div class="testimonio-avatar">JR</div>
                    <div class="testimonio-info">
                        <h4>Javier Rodríguez</h4>
                        <p>Cliente frecuente</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonio-card">
                <div class="testimonio-stars">⭐⭐⭐⭐⭐</div>
                <p class="testimonio-texto">"Ambiente acogedor y café de primera calidad. Los postres también son deliciosos. Mi lugar favorito en la ciudad."</p>
                <div class="testimonio-autor">
                    <div class="testimonio-avatar">AS</div>
                    <div class="testimonio-info">
                        <h4>Ana Sofía</h4>
                        <p>Cliente VIP</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA FINAL -->
    <section class="cta-section">
        <div class="cta-content">
            <h2 class="cta-title">¿Listo Para Probar el Mejor Café?</h2>
            <p class="cta-description">Visítanos en cualquiera de nuestras sucursales o haz tu pedido en línea. La experiencia perfecta del café te espera.</p>
            <div class="cta-buttons">
                <a href="tienda.php" class="btn-hero-primary">🛍️ Hacer Pedido</a>
                <a href="sucursales.php" class="btn-hero-secondary" style="color: var(--cafe-oscuro); border-color: var(--cafe-medio);">📍 Ver Sucursales</a>
            </div>
        </div>
    </section>

    <?php include("../includes/footer.php"); ?>

    <script>
        async function addToCart(id) {
            try {
                const response = await fetch('../php/cart_add.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}`
                });
                
                const data = await response.json();
                
                if (data.ok) {
                    // Actualizar badge del carrito
                    if (window.updateCartBadge) {
                        window.updateCartBadge(data.count);
                    }
                    
                    // Notificación visual
                    showNotification('✅ Producto agregado al carrito');
                } else {
                    showNotification('❌ Error al agregar producto', 'error');
                }
            } catch (error) {
                showNotification('❌ Error de conexión', 'error');
            }
        }
        
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 100px;
                right: 20px;
                background: ${type === 'success' ? 'linear-gradient(135deg, #28a745, #20c997)' : 'linear-gradient(135deg, #dc3545, #c82333)'};
                color: white;
                padding: 16px 24px;
                border-radius: 12px;
                z-index: 10000;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                animation: slideIn 0.3s ease-out;
                font-weight: 600;
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
        
        // Animaciones
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