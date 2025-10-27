<?php 
session_start();
require_once("../config/db.php");
global $pdo;


// Solo administradores pueden acceder
if (!esAdmin()) {
    header("Location: login.php");
    exit;
}

// Filtros
$fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
$fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d');
$metodo_pago = $_GET['metodo_pago'] ?? 'todos';

// Construir query con filtros
$sql = "SELECT v.*, 
        (SELECT COUNT(*) FROM venta_detalle WHERE id_venta = v.id) as items,
        (SELECT SUM(cantidad) FROM venta_detalle WHERE id_venta = v.id) as unidades
        FROM ventas v 
        WHERE DATE(v.fecha) BETWEEN :fecha_desde AND :fecha_hasta ";

if ($metodo_pago !== 'todos') {
    $sql .= "AND v.metodo_pago = :metodo_pago ";
}

$sql .= "ORDER BY v.fecha DESC";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':fecha_desde', $fecha_desde);
$stmt->bindParam(':fecha_hasta', $fecha_hasta);
if ($metodo_pago !== 'todos') {
    $stmt->bindParam(':metodo_pago', $metodo_pago);
}
$stmt->execute();
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas del período
$statsQuery = $pdo->prepare("
    SELECT 
        COUNT(*) as total_ventas,
        COALESCE(SUM(total), 0) as total_ingresos,
        COALESCE(AVG(total), 0) as promedio_venta,
        COALESCE(SUM(CASE WHEN metodo_pago = 'Efectivo' THEN total ELSE 0 END), 0) as efectivo,
        COALESCE(SUM(CASE WHEN metodo_pago = 'Tarjeta' THEN total ELSE 0 END), 0) as tarjeta,
        COALESCE(SUM(CASE WHEN metodo_pago = 'Yappy' THEN total ELSE 0 END), 0) as yappy
    FROM ventas 
    WHERE DATE(fecha) BETWEEN :fecha_desde AND :fecha_hasta
");
$statsQuery->execute([':fecha_desde' => $fecha_desde, ':fecha_hasta' => $fecha_hasta]);
$stats = $statsQuery->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .ventas {
            padding: 50px 6%;
            background: var(--crema);
            min-height: calc(100vh - 90px);
        }
        
        .ventas-header {
            margin-bottom: 30px;
        }
        
        .ventas-header h2 {
            color: var(--cafe-oscuro);
            margin: 0 0 20px 0;
        }
        
        .filtros-container {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .filtros-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }
        
        .form-group {
            margin-bottom: 0;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--texto);
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: var(--cafe-medio);
            color: #fff;
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }
        
        .btn-primary:hover {
            background: var(--cafe-oscuro);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card.destacado {
            background: linear-gradient(135deg, var(--cafe-medio) 0%, var(--cafe-oscuro) 100%);
            color: #fff;
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 2rem;
        }
        
        .stat-card p {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .stat-card.destacado h3,
        .stat-card.destacado p {
            color: #fff;
        }
        
        .stat-card:not(.destacado) h3 {
            color: var(--cafe-medio);
        }
        
        .stat-card:not(.destacado) p {
            color: #666;
        }
        
        .tabla-ventas {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 14px rgba(0,0,0,0.12);
        }
        
        .tabla {
            width: 100%;
            border-collapse: collapse;
        }
        
        .tabla thead {
            background: var(--cafe-oscuro);
            color: #fff;
        }
        
        .tabla th,
        .tabla td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .tabla tbody tr:hover {
            background: #f8f6f1;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .badge-efectivo {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-tarjeta {
            background: #cce5ff;
            color: #004085;
        }
        
        .badge-yappy {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-transferencia {
            background: #e2e3e5;
            color: #383d41;
        }
        
        .btn-sm {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-block;
            transition: opacity 0.2s;
        }
        
        .btn-sm:hover {
            opacity: 0.8;
        }
        
        .btn-view {
            background: #17a2b8;
            color: #fff;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        
        .modal.show {
            display: flex;
        }
        
        .modal-content {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            max-width: 700px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--cafe-claro);
        }
        
        .modal-header h3 {
            margin: 0;
            color: var(--cafe-oscuro);
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
        }
        
        .detalle-venta {
            margin-bottom: 20px;
        }
        
        .detalle-header {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .detalle-item {
            padding: 10px;
            background: #f8f6f1;
            border-radius: 8px;
        }
        
        .detalle-item label {
            display: block;
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 4px;
        }
        
        .detalle-item strong {
            color: var(--cafe-oscuro);
            font-size: 1.1rem;
        }
        
        .productos-detalle {
            margin: 20px 0;
        }
        
        .productos-detalle h4 {
            margin: 0 0 15px 0;
            color: var(--cafe-medio);
        }
        
        .producto-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .producto-item:last-child {
            border-bottom: none;
        }
        
        .producto-info {
            flex: 1;
        }
        
        .producto-nombre {
            font-weight: 600;
            color: var(--texto);
        }
        
        .producto-cantidad {
            font-size: 0.9rem;
            color: #666;
        }
        
        .producto-precio {
            text-align: right;
            font-weight: 600;
            color: var(--cafe-medio);
        }
        
        .total-venta {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid var(--cafe-claro);
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
        }
        
        .total-row.final {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--cafe-oscuro);
            margin-top: 15px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        @media (max-width: 768px) {
            .filtros-form {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .tabla {
                font-size: 0.85rem;
            }
            
            .tabla th,
            .tabla td {
                padding: 10px 8px;
            }
            
            .detalle-header {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <main class="ventas">
        <div class="ventas-header">
            <h2>💰 Registro de Ventas</h2>
        </div>

        <!-- Filtros -->
        <div class="filtros-container">
            <form method="GET" class="filtros-form">
                <div class="form-group">
                    <label>Desde</label>
                    <input type="date" name="fecha_desde" value="<?= htmlspecialchars($fecha_desde) ?>">
                </div>
                
                <div class="form-group">
                    <label>Hasta</label>
                    <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($fecha_hasta) ?>">
                </div>
                
                <div class="form-group">
                    <label>Método de Pago</label>
                    <select name="metodo_pago">
                        <option value="todos" <?= $metodo_pago === 'todos' ? 'selected' : '' ?>>Todos</option>
                        <option value="Efectivo" <?= $metodo_pago === 'Efectivo' ? 'selected' : '' ?>>Efectivo</option>
                        <option value="Tarjeta" <?= $metodo_pago === 'Tarjeta' ? 'selected' : '' ?>>Tarjeta</option>
                        <option value="Yappy" <?= $metodo_pago === 'Yappy' ? 'selected' : '' ?>>Yappy</option>
                        <option value="Transferencia" <?= $metodo_pago === 'Transferencia' ? 'selected' : '' ?>>Transferencia</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-primary">🔍 Filtrar</button>
                </div>
            </form>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card destacado">
                <h3>$<?= number_format($stats['total_ingresos'], 2) ?></h3>
                <p>💵 Total Ingresos</p>
            </div>
            
            <div class="stat-card">
                <h3><?= $stats['total_ventas'] ?></h3>
                <p>🛍️ Total Ventas</p>
            </div>
            
            <div class="stat-card">
                <h3>$<?= number_format($stats['promedio_venta'], 2) ?></h3>
                <p>📊 Promedio por Venta</p>
            </div>
            
            <div class="stat-card">
                <h3>$<?= number_format($stats['efectivo'], 2) ?></h3>
                <p>💵 Efectivo</p>
            </div>
            
            <div class="stat-card">
                <h3>$<?= number_format($stats['tarjeta'], 2) ?></h3>
                <p>💳 Tarjeta</p>
            </div>
            
            <div class="stat-card">
                <h3>$<?= number_format($stats['yappy'], 2) ?></h3>
                <p>📱 Yappy</p>
            </div>
        </div>

        <!-- Tabla de Ventas -->
        <?php if (count($ventas) > 0): ?>
            <div class="tabla-ventas">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Método Pago</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ventas as $venta): ?>
                            <tr>
                                <td><strong>#<?= str_pad($venta['id'], 4, '0', STR_PAD_LEFT) ?></strong></td>
                                <td><?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></td>
                                <td>
                                    <?php if ($venta['cliente_nombre']): ?>
                                        <?= htmlspecialchars($venta['cliente_nombre']) ?><br>
                                        <small style="color: #666;"><?= htmlspecialchars($venta['cliente_correo']) ?></small>
                                    <?php else: ?>
                                        <em style="color: #999;">Cliente general</em>
                                    <?php endif; ?>
                                </td>
                                <td><?= $venta['items'] ?> productos (<?= $venta['unidades'] ?> unidades)</td>
                                <td><strong style="color: var(--cafe-medio);">$<?= number_format($venta['total'], 2) ?></strong></td>
                                <td>
                                    <span class="badge badge-<?= strtolower($venta['metodo_pago']) ?>">
                                        <?= htmlspecialchars($venta['metodo_pago']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                        $estadoClass = 'efectivo';
                                        if ($venta['estado'] === 'Cancelada') $estadoClass = 'critico';
                                        if ($venta['estado'] === 'Pendiente') $estadoClass = 'yappy';
                                    ?>
                                    <span class="badge badge-<?= $estadoClass ?>">
                                        <?= htmlspecialchars($venta['estado']) ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-sm btn-view" onclick="verDetalle(<?= $venta['id'] ?>)">👁️ Ver</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <h3>No hay ventas para mostrar</h3>
                <p>Ajusta los filtros o realiza tu primera venta</p>
            </div>
        <?php endif; ?>
    </main>

    <!-- Modal Detalle de Venta -->
    <div id="modalDetalle" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>📋 Detalle de Venta <span id="ventaNumero"></span></h3>
                <button class="close-modal" onclick="cerrarModal('modalDetalle')">&times;</button>
            </div>
            <div id="contenidoDetalle">
                <div style="text-align: center; padding: 40px; color: #999;">
                    Cargando...
                </div>
            </div>
        </div>
    </div>

    <?php include("../includes/footer.php"); ?>

    <script>
        async function verDetalle(id) {
            document.getElementById('modalDetalle').classList.add('show');
            document.getElementById('ventaNumero').textContent = '#' + String(id).padStart(4, '0');
            
            try {
                const response = await fetch('../php/venta_detalle.php?id=' + id);
                const data = await response.json();
                
                if (data.ok) {
                    mostrarDetalle(data.venta, data.productos);
                } else {
                    document.getElementById('contenidoDetalle').innerHTML = 
                        '<div style="text-align: center; padding: 40px; color: #dc3545;">Error al cargar el detalle</div>';
                }
            } catch (error) {
                document.getElementById('contenidoDetalle').innerHTML = 
                    '<div style="text-align: center; padding: 40px; color: #dc3545;">Error de conexión</div>';
            }
        }
        
        function mostrarDetalle(venta, productos) {
            let html = `
                <div class="detalle-venta">
                    <div class="detalle-header">
                        <div class="detalle-item">
                            <label>Fecha</label>
                            <strong>${formatearFecha(venta.fecha)}</strong>
                        </div>
                        <div class="detalle-item">
                            <label>Método de Pago</label>
                            <strong>${venta.metodo_pago}</strong>
                        </div>
                        <div class="detalle-item">
                            <label>Cliente</label>
                            <strong>${venta.cliente_nombre || 'Cliente general'}</strong>
                        </div>
                        <div class="detalle-item">
                            <label>Estado</label>
                            <strong>${venta.estado}</strong>
                        </div>
                    </div>
                    
                    <div class="productos-detalle">
                        <h4>Productos</h4>
            `;
            
            productos.forEach(prod => {
                html += `
                    <div class="producto-item">
                        <div class="producto-info">
                            <div class="producto-nombre">${prod.nombre_producto}</div>
                            <div class="producto-cantidad">${prod.cantidad} x ${parseFloat(prod.precio_unitario).toFixed(2)}</div>
                        </div>
                        <div class="producto-precio">${parseFloat(prod.subtotal).toFixed(2)}</div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                    
                    <div class="total-venta">
                        <div class="total-row">
                            <span>Subtotal:</span>
                            <span>${parseFloat(venta.subtotal).toFixed(2)}</span>
                        </div>
                        <div class="total-row">
                            <span>Impuesto:</span>
                            <span>${parseFloat(venta.impuesto).toFixed(2)}</span>
                        </div>
                        <div class="total-row final">
                            <span>TOTAL:</span>
                            <span>${parseFloat(venta.total).toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('contenidoDetalle').innerHTML = html;
        }
        
        function formatearFecha(fecha) {
            const d = new Date(fecha);
            const opciones = { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric', 
                hour: '2-digit', 
                minute: '2-digit' 
            };
            return d.toLocaleDateString('es-PA', opciones);
        }
        
        function cerrarModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        // Cerrar modal al hacer clic fuera
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>