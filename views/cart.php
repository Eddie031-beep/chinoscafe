<?php
session_start();
require_once("../config/db.php");
require_once("../includes/helpers.php");

$cart = $_SESSION['cart'] ?? [];

// Calcular totales usando la función helper
$totales = calcularTotalesCarrito($cart);
$subtotal = $totales['subtotal'];
$impuesto = $totales['impuesto'];
$total = $totales['total'];
$total_items = $totales['total_items'];
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
            background: var(--crema);
        }
        
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .cart-header h2 {
            color: var(--cafe-oscuro);
            margin: 0;
            font-size: 2.5rem;
        }
        
        .cart-count-badge {
            background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro));
            color: #fff;
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 5px 15px rgba(210, 166, 121, 0.3);
        }
        
        .cart-empty {
            text-align: center;
            padding: 100px 20px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }
        
        .cart-empty-icon {
            font-size: 6rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .cart-empty h3 {
            color: var(--cafe-oscuro);
            margin-bottom: 15px;
            font-size: 2rem;
        }
        
        .cart-empty p {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        
        .table-container {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,.08);
            margin-bottom: 30px;
        }
        
        .tbl {
            width: 100%;
            border-collapse: collapse;
        }
        
        .tbl th {
            background: var(--cafe-oscuro);
            color: #fff;
            padding: 18px;
            text-align: left;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .tbl td {
            padding: 20px 18px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        
        .tbl tr:hover {
            background: #f8f6f1;
        }
        
        .tbl tr:last-child td {
            border-bottom: none;
        }
        
        .product-cell {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .product-info h4 {
            margin: 0 0 5px 0;
            color: var(--cafe-oscuro);
            font-size: 1.1rem;
        }
        
        .product-info p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .qty-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f8f6f1;
            padding: 8px;
            border-radius: 30px;
            width: fit-content;
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
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .qty-btn:hover {
            background: var(--cafe-medio);
            transform: scale(1.1);
        }
        
        .qty-input {
            width: 60px;
            padding: 8px;
            text-align: center;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .qty-input:focus {
            outline: none;
            border-color: var(--cafe-medio);
        }
        
        .remove-btn {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        
        .remove-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220, 53, 69, 0.4);
        }
        
        .cart-summary {
            background: #fff;
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,.08);
        }
        
        .summary-title {
            color: var(--cafe-oscuro);
            margin: 0 0 25px 0;
            font-size: 1.8rem;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--cafe-claro);
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 1.1rem;
        }
        
        .summary-row:last-of-type {
            border-bottom: none;
        }
        
        .summary-row.total {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--cafe-oscuro);
            border-top: 2px solid var(--cafe-claro);
            padding-top: 20px;
            margin-top: 20px;
        }
        
        .cart-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }
        
        .btn {
            padding: 16px 32px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: #fff;
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro));
            color: #fff;
            box-shadow: 0 5px 15px rgba(210, 166, 121, 0.4);
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        
        @media (max-width: 768px) {
            .cart-header h2 {
                font-size: 2rem;
            }
            
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
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <main class="cart">
        <div class="cart-header">
            <h2>🛒 Tu Carrito</h2>
            <span class="cart-count-badge">
                <?= $total_items ?> producto<?= $total_items != 1 ? 's' : '' ?>
            </span>
        </div>

        <?php if(empty($cart)): ?>
            <div class="cart-empty">
                <div class="cart-empty-icon">🛒</div>
                <h3>Tu carrito está vacío</h3>
                <p>¡Descubre nuestros deliciosos productos y añade algunos a tu carrito!</p>
                <a href="tienda.php" class="btn btn-primary" style="margin-top: 20px;">
                    🛍️ Ir a la Tienda
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
                                        <div class="product-info">
                                            <h4><?= htmlspecialchars($item['nombre']) ?></h4>
                                            <p>Producto de calidad premium</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong style="color: var(--cafe-medio); font-size: 1.2rem;">
                                        $<?= number_format($item['precio'], 2) ?>
                                    </strong>
                                </td>
                                <td>
                                    <div class="qty-controls">
                                        <button class="qty-btn minus" onclick="updateQuantity(<?= $id ?>, -1)">−</button>
                                        <input type="number" class="qty-input" value="<?= $item['cantidad'] ?>" 
                                               min="1" onchange="updateQuantity(<?= $id ?>, 0, this.value)">
                                        <button class="qty-btn plus" onclick="updateQuantity(<?= $id ?>, 1)">+</button>
                                    </div>
                                </td>
                                <td class="item-total">
                                    <strong style="color: var(--cafe-oscuro); font-size: 1.3rem;">
                                        $<?= number_format($item_total, 2) ?>
                                    </strong>
                                </td>
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
                <h3 class="summary-title">📋 Resumen del Pedido</h3>
                
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="subtotal"><strong>$<?= number_format($subtotal, 2) ?></strong></span>
                </div>
                <div class="summary-row">
                    <span>ITBMS (7%):</span>
                    <span id="tax"><strong>$<?= number_format($impuesto, 2) ?></strong></span>
                </div>
                <div class="summary-row total">
                    <span>Total a Pagar:</span>
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

    <?php include("../includes/footer.php"); ?>

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
                    // Recargar página para actualizar totales
                    location.reload();
                } else {
                    alert('Error al actualizar la cantidad');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error de conexión');
            }
        }
        
        async function removeItem(productId) {
            if (confirm('¿Estás seguro de eliminar este producto del carrito?')) {
                try {
                    const response = await fetch('../php/cart_update.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${productId}&qty=0`
                    });
                    
                    const data = await response.json();
                    
                    if (data.ok) {
                        // Recargar página
                        location.reload();
                    } else {
                        alert('Error al eliminar el producto');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error de conexión');
                }
            }
        }
    </script>
</body>
</html>