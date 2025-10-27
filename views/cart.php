<?php
session_start();
require_once("../config/db.php");
$cart = $_SESSION['cart'] ?? [];

// Calcular totales
$subtotal = 0;
$total_items = 0;
foreach($cart as $item) { 
    $subtotal += $item['precio'] * $item['cantidad'];
    $total_items += $item['cantidad'];
}
$impuesto = $subtotal * 0.07; // 7% ITBMS
$total = $subtotal + $impuesto;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .cart {
            padding: 50px 7%;
            min-height: 70vh;
        }
        
        .cart-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .cart-empty {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .cart-empty h3 {
            color: var(--cafe-medio);
            margin-bottom: 15px;
        }
        
        .table-container {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 14px rgba(0,0,0,.12);
            margin-bottom: 30px;
        }
        
        .tbl {
            width: 100%;
            border-collapse: collapse;
        }
        
        .tbl th {
            background: var(--cafe-oscuro);
            color: #fff;
            padding: 16px;
            text-align: left;
            font-weight: 600;
        }
        
        .tbl td {
            padding: 16px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        .tbl tr:hover {
            background: #f8f6f1;
        }
        
        .product-cell {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .product-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .qty-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .qty-btn {
            width: 35px;
            height: 35px;
            border: none;
            border-radius: 50%;
            background: var(--cafe-claro);
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .qty-input {
            width: 60px;
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        
        .remove-btn {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
        }
        
        .cart-summary {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0,0,0,.12);
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .summary-row.total {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--cafe-oscuro);
            border-bottom: none;
            margin-top: 15px;
        }
        
        .cart-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 25px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: #fff;
        }
        
        .btn-primary {
            background: var(--cafe-medio);
            color: #fff;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        @media (max-width: 768px) {
            .tbl {
                font-size: 0.85rem;
            }
            
            .product-cell {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .cart-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <main class="cart">
        <div class="cart-header">
            <h2>🛒 Tu Carrito de Compras</h2>
            <span class="cart-count"><?= $total_items ?> producto(s)</span>
        </div>

        <?php if(empty($cart)): ?>
            <div class="cart-empty">
                <h3>Tu carrito está vacío</h3>
                <p>¡Descubre nuestros deliciosos productos!</p>
                <a href="tienda.php" class="btn btn-primary" style="margin-top: 20px;">
                    Ir a la Tienda
                </a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cart as $id => $item): 
                            $item_total = $item['precio'] * $item['cantidad'];
                        ?>
                            <tr data-id="<?= $id ?>">
                                <td>
                                    <div class="product-cell">
                                        <img src="../assets/img/<?= htmlspecialchars($item['imagen']) ?>" 
                                             alt="<?= htmlspecialchars($item['nombre']) ?>" 
                                             class="product-img">
                                        <div>
                                            <strong><?= htmlspecialchars($item['nombre']) ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td>$<?= number_format($item['precio'], 2) ?></td>
                                <td>
                                    <div class="qty-controls">
                                        <button class="qty-btn minus" onclick="updateQuantity(<?= $id ?>, -1)">-</button>
                                        <input type="number" class="qty-input" value="<?= $item['cantidad'] ?>" 
                                               min="1" onchange="updateQuantity(<?= $id ?>, 0, this.value)">
                                        <button class="qty-btn plus" onclick="updateQuantity(<?= $id ?>, 1)">+</button>
                                    </div>
                                </td>
                                <td class="item-total">$<?= number_format($item_total, 2) ?></td>
                                <td>
                                    <button class="remove-btn" onclick="removeItem(<?= $id ?>)">
                                        🗑️ Eliminar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="subtotal">$<?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span>ITBMS (7%):</span>
                    <span id="tax">$<?= number_format($impuesto, 2) ?></span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span id="total">$<?= number_format($total, 2) ?></span>
                </div>
                
                <div class="cart-actions">
                    <a href="tienda.php" class="btn btn-secondary">
                        ← Seguir Comprando
                    </a>
                    <a href="checkout.php" class="btn btn-primary">
                        🛒 Proceder al Pago
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script>
        async function updateQuantity(productId, change, newValue = null) {
            let quantity = newValue !== null ? parseInt(newValue) : 
                          parseInt(document.querySelector(`tr[data-id="${productId}"] .qty-input`).value) + change;
            
            if (quantity < 1) quantity = 1;
            
            try {
                const response = await fetch('../php/cart_update.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${productId}&qty=${quantity}`
                });
                
                const data = await response.json();
                
                if (data.ok) {
                    location.reload(); // Recargar para ver cambios
                } else {
                    alert('Error al actualizar la cantidad');
                }
            } catch (error) {
                alert('Error de conexión');
            }
        }
        
        async function removeItem(productId) {
            if (confirm('¿Estás seguro de eliminar este producto del carrito?')) {
                await updateQuantity(productId, 0, 0);
            }
        }
    </script>

    <?php include("../includes/footer.php"); ?>
</body>
</html>