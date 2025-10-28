<?php
// php/generar_factura.php
session_start();
require_once("../config/db.php");

$venta_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($venta_id <= 0) {
    die("ID de venta inválido");
}

// Obtener información de la venta
$stmtVenta = $pdo->prepare("SELECT * FROM ventas WHERE id = ?");
$stmtVenta->execute([$venta_id]);
$venta = $stmtVenta->fetch(PDO::FETCH_ASSOC);

if (!$venta) {
    die("Venta no encontrada");
}

// Obtener productos de la venta
$stmtProductos = $pdo->prepare("SELECT * FROM venta_detalle WHERE id_venta = ? ORDER BY id");
$stmtProductos->execute([$venta_id]);
$productos = $stmtProductos->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #<?= str_pad($venta_id, 4, '0', STR_PAD_LEFT) ?> | Chinos Café</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            padding: 40px;
            background: #f5f5f5;
        }
        
        .factura {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 60px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 3px solid #8b5e3c;
        }
        
        .logo-section h1 {
            color: #2b1e17;
            font-size: 2.5rem;
            margin-bottom: 5px;
        }
        
        .logo-section p {
            color: #8b5e3c;
            font-size: 0.95rem;
        }
        
        .factura-info {
            text-align: right;
        }
        
        .factura-numero {
            font-size: 1.8rem;
            color: #2b1e17;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .factura-fecha {
            color: #666;
            font-size: 0.9rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .info-section h3 {
            color: #2b1e17;
            margin-bottom: 15px;
            font-size: 1.1rem;
            border-bottom: 2px solid #d2a679;
            padding-bottom: 8px;
        }
        
        .info-section p {
            color: #555;
            line-height: 1.8;
            margin: 5px 0;
        }
        
        .info-section strong {
            color: #2b1e17;
        }
        
        .tabla-productos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .tabla-productos thead {
            background: #2b1e17;
            color: #fff;
        }
        
        .tabla-productos th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .tabla-productos td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .tabla-productos tbody tr:hover {
            background: #f8f6f1;
        }
        
        .tabla-productos .text-right {
            text-align: right;
        }
        
        .tabla-productos .text-center {
            text-align: center;
        }
        
        .totales {
            margin-left: auto;
            width: 300px;
            margin-top: 30px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
            font-size: 1rem;
        }
        
        .total-row.final {
            border-top: 3px solid #8b5e3c;
            border-bottom: 3px solid #8b5e3c;
            margin-top: 15px;
            padding: 20px 0;
            font-size: 1.4rem;
            font-weight: bold;
            color: #2b1e17;
        }
        
        .footer {
            margin-top: 60px;
            padding-top: 30px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            color: #666;
        }
        
        .footer p {
            margin: 8px 0;
            font-size: 0.9rem;
        }
        
        .gracias {
            margin-top: 40px;
            text-align: center;
            color: #8b5e3c;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .btn-imprimir {
            background: #8b5e3c;
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            margin: 20px auto;
            display: block;
        }
        
        .btn-imprimir:hover {
            background: #2b1e17;
        }
        
        @media print {
            body {
                padding: 0;
                background: #fff;
            }
            
            .factura {
                box-shadow: none;
                padding: 20px;
            }
            
            .btn-imprimir,
            .no-print {
                display: none !important;
            }
        }
        
        .badge-metodo {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
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
    </style>
</head>
<body>
    <button class="btn-imprimir no-print" onclick="window.print()">🖨️ Imprimir Factura</button>
    
    <div class="factura">
        <!-- HEADER -->
        <div class="header">
            <div class="logo-section">
                <h1>☕ Chinos Café</h1>
                <p>Café Premium de Panamá</p>
            </div>
            <div class="factura-info">
                <div class="factura-numero">
                    FACTURA #<?= str_pad($venta_id, 4, '0', STR_PAD_LEFT) ?>
                </div>
                <div class="factura-fecha">
                    Fecha: <?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?>
                </div>
            </div>
        </div>
        
        <!-- INFORMACIÓN -->
        <div class="info-grid">
            <div class="info-section">
                <h3>🏢 Información de la Empresa</h3>
                <p><strong>Chinos Café S.A.</strong></p>
                <p>RUC: 123456-78-123456</p>
                <p>Calle 53 Este, Obarrio</p>
                <p>Ciudad de Panamá, Panamá</p>
                <p>Tel: +507 264-5000</p>
                <p>Email: info@chinoscafe.com</p>
            </div>
            
            <div class="info-section">
                <h3>👤 Datos del Cliente</h3>
                <?php if (!empty($venta['cliente_nombre'])): ?>
                    <p><strong><?= htmlspecialchars($venta['cliente_nombre']) ?></strong></p>
                    <p><?= htmlspecialchars($venta['cliente_correo']) ?></p>
                    <?php if (!empty($venta['cliente_telefono'])): ?>
                        <p>Tel: <?= htmlspecialchars($venta['cliente_telefono']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($venta['direccion_entrega'])): ?>
                        <p>Dirección: <?= htmlspecialchars($venta['direccion_entrega']) ?></p>
                    <?php endif; ?>
                <?php else: ?>
                    <p><strong>Cliente General</strong></p>
                    <p>Venta en mostrador</p>
                <?php endif; ?>
                <p style="margin-top: 15px;">
                    <strong>Método de Pago:</strong><br>
                    <span class="badge-metodo badge-<?= strtolower($venta['metodo_pago']) ?>">
                        <?= htmlspecialchars($venta['metodo_pago']) ?>
                    </span>
                </p>
            </div>
        </div>
        
        <!-- TABLA DE PRODUCTOS -->
        <table class="tabla-productos">
            <thead>
                <tr>
                    <th>Cantidad</th>
                    <th>Descripción</th>
                    <th class="text-right">Precio Unit.</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $prod): ?>
                    <tr>
                        <td class="text-center"><strong><?= $prod['cantidad'] ?></strong></td>
                        <td><?= htmlspecialchars($prod['nombre_producto']) ?></td>
                        <td class="text-right">$<?= number_format($prod['precio_unitario'], 2) ?></td>
                        <td class="text-right"><strong>$<?= number_format($prod['subtotal'], 2) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- TOTALES -->
        <div class="totales">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>$<?= number_format($venta['subtotal'], 2) ?></span>
            </div>
            <div class="total-row">
                <span>ITBMS (7%):</span>
                <span>$<?= number_format($venta['impuesto'], 2) ?></span>
            </div>
            <div class="total-row final">
                <span>TOTAL A PAGAR:</span>
                <span>$<?= number_format($venta['total'], 2) ?></span>
            </div>
        </div>
        
        <?php if (!empty($venta['notas'])): ?>
            <div class="info-section" style="margin-top: 30px;">
                <h3>📝 Notas</h3>
                <p><?= nl2br(htmlspecialchars($venta['notas'])) ?></p>
            </div>
        <?php endif; ?>
        
        <div class="gracias">
            ¡Gracias por su compra!
        </div>
        
        <!-- FOOTER -->
        <div class="footer">
            <p><strong>Chinos Café - Café Premium desde 1995</strong></p>
            <p>Síguenos en redes sociales: @ChinosCafe</p>
            <p>www.chinoscafe.com | info@chinoscafe.com</p>
            <p style="margin-top: 15px; font-size: 0.8rem;">
                Esta factura es válida como comprobante de compra. Conserve para cualquier reclamo.
            </p>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 30px;">
        <a href="../views/checkout_success.php" style="display: inline-block; background: #6c757d; color: #fff; padding: 12px 30px; border-radius: 5px; text-decoration: none; font-weight: 600;">
            ← Volver
        </a>
    </div>
</body>
</html>