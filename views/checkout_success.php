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
            padding: 60px 50px;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.12);
            max-width: 700px;
            width: 100%;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .success-icon {
            font-size: 6rem;
            margin-bottom: 25px;
            animation: bounce 1s ease-in-out;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        .success-title {
            color: var(--cafe-oscuro);
            margin-bottom: 15px;
            font-size: 2.5rem;
        }
        
        .success-subtitle {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        
        .order-number {
            background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro));
            color: #fff;
            padding: 15px 30px;
            border-radius: 30px;
            font-weight: 700;
            display: inline-block;
            margin: 20px 0 30px 0;
            font-size: 1.3rem;
            box-shadow: 0 8px 20px rgba(210, 166, 121, 0.3);
        }
        
        .next-steps {
            text-align: left;
            background: #f8f6f1;
            padding: 30px;
            border-radius: 15px;
            margin: 30px 0;
            border-left: 5px solid var(--cafe-medio);
        }
        
        .next-steps h4 {
            color: var(--cafe-medio);
            margin-bottom: 20px;
            font-size: 1.3rem;
        }
        
        .next-steps ul {
            list-style: none;
            padding: 0;
        }
        
        .next-steps li {
            padding: 12px 0;
            display: flex;
            align-items: flex-start;
            gap: 15px;
            color: #555;
            line-height: 1.6;
        }
        
        .next-steps li::before {
            content: '✓';
            background: var(--cafe-medio);
            color: #fff;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-weight: bold;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 35px;
        }
        
        .btn {
            padding: 16px 24px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro));
            color: #fff;
            box-shadow: 0 5px 15px rgba(210, 166, 121, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: #fff;
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }
        
        .btn-success {
            background: #28a745;
            color: #fff;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        
        .contact-info {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #e0e0e0;
            color: #666;
        }
        
        .contact-info a {
            color: var(--cafe-medio);
            text-decoration: none;
            font-weight: 600;
        }
        
        .contact-info a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .success-card {
                padding: 40px 30px;
            }
            
            .success-title {
                font-size: 2rem;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <main class="success-page">
        <div class="success-card">
            <div class="success-icon">🎉</div>
            <h1 class="success-title">¡Pedido Confirmado!</h1>
            <p class="success-subtitle">
                Gracias por tu compra en Chinos Café.<br>
                Tu pedido ha sido procesado exitosamente.
            </p>
            
            <div class="order-number">
                📋 Pedido Nº <?= str_pad($venta_id, 4, '0', STR_PAD_LEFT) ?>
            </div>
            
            <div class="next-steps">
                <h4>📋 Próximos pasos:</h4>
                <ul>
                    <li>
                        <div>
                            <strong>Confirmación por email</strong><br>
                            Recibirás un correo con los detalles de tu pedido
                        </div>
                    </li>
                    <li>
                        <div>
                            <strong>Preparación del pedido</strong><br>
                            Prepararemos tu pedido en los próximos 15-20 minutos
                        </div>
                    </li>
                    <li>
                        <div>
                            <strong>Coordinación de entrega</strong><br>
                            Te contactaremos para coordinar la entrega según tu método de pago
                        </div>
                    </li>
                    <li>
                        <div>
                            <strong>Disfruta tu café</strong><br>
                            Relájate y prepárate para disfrutar de nuestros productos premium
                        </div>
                    </li>
                </ul>
            </div>
            
            <div class="action-buttons">
                <a href="../php/generar_factura.php?id=<?= $venta_id ?>" class="btn btn-success" target="_blank">
                    🧾 Ver/Imprimir Factura
                </a>
                <a href="tienda.php" class="btn btn-primary">
                    🛍️ Seguir Comprando
                </a>
                <a href="index.php" class="btn btn-secondary">
                    🏠 Ir al Inicio
                </a>
            </div>
            
            <div class="contact-info">
                <p>¿Tienes alguna pregunta sobre tu pedido?</p>
                <p>
                    Contáctanos: 
                    <a href="tel:+5076000000">+507 6000-0000</a> | 
                    <a href="mailto:info@chinoscafe.com">info@chinoscafe.com</a>
                </p>
            </div>
        </div>
    </main>

    <?php include("../includes/footer.php"); ?>
    
    <script>
        // Animación de confeti (opcional)
        function createConfetti() {
            const colors = ['#8b5e3c', '#d2a679', '#f8f6f1', '#2b1e17'];
            const confettiCount = 50;
            
            for (let i = 0; i < confettiCount; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.style.cssText = `
                        position: fixed;
                        width: 10px;
                        height: 10px;
                        background: ${colors[Math.floor(Math.random() * colors.length)]};
                        top: -10px;
                        left: ${Math.random() * 100}%;
                        opacity: ${Math.random()};
                        transform: rotate(${Math.random() * 360}deg);
                        pointer-events: none;
                        z-index: 9999;
                        animation: fall ${2 + Math.random() * 3}s linear forwards;
                    `;
                    document.body.appendChild(confetti);
                    
                    setTimeout(() => confetti.remove(), 5000);
                }, i * 30);
            }
        }
        
        // Agregar CSS para la animación
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fall {
                to {
                    transform: translateY(100vh) rotate(720deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
        
        // Ejecutar confeti al cargar
        window.addEventListener('load', createConfetti);
    </script>
</body>
</html>