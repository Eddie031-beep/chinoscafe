<?php 
session_start();
require_once("../config/db.php");
global $pdo;

// Estadísticas generales
$statsQuery = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM productos WHERE activo = 1) as total_productos,
        (SELECT SUM(stock) FROM productos WHERE activo = 1) as total_stock,
        (SELECT COUNT(*) FROM productos WHERE stock <= stock_minimo) as productos_bajo_stock,
        (SELECT COUNT(*) FROM proveedores WHERE activo = 1) as total_proveedores,
        (SELECT COUNT(*) FROM ventas WHERE DATE(fecha) = CURDATE()) as ventas_hoy,
        (SELECT COALESCE(SUM(total), 0) FROM ventas WHERE DATE(fecha) = CURDATE()) as ingresos_hoy,
        (SELECT COUNT(*) FROM ventas WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())) as ventas_mes,
        (SELECT COALESCE(SUM(total), 0) FROM ventas WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())) as ingresos_mes
");
$stats = $statsQuery->fetch(PDO::FETCH_ASSOC);

// Productos más vendidos (este mes)
$topProductosQuery = $pdo->query("
    SELECT 
        vd.nombre_producto,
        SUM(vd.cantidad) as total_vendido,
        SUM(vd.subtotal) as ingresos_generados
    FROM venta_detalle vd
    JOIN ventas v ON vd.id_venta = v.id
    WHERE MONTH(v.fecha) = MONTH(CURDATE()) AND YEAR(v.fecha) = YEAR(CURDATE())
    GROUP BY vd.nombre_producto
    ORDER BY total_vendido DESC
    LIMIT 5
");
$topProductos = $topProductosQuery->fetchAll(PDO::FETCH_ASSOC);

// Productos con bajo stock
$bajoStockQuery = $pdo->query("
    SELECT nombre, stock, stock_minimo, imagen
    FROM productos 
    WHERE stock <= stock_minimo 
    ORDER BY stock ASC 
    LIMIT 5
");
$productosBajoStock = $bajoStockQuery->fetchAll(PDO::FETCH_ASSOC);

// Últimas ventas
$ultimasVentasQuery = $pdo->query("
    SELECT 
        v.*,
        (SELECT COUNT(*) FROM venta_detalle WHERE id_venta = v.id) as items
    FROM ventas v
    ORDER BY v.fecha DESC
    LIMIT 10
");
$ultimasVentas = $ultimasVentasQuery->fetchAll(PDO::FETCH_ASSOC);

// Ventas por método de pago (este mes)
$metodosPagoQuery = $pdo->query("
    SELECT 
        metodo_pago,
        COUNT(*) as cantidad,
        SUM(total) as total
    FROM ventas
    WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())
    GROUP BY metodo_pago
    ORDER BY total DESC
");
$metodosPago = $metodosPagoQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dashboard {
            padding: 50px 6%;
            background: var(--crema);
            min-height: calc(100vh - 90px);
        }
        
        .dashboard-header {
            margin-bottom: 30px;
        }
        
        .dashboard-header h2 {
            color: var(--cafe-oscuro);
            margin: 0 0 10px 0;
        }
        
        .dashboard-header p {
            color: #666;
            margin: 0;
        }
        
        .stats-main {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--cafe-medio);
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--cafe-oscuro);
            margin: 10px 0;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-change {
            font-size: 0.85rem;
            color: #28a745;
            margin-top: 8px;
        }
        
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .card-section {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .card-section h3 {
            margin: 0 0 20px 0;
            color: var(--cafe-oscuro);
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .producto-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #eee;
            transition: background 0.2s;
        }
        
        .producto-top:hover {
            background: #f8f6f1;
        }
        
        .producto-top:last-child {
            border-bottom: none;
        }
        
        .producto-top-info {
            flex: 1;
        }
        
        .producto-top-nombre {
            font-weight: 600;
            color: var(--texto);
            margin-bottom: 4px;
        }
        
        .producto-top-cantidad {
            font-size: 0.85rem;
            color: #666;
        }
        
        .producto-top-valor {
            text-align: right;
            font-weight: 700;
            color: var(--cafe-medio);
        }
        
        .alert-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        
        .alert-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .alert-info {
            flex: 1;
        }
        
        .alert-nombre {
            font-weight: 600;
            color: var(--texto);
            margin-bottom: 4px;
        }
        
        .alert-stock {
            font-size: 0.85rem;
            color: #856404;
        }
        
        .venta-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .venta-item:last-child {
            border-bottom: none;
        }
        
        .venta-info {
            flex: 1;
        }
        
        .venta-id {
            font-weight: 700;
            color: var(--cafe-oscuro);
        }
        
        .venta-fecha {
            font-size: 0.85rem;
            color: #666;
        }
        
        .venta-total {
            font-weight: 700;
            color: var(--cafe-medio);
            font-size: 1.1rem;
        }
        
        .metodo-pago-bar {
            margin-bottom: 20px;
        }
        
        .metodo-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        .metodo-nombre {
            font-weight: 600;
            color: var(--texto);
        }
        
        .metodo-valor {
            color: var(--cafe-medio);
            font-weight: 700;
        }
        
        .progress-bar {
            height: 10px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--cafe-medio), var(--cafe-claro));
            transition: width 0.6s ease;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }
        
        .btn-link {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 16px;
            background: var(--cafe-medio);
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: background 0.3s;
        }
        
        .btn-link:hover {
            background: var(--cafe-oscuro);
        }
        
        @media (max-width: 1024px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .stats-main {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .stat-value {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <main class="dashboard">
        <div class="dashboard-header">
            <h2>📊 Dashboard - Panel de Control</h2>
            <p>Vista general de tu negocio • <?= date('d/m/Y') ?></p>
        </div>

        <!-- Estadísticas Principales -->
        <div class="stats-main">
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-value">$<?= number_format($stats['ingresos_hoy'], 2) ?></div>
                <div class="stat-label">Ingresos Hoy</div>
                <div class="stat-change">📈 <?= $stats['ventas_hoy'] ?> ventas</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-value">$<?= number_format($stats['ingresos_mes'], 2) ?></div>
                <div class="stat-label">Ingresos del Mes</div>
                <div class="stat-change">📈 <?= $stats['ventas_mes'] ?> ventas</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-value"><?= $stats['total_productos'] ?></div>
                <div class="stat-label">Productos Activos</div>
                <div class="stat-change">📊 <?= $stats['total_stock'] ?> unidades</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">⚠️</div>
                <div class="stat-value"><?= $stats['productos_bajo_stock'] ?></div>
                <div class="stat-label">Productos Bajo Stock</div>
                <?php if ($stats['productos_bajo_stock'] > 0): ?>
                    <div class="stat-change" style="color: #dc3545;">🔴 Requieren atención</div>
                <?php else: ?>
                    <div class="stat-change">✅ Todo normal</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Grid de 2 columnas -->
        <div class="grid-2">
            <!-- Productos Más Vendidos -->
            <div class="card-section">
                <h3>🔥 Top 5 Productos del Mes</h3>
                <?php if (count($topProductos) > 0): ?>
                    <?php foreach ($topProductos as $prod): ?>
                        <div class="producto-top">
                            <div class="producto-top-info">
                                <div class="producto-top-nombre"><?= htmlspecialchars($prod['nombre_producto']) ?></div>
                                <div class="producto-top-cantidad"><?= $prod['total_vendido'] ?> unidades vendidas</div>
                            </div>
                            <div class="producto-top-valor">
                                $<?= number_format($prod['ingresos_generados'], 2) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No hay datos de ventas este mes</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Productos con Bajo Stock -->
            <div class="card-section">
                <h3>⚠️ Alertas de Inventario</h3>
                <?php if (count($productosBajoStock) > 0): ?>
                    <?php foreach ($productosBajoStock as $prod): ?>
                        <div class="alert-item">
                            <img src="../assets/img/<?= htmlspecialchars($prod['imagen']) ?>" alt="<?= htmlspecialchars($prod['nombre']) ?>">
                            <div class="alert-info">
                                <div class="alert-nombre"><?= htmlspecialchars($prod['nombre']) ?></div>
                                <div class="alert-stock">
                                    Stock actual: <strong><?= $prod['stock'] ?></strong> / Mínimo: <?= $prod['stock_minimo'] ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <a href="inventario.php" class="btn-link">Ver Inventario Completo →</a>
                <?php else: ?>
                    <div class="empty-state">
                        <p>✅ Todos los productos tienen stock suficiente</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Grid de 2 columnas -->
        <div class="grid-2">
            <!-- Últimas Ventas -->
            <div class="card-section">
                <h3>🛍️ Últimas 10 Ventas</h3>
                <?php if (count($ultimasVentas) > 0): ?>
                    <?php foreach ($ultimasVentas as $venta): ?>
                        <div class="venta-item">
                            <div class="venta-info">
                                <div class="venta-id">
                                    Venta #<?= str_pad($venta['id'], 4, '0', STR_PAD_LEFT) ?>
                                    <span style="font-size: 0.85rem; color: #666; font-weight: normal;">
                                        (<?= $venta['items'] ?> items)
                                    </span>
                                </div>
                                <div class="venta-fecha">
                                    <?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?> • 
                                    <?= htmlspecialchars($venta['metodo_pago']) ?>
                                </div>
                            </div>
                            <div class="venta-total">
                                $<?= number_format($venta['total'], 2) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <a href="ventas.php" class="btn-link">Ver Todas las Ventas →</a>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No hay ventas registradas aún</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Ventas por Método de Pago -->
            <div class="card-section">
                <h3>💳 Métodos de Pago (Este Mes)</h3>
                <?php if (count($metodosPago) > 0): ?>
                    <?php 
                        $totalGeneral = array_sum(array_column($metodosPago, 'total'));
                        foreach ($metodosPago as $metodo): 
                            $porcentaje = $totalGeneral > 0 ? ($metodo['total'] / $totalGeneral) * 100 : 0;
                    ?>
                        <div class="metodo-pago-bar">
                            <div class="metodo-label">
                                <span class="metodo-nombre">
                                    <?= htmlspecialchars($metodo['metodo_pago']) ?> 
                                    (<?= $metodo['cantidad'] ?> ventas)
                                </span>
                                <span class="metodo-valor">$<?= number_format($metodo['total'], 2) ?></span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= $porcentaje ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #eee;">
                        <div class="metodo-label">
                            <span class="metodo-nombre" style="font-size: 1.1rem;">TOTAL</span>
                            <span class="metodo-valor" style="font-size: 1.3rem;">$<?= number_format($totalGeneral, 2) ?></span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No hay ventas este mes</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sección de Acciones Rápidas -->
        <div class="card-section" style="margin-top: 20px;">
            <h3>⚡ Acciones Rápidas</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px;">
                <a href="inventario.php" class="btn-link" style="text-align: center; padding: 20px;">
                    📦 Gestionar Inventario
                </a>
                <a href="ventas.php" class="btn-link" style="text-align: center; padding: 20px;">
                    💰 Ver Todas las Ventas
                </a>
                <a href="proveedores.php" class="btn-link" style="text-align: center; padding: 20px;">
                    🏢 Gestionar Proveedores
                </a>
                <a href="tienda.php" class="btn-link" style="text-align: center; padding: 20px;">
                    🛍️ Ir a la Tienda
                </a>
            </div>
        </div>
    </main>

    <?php include("../includes/footer.php"); ?>

    <script>
        // Animación de las barras de progreso al cargar
        window.addEventListener('load', () => {
            document.querySelectorAll('.progress-fill').forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            });
        });
    </script>
</body>
</html>