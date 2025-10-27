<?php
session_start();
require_once("../config/db.php");
require_once("../includes/helpers.php");

$mensaje_exito = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CORREGIDO: usar sanitizar (con una 't') en lugar de santtizar
    $nombre = sanitizar($_POST['nombre'] ?? '');
    $correo = sanitizar($_POST['correo'] ?? '');
    $mensaje = sanitizar($_POST['mensaje'] ?? '');

    if (!empty($nombre) && !empty($correo) && !empty($mensaje)) {
        try {
            $sql = "INSERT INTO contactos (nombre, correo, mensaje) VALUES (:nombre, :correo, :mensaje)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':correo' => $correo,
                ':mensaje' => $mensaje
            ]);

            $mensaje_exito = "✅ Tu mensaje ha sido enviado. Te contactaremos pronto.";
            
            // Limpiar los campos después del envío exitoso
            $_POST = [];
        } catch (PDOException $e) {
            $error = "❌ Error al enviar el mensaje. Intenta nuevamente.";
        }
    } else {
        $error = "❌ Por favor, completa todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .contacto-hero {
            background: url('../assets/img/hero-cafe.jpg') center/cover no-repeat;
            height: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            position: relative;
        }
        
        .contacto-hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(43, 30, 23, 0.7);
        }
        
        .contacto-hero-content {
            position: relative;
            z-index: 1;
        }
        
        .contacto-form {
            padding: 60px 6%;
            background: var(--crema);
        }
        
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.1);
        }
        
        .contacto-form h2 {
            text-align: center;
            color: var(--cafe-oscuro);
            margin-bottom: 30px;
            font-size: 2rem;
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
        
        .form-input, .form-textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--cafe-medio);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 150px;
        }
        
        .btn-enviar {
            width: 100%;
            background: var(--cafe-medio);
            color: #fff;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-enviar:hover {
            background: var(--cafe-oscuro);
        }
        
        .info-contacto {
            background: var(--cafe-oscuro);
            color: #fff;
            padding: 40px 6%;
            text-align: center;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .info-item {
            padding: 20px;
        }
        
        .info-icon {
            font-size: 2rem;
            margin-bottom: 15px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <!-- Hero Section -->
    <section class="contacto-hero">
        <div class="contacto-hero-content">
            <h1>Contáctanos</h1>
            <p>Estamos aquí para servirte</p>
        </div>
    </section>

    <!-- Formulario de Contacto -->
    <section class="contacto-form">
        <div class="form-container">
            <h2>📬 Envíanos un Mensaje</h2>

            <?php if (!empty($mensaje_exito)): ?>
                <div class="alert alert-success">
                    <?= $mensaje_exito ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Nombre Completo *</label>
                    <input type="text" name="nombre" class="form-input" required 
                           value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                           placeholder="Tu nombre completo">
                </div>

                <div class="form-group">
                    <label class="form-label">Correo Electrónico *</label>
                    <input type="email" name="correo" class="form-input" required 
                           value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>"
                           placeholder="tu.correo@ejemplo.com">
                </div>

                <div class="form-group">
                    <label class="form-label">Mensaje *</label>
                    <textarea name="mensaje" class="form-textarea" required 
                              placeholder="¿En qué podemos ayudarte?"><?= htmlspecialchars($_POST['mensaje'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn-enviar">
                    📤 Enviar Mensaje
                </button>
            </form>
        </div>
    </section>

    <!-- Información de Contacto -->
    <section class="info-contacto">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-icon">📍</div>
                <h3>Dirección</h3>
                <p>Av. Principal #123<br>Ciudad de Panamá, Panamá</p>
            </div>
            
            <div class="info-item">
                <div class="info-icon">📞</div>
                <h3>Teléfono</h3>
                <p>+507 6000-0000<br>Lun-Dom: 7:00 AM - 9:00 PM</p>
            </div>
            
            <div class="info-item">
                <div class="info-icon">✉️</div>
                <h3>Correo</h3>
                <p>info@chinoscafe.com<br>ventas@chinoscafe.com</p>
            </div>
            
            <div class="info-item">
                <div class="info-icon">🕒</div>
                <h3>Horario</h3>
                <p>Lunes a Domingo<br>7:00 AM - 9:00 PM</p>
            </div>
        </div>
    </section>

    <?php include("../includes/footer.php"); ?>
</body>
</html>