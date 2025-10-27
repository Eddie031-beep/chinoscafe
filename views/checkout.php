<?php
session_start();
require_once("../config/db.php");

// Verificar carrito
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("Location: cart.php");
    exit;
}

// Calcular totales
$subtotal = 0;
$total_items = 0;
foreach($cart as $item) {
    $subtotal += $item['precio'] * $item['cantidad'];
    $total_items += $item['cantidad'];
}
$impuesto = $subtotal * 0.07;
$total = $subtotal + $impuesto;

// Procesar checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $metodo_pago = $_POST['metodo_pago'] ?? 'Efectivo';
    $notas = trim($_POST['notas'] ?? '');
    
    // Validaciones básicas
    if (empty($nombre) || empty($correo)) {
        $error = "Nombre y correo son obligatorios";
    } else {
        try {
            $pdo->beginTransaction();
            
            // 1. Insertar venta
            $sqlVenta = "INSERT INTO ventas (subtotal, impuesto, total, metodo_pago, cliente_nombre, cliente_correo, cliente_telefono, direccion_entrega, notas, estado) 
                        VALUES (:subtotal, :impuesto, :total, :metodo_pago, :nombre, :correo, :telefono, :direccion, :notas, 'Pendiente')";
            $stmtVenta = $pdo->prepare($sqlVenta);
            $stmtVenta->execute([
                ':subtotal' => $subtotal,
                ':impuesto' => $impuesto,
                ':total' => $total,
                ':metodo_pago' => $metodo_pago,
                ':nombre' => $nombre,
                ':correo' => $correo,
                ':telefono' => $telefono,
                ':direccion' => $direccion,
                ':notas' => $notas
            ]);
            
            $venta_id = $pdo->lastInsertId();
            
            // 2. Insertar detalles y actualizar stock
            foreach($cart as $id => $item) {
                // Insertar detalle
                $sqlDetalle = "INSERT INTO venta_detalle (id_venta, id_producto, nombre_producto, cantidad, precio_unitario, subtotal) 
                              VALUES (:venta_id, :producto_id, :nombre, :cantidad, :precio, :subtotal)";
                $stmtDetalle = $pdo->prepare($sqlDetalle);
                $stmtDetalle->execute([
                    ':venta_id' => $venta_id,
                    ':producto_id' => $id,
                    ':nombre' => $item['nombre'],
                    ':cantidad' => $item['cantidad'],
                    ':precio' => $item['precio'],
                    ':subtotal' => $item['precio'] * $item['cantidad']
                ]);
                
                // Actualizar stock
                $sqlStock = "UPDATE productos SET stock = stock - :cantidad WHERE id = :id";
                $stmtStock = $pdo->prepare($sqlStock);
                $stmtStock->execute([
                    ':cantidad' => $item['cantidad'],
                    ':id' => $id
                ]);
            }
            
            $pdo->commit();
            
            // Limpiar carrito y redirigir
            unset($_SESSION['cart']);
            $_SESSION['checkout_success'] = $venta_id;
            header("Location: checkout_success.php");
            exit;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error al procesar el pedido: " . $e->getMessage();
        }
    }
}
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
            min-height: 80vh;
        }
        
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .checkout-section {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.1);
        }
        
        .section-title {
            color: var(--cafe-oscuro);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--cafe-claro);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--texto);
            font-weight: 600;
        }
        
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--cafe-medio);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        
        .payment-method {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .payment-method.selected {
            border-color: var(--cafe-medio);
            background: #f8f6f1;
        }
        
        .payment-icon {
            font-size: 1.5rem;
            margin-bottom: 8px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-item-info {
            flex: 1;
        }
        
        .order-item-name {
            font-weight: 600;
            color: var(--texto);
        }
        
        .order-item-qty {
            color: #666;
            font-size: 0.9rem;
        }
        
        .order-summary {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid var(--cafe-claro);
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .summary-row.total {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--cafe-oscuro);
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #eee;
        }
        
        .btn-checkout {
            width: 100%;
            background: var(--cafe-medio);
            color: #fff;
            padding: 16px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s;
        }
        
        .btn-checkout:hover {
            background: var(--cafe-oscuro);
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
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
        <h2 style="text-align: center; margin-bottom: 40px; color: var(--cafe-oscuro);">
            🛒 Finalizar Compra
        </h2>
        
        <?php if (isset($error)): ?>
            <div class="alert-error" style="max-width: 1200px; margin: 0 auto 30px;">
                ⚠️ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="checkout-grid">
            <!-- Información del Cliente -->
            <div class="checkout-section">
                <h3 class="section-title">👤 Información Personal</h3>
                
                <div class="form-group">
                    <label class="form-label">Nombre Completo *</label>
                    <input type="text" name="nombre" class="form-input" 
                           value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" 
                           placeholder="Ej: Juan Pérez" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Correo Electrónico *</label>
                    <input type="email" name="correo" class="form-input" 
                           value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" 
                           placeholder="correo@ejemplo.com" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" name="telefono" class="form-input" 
                           value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>" 
                           placeholder="6000-0000">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Dirección de Entrega</label>
                    <textarea name="direccion" class="form-textarea" 
                              placeholder="Dirección completa para la entrega"><?= htmlspecialchars($_POST['direccion'] ?? '') ?></textarea>
                </div>
                
                <h3 class="section-title">💳 Método de Pago</h3>
                
                <div class="payment-methods">
                    <label class="payment-method <?= ($_POST['metodo_pago'] ?? 'Efectivo') === 'Efectivo' ? 'selected' : '' ?>">
                        <input type="radio" name="metodo_pago" value="Efectivo" 
                               <?= ($_POST['metodo_pago'] ?? 'Efectivo') === 'Efectivo' ? 'checked' : '' ?> hidden>
                        <div class="payment-icon">💵</div>
                        <div>Efectivo</div>
                    </label>
                    
                    <label class="payment-method <?= ($_POST['metodo_pago'] ?? '') === 'Tarjeta' ? 'selected' : '' ?>">
                        <input type="radio" name="metodo_pago" value="Tarjeta" 
                               <?= ($_POST['metodo_pago'] ?? '') === 'Tarjeta' ? 'checked' : '' ?> hidden>
                        <div class="payment-icon">💳</div>
                        <div>Tarjeta</div>
                    </label>
                    
                    <label class="payment-method <?= ($_POST['metodo_pago'] ?? '') === 'Yappy' ? 'selected' : '' ?>">
                        <input type="radio" name="metodo_pago" value="Yappy" 
                               <?= ($_POST['metodo_pago'] ?? '') === 'Yappy' ? 'checked' : '' ?> hidden>
                        <div class="payment-icon">📱</div>
                        <div>Yappy</div>
                    </label>
                    
                    <label class="payment-method <?= ($_POST['metodo_pago'] ?? '') === 'Transferencia' ? 'selected' : '' ?>">
                        <input type="radio" name="metodo_pago" value="Transferencia" 
                               <?= ($_POST['metodo_pago'] ?? '') === 'Transferencia' ? 'checked' : '' ?> hidden>
                        <div class="payment-icon">🏦</div>
                        <div>Transferencia</div>
                    </label>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Notas Adicionales</label>
                    <textarea name="notas" class="form-textarea" 
                              placeholder="Instrucciones especiales para tu pedido..."><?= htmlspecialchars($_POST['notas'] ?? '') ?></textarea>
                </div>
            </div>
            
            <!-- Resumen del Pedido -->
            <div class="checkout-section">
                <h3 class="section-title">📋 Resumen del Pedido</h3>
                
                <?php foreach($cart as $item): ?>
                    <div class="order-item">
                        <div class="order-item-info">
                            <div class="order-item-name"><?= htmlspecialchars($item['nombre']) ?></div>
                            <div class="order-item-qty">Cantidad: <?= $item['cantidad'] ?></div>
                        </div>
                        <div style="font-weight: 600; color: var(--cafe-medio);">
                            $<?= number_format($item['precio'] * $item['cantidad'], 2) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="order-summary">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>$<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>ITBMS (7%):</span>
                        <span>$<?= number_format($impuesto, 2) ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>$<?= number_format($total, 2) ?></span>
                    </div>
                </div>
                
                <button type="submit" class="btn-checkout">
                    ✅ Confirmar Pedido
                </button>
                
                <p style="text-align: center; margin-top: 15px; color: #666; font-size: 0.9rem;">
                    Al confirmar, aceptas nuestros términos y condiciones
                </p>
            </div>
        </form>
    </main>

    <script>
        // Selección de método de pago
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', () => {
                document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
                method.classList.add('selected');
                method.querySelector('input[type="radio"]').checked = true;
            });
        });
    </script>

    <?php include("../includes/footer.php"); ?>
</body>
</html>