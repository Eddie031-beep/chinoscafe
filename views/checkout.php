<?php
session_start();
require_once("../config/db.php");

// Verificar que hay productos en el carrito
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("Location: cart.php");
    exit;
}

// Calcular totales
$subtotal = 0;
foreach($cart as $item) {
    $subtotal += $item['precio'] * $item['cantidad'];
}
$impuesto = $subtotal * 0.07; // 7% ITBMS en Panamá
$total = $subtotal + $impuesto;

// Procesar el pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $metodo_pago = $_POST['metodo_pago'] ?? 'Efectivo';
    
    try {
        // Iniciar transacción
        $pdo->beginTransaction();
        
        // Insertar venta
        $sqlVenta = "INSERT INTO ventas (subtotal, impuesto, total, metodo_pago, cliente_nombre, cliente_correo, estado) 
                     VALUES (:subtotal, :impuesto, :total, :metodo_pago, :nombre, :correo, 'Completada')";
        $stmtVenta = $pdo->prepare($sqlVenta);
        $stmtVenta->execute([
            ':subtotal' => $subtotal,
            ':impuesto' => $impuesto,
            ':total' => $total,
            ':metodo_pago' => $metodo_pago,
            ':nombre' => $nombre,
            ':correo' => $correo
        ]);
        
        $ventaId = $pdo->lastInsertId();
        
        // Insertar detalles de la venta y actualizar stock
        $sqlDetalle = "INSERT INTO venta_detalle (id_venta, id_producto, nombre_producto, cantidad, precio_unitario, subtotal) 
                       VALUES (:venta_id, :producto_id, :nombre, :cantidad, :precio, :subtotal)";
        $stmtDetalle = $pdo->prepare($sqlDetalle);
        
        $sqlStock = "UPDATE productos SET stock = stock - :cantidad WHERE id = :id AND stock >= :cantidad";
        $stmtStock = $pdo->prepare($sqlStock);
        
        foreach($cart as $item) {
            // Insertar detalle
            $stmtDetalle->execute([
                ':venta_id' => $ventaId,
                ':producto_id' => $item['id'],
                ':nombre' => $item['nombre'],
                ':cantidad' => $item['cantidad'],
                ':precio' => $item['precio'],
                ':subtotal' => $item['precio'] * $item['cantidad']
            ]);
            
            // Actualizar stock
            $stmtStock->execute([
                ':cantidad' => $item['cantidad'],
                ':id' => $item['id']
            ]);
        }
        
        // Confirmar transacción
        $pdo->commit();
        
        // Limpiar carrito
        unset($_SESSION['cart']);
        
        // Redirigir a página de éxito
        header("Location: checkout.php?success=1&venta_id=$ventaId");
        exit;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error al procesar el pedido: " . $e->getMessage();
    }
}

// Si es una compra exitosa
$success = isset($_GET['success']) && $_GET['success'] == 1;
$ventaId = $_GET['venta_id'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .checkout {
            padding: 50px 6%;
            background: var(--crema);
            min-height: calc(100vh - 90px);
        }
        
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }
        
        .checkout-section {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .checkout-section h2 {
            margin: 0 0 20px 0;
            color: var(--cafe-oscuro);
            font-size: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--texto);
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--cafe-medio);
        }
        
        .resumen-producto {
            display: flex;
            gap: 15px;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .resumen-producto:last-child {
            border-bottom: none;
        }
        
        .resumen-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .resumen-info {
            flex: 1;
        }
        
        .resumen-nombre {
            font-weight: 600;
            color: var(--texto);
            margin-bottom: 5px;
        }
        
        .resumen-cantidad {
            font-size: 0.9rem;
            color: #666;
        }
        
        .resumen-precio {
            font-weight: 700;
            color: var(--cafe-medio);
        }
        
        .totales {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }
        
        .total-row.final {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--cafe-oscuro);
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid var(--cafe-claro);
        }
        
        .btn-submit {
            width: 100%;
            background: var(--cafe-medio);
            color: #fff;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s;
        }
        
        .btn-submit:hover {
            background: var(--cafe-oscuro);
        }
        
        .success-message {
            text-align: center;
            padding: 60px 20px;
        }
        
        .success-icon {
            font-size: 5rem;
            margin-bottom: 20px;
        }
        
        .success-message h2 {
            color: var(--cafe-oscuro);
            margin-bottom: 15px;
        }
        
        .success-message p {
            color: #666;
            margin-bottom: 30px;
        }
        
        .btn-link {
            display: inline-block;
            padding: 12px 24px;
            background: var(--cafe-medio);
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            margin: 0 10px;
            transition: background 0.3s;
        }
        
        .btn-link:hover {
            background: var(--cafe-oscuro);
        }
        
        .alert-error {
            padding: 15px;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 968px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <main class="checkout">
        <div class="checkout-container">
            <?php if ($success): ?>
                <!-- Mensaje de Éxito -->
                <div class="checkout-section">
                    <div class="success-message">
                        <div class="success-icon">✅</div>
                        <h2>¡Pedido Realizado con Éxito!</h2>
                        <p>Tu pedido #<?= str_pad($ventaId, 4, '0', STR_PAD_LEFT) ?> ha sido procesado correctamente.</p>
                        <p>Recibirás un correo de confirmación en breve.</p>
                        <div>
                            <a href="tienda.php" class="btn-link">🛍️ Seguir Comprando</a>
                            <a href="../views/index.php" class="btn-link">🏠 Ir al Inicio</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Formulario de Checkout -->
                <h2 style="margin-bottom: 30px; color: var(--cafe-oscuro);">🛒 Finalizar Pedido</h2>
                
                <?php if (isset($error)): ?>
                    <div class="alert-error">
                        ⚠️ <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="checkout-grid">
                    <!-- Datos del Cliente -->
                    <div class="checkout-section">
                        <h2>👤 Información del Cliente</h2>
                        
                        <div class="form-group">
                            <label>Nombre Completo *</label>
                            <input type="text" name="nombre" required placeholder="Ej: Juan Pérez">
                        </div>
                        
                        <div class="form-group">
                            <label>Correo Electrónico *</label>
                            <input type="email" name="correo" required placeholder="correo@ejemplo.com">
                        </div>
                        
                        <div class="form-group">
                            <label>Método de Pago *</label>
                            <select name="metodo_pago" required>
                                <option value="Efectivo">💵 Efectivo</option>
                                <option value="Tarjeta">💳 Tarjeta</option>
                                <option value="Yappy">📱 Yappy</option>
                                <option value="Transferencia">🏦 Transferencia</option>
                            </select>
                        </div>
                        
                        <p style="color: #666; font-size: 0.9rem; margin-top: 20px;">
                            <strong>Nota:</strong> El pago se realizará al recibir tu pedido. Te contactaremos para coordinar la entrega.
                        </p>
                    </div>
                    
                    <!-- Resumen del Pedido -->
                    <div>
                        <div class="checkout-section">
                            <h2>📋 Resumen del Pedido</h2>
                            
                            <?php foreach($cart as $item): ?>
                                <div class="resumen-producto">
                                    <img src="../assets/img/<?= htmlspecialchars($item['imagen']) ?>" 
                                         alt="<?= htmlspecialchars($item['nombre']) ?>" 
                                         class="resumen-img">
                                    <div class="resumen-info">
                                        <div class="resumen-nombre"><?= htmlspecialchars($item['nombre']) ?></div>
                                        <div class="resumen-cantidad">
                                            Cantidad: <?= $item['cantidad'] ?> x $<?= number_format($item['precio'], 2) ?>
                                        </div>
                                    </div>
                                    <div class="resumen-precio">
                                        $<?= number_format($item['precio'] * $item['cantidad'], 2) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="totales">
                                <div class="total-row">
                                    <span>Subtotal:</span>
                                    <span>$<?= number_format($subtotal, 2) ?></span>
                                </div>
                                <div class="total-row">
                                    <span>Impuesto (7% ITBMS):</span>
                                    <span>$<?= number_format($impuesto, 2) ?></span>
                                </div>
                                <div class="total-row final">
                                    <span>TOTAL:</span>
                                    <span>$<?= number_format($total, 2) ?></span>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn-submit">
                                💳 Confirmar Pedido
                            </button>
                            
                            <a href="cart.php" style="display: block; text-align: center; margin-top: 15px; color: var(--cafe-medio); text-decoration: none;">
                                ← Volver al Carrito
                            </a>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <?php include("../includes/footer.php"); ?>
</body>
</html>