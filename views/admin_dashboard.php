<?php
session_start();
require_once("../config/db.php");
require_once("../includes/helpers.php");

// Verificar si el usuario es admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Obtener estadísticas para el dashboard
$stats = [
    'total_usuarios' => $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn(),
    'total_productos' => $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn(),
    'total_ventas' => $pdo->query("SELECT COUNT(*) FROM ventas")->fetchColumn(),
    'ingresos_totales' => $pdo->query("SELECT COALESCE(SUM(total), 0) FROM ventas")->fetchColumn(),
    'ventas_hoy' => $pdo->query("SELECT COUNT(*) FROM ventas WHERE DATE(fecha) = CURDATE()")->fetchColumn(),
    'ingresos_hoy' => $pdo->query("SELECT COALESCE(SUM(total), 0) FROM ventas WHERE DATE(fecha) = CURDATE()")->fetchColumn()
];

// Últimas ventas
$ultimas_ventas = $pdo->query("SELECT * FROM ventas ORDER BY fecha DESC LIMIT 5")->fetchAll();

// Productos con bajo stock
$productos_bajo_stock = $pdo->query("SELECT nombre, stock, stock_minimo FROM productos WHERE stock <= stock_minimo ORDER BY stock ASC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-dashboard {
            padding: 50px 6%;
            background: var(--crema);
            min-height: 100vh;
        }

        .dashboard-header {
            margin-bottom: 40px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
            border-left: 4px solid var(--cafe-medio);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--cafe-oscuro);
            margin: 10px 0;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .dashboard-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .card-title {
            color: var(--cafe-oscuro);
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--cafe-claro);
        }

        .venta-item, .producto-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .venta-item:last-child, .producto-item:last-child {
            border-bottom: none;
        }

        .alert-item {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        @media (max-width: 968px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <main class="admin-dashboard">
        <div class="dashboard-header">
            <h1>👑 Panel de Administración</h1>
            <p>Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?> • <?= date('d/m/Y') ?></p>
        </div>

        <!-- Estadísticas Principales -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-value">$<?= number_format($stats['ingresos_totales'], 2) ?></div>
                <div class="stat-label">Ingresos Totales</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-value"><?= $stats['total_productos'] ?></div>
                <div class="stat-label">Productos</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🛍️</div>
                <div class="stat-value"><?= $stats['total_ventas'] ?></div>
                <div class="stat-label">Ventas Totales</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value"><?= $stats['total_usuarios'] ?></div>
                <div class="stat-label">Usuarios Registrados</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-value"><?= $stats['ventas_hoy'] ?></div>
                <div class="stat-label">Ventas Hoy</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">💵</div>
                <div class="stat-value">$<?= number_format($stats['ingresos_hoy'], 2) ?></div>
                <div class="stat-label">Ingresos Hoy</div>
            </div>
        </div>

        <!-- Grid de Información -->
        <div class="dashboard-grid">
            <!-- Últimas Ventas -->
            <div class="dashboard-card">
                <h3 class="card-title">🛍️ Últimas Ventas</h3>
                <?php if ($ultimas_ventas): ?>
                    <?php foreach ($ultimas_ventas as $venta): ?>
                        <div class="venta-item">
                            <div>
                                <strong>Venta #<?= str_pad($venta['id'], 4, '0', STR_PAD_LEFT) ?></strong>
                                <div style="font-size: 0.9rem; color: #666;">
                                    <?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?>
                                </div>
                            </div>
                            <div style="font-weight: 700; color: var(--cafe-medio);">
                                $<?= number_format($venta['total'], 2) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 20px;">
                        No hay ventas registradas
                    </p>
                <?php endif; ?>
            </div>

            <!-- Alertas de Stock -->
            <div class="dashboard-card">
                <h3 class="card-title">⚠️ Productos con Bajo Stock</h3>
                <?php if ($productos_bajo_stock): ?>
                    <?php foreach ($productos_bajo_stock as $producto): ?>
                        <div class="alert-item">
                            <strong><?= htmlspecialchars($producto['nombre']) ?></strong>
                            <div>Stock: <?= $producto['stock'] ?> / Mínimo: <?= $producto['stock_minimo'] ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 20px;">
                        ✅ Todos los productos tienen stock suficiente
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="dashboard-card" style="margin-top: 30px;">
            <h3 class="card-title">⚡ Acciones Rápidas</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <a href="inventario.php" class="btn" style="background: var(--cafe-medio); color: #fff; padding: 15px; text-align: center; border-radius: 8px; text-decoration: none;">
                    📦 Gestionar Inventario
                </a>
                <a href="ventas.php" class="btn" style="background: var(--cafe-medio); color: #fff; padding: 15px; text-align: center; border-radius: 8px; text-decoration: none;">
                    💰 Ver Todas las Ventas
                </a>
                <a href="proveedores.php" class="btn" style="background: var(--cafe-medio); color: #fff; padding: 15px; text-align: center; border-radius: 8px; text-decoration: none;">
                    🏢 Gestionar Proveedores
                </a>
                <a href="gestion_usuarios.php" class="btn" style="background: var(--cafe-medio); color: #fff; padding: 15px; text-align: center; border-radius: 8px; text-decoration: none;">
                    👥 Gestionar Usuarios
                </a>
            </div>
        </div>
    </main>

    <?php include("../includes/footer.php"); ?>
</body>
</html>