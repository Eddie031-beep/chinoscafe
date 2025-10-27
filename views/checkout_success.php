<?php
session_start();
if (!isset($_SESSION['checkout_success'])) {
    header("Location: cart.php");
    exit;
}

$venta_id = $_SESSION['checkout_success'];
unset($_SESSION['checkout_success']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Pedido Confirmado! | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .success-page {
            padding: 80px 6%;
            text-align: center;
            background: var(--crema);
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .success-card {
            background: #fff;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
        }
        
        .success-icon {
            font-size: 5rem;
            margin-bottom: 20px;
        }
        
        .success-title {
            color: var(--cafe-oscuro);
            margin-bottom: 15px;
        }
        
        .order-number {
            background: var(--cafe-claro);
            color: #fff;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 700;
            display: inline-block;
            margin: 20px 0;
        }
        
        .next-steps {
            text-align: left;
            background: #f8f6f1;
            padding: 20px;
            border-radius: 10px;
            margin: 25px 0;
        }
        
        .next-steps h4 {
            color: var(--cafe-medio);
            margin-bottom: 15px;
        }
        
        .next-steps ul {
            list-style: none;
            padding: 0;
        }
        
        .next-steps li {
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
        }
        
        .btn-primary {
            background: var(--cafe-medio);
            color: #fff;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: #fff;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <main class="success-page">
        <div class="success-card">
            <div class="success-icon">🎉</div>
            <h1 class="success-title">¡Pedido Confirmado!</h1>
            <p>Gracias por tu compra en Chinos Café. Tu pedido ha sido procesado exitosamente.</p>
            
            <div class="order-number">
                Nº de Pedido: #<?= str_pad($venta_id, 4, '0', STR_PAD_LEFT) ?>
            </div>
            
            <div class="next-steps">
                <h4>📋 Próximos pasos:</h4>
                <ul>
                    <li>✅ <strong>Recibirás un correo de confirmación</strong> con los detalles de tu pedido</li>
                    <li>⏰ <strong>Prepararemos tu pedido</strong> en los próximos 15-20 minutos</li>
                    <li>🚗 <strong>Coordinaremos la entrega</strong> según tu método de pago seleccionado</li>
                    <li>📞 <strong>Te contactaremos</strong> si necesitamos más información</li>
                </ul>
            </div>
            
            <p style="color: #666; margin-bottom: 25px;">
                ¿Tienes alguna pregunta? <a href="contacto.php" style="color: var(--cafe-medio);">Contáctanos</a>
            </p>
            
            <div class="action-buttons">
                <a href="tienda.php" class="btn btn-primary">
                    🛍️ Seguir Comprando
                </a>
                <a href="../views/index.php" class="btn btn-secondary">
                    🏠 Ir al Inicio
                </a>
            </div>
        </div>
    </main>

    <?php include("../includes/footer.php"); ?>
</body>
</html>