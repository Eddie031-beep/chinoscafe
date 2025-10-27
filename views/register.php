<?php
session_start();
require_once("../config/db.php");
require_once("../includes/helpers.php");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitizar($_POST['nombre'] ?? '');
    $correo = sanitizar($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!empty($nombre) && !empty($correo) && !empty($password) && !empty($confirm_password)) {
        if ($password !== $confirm_password) {
            $error = "❌ Las contraseñas no coinciden.";
        } else {
            try {
                // Verificar si el correo ya existe
                $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
                $stmt->execute([$correo]);
                if ($stmt->fetch()) {
                    $error = "❌ Este correo ya está registrado.";
                } else {
                    // Hash de la contraseña
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);

                    // Insertar usuario
                    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, password_hash) VALUES (?, ?, ?)");
                    $stmt->execute([$nombre, $correo, $password_hash]);

                    $success = "✅ Registro exitoso. Ahora puedes iniciar sesión.";
                }
            } catch (PDOException $e) {
                $error = "❌ Error al registrar. Intenta nuevamente.";
            }
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
    <title>Registrarse | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 50px 6%;
            background: var(--crema);
        }

        .register-form {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .register-form h2 {
            text-align: center;
            color: var(--cafe-oscuro);
            margin-bottom: 30px;
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

        .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }

        .btn-register {
            width: 100%;
            background: var(--cafe-medio);
            color: #fff;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 15px;
        }

        .btn-register:hover {
            background: var(--cafe-oscuro);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <main class="register-container">
        <div class="register-form">
            <h2>📝 Crear Cuenta</h2>

            <?php if (!empty($error)): ?>
                <div class="alert-error">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert-success">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" name="nombre" class="form-input" required 
                           value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                           placeholder="Tu nombre completo">
                </div>

                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="correo" class="form-input" required 
                           value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>"
                           placeholder="tu.correo@ejemplo.com">
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-input" required 
                           placeholder="Mínimo 6 caracteres">
                </div>

                <div class="form-group">
                    <label class="form-label">Confirmar Contraseña</label>
                    <input type="password" name="confirm_password" class="form-input" required 
                           placeholder="Repite tu contraseña">
                </div>

                <button type="submit" class="btn-register">
                    Registrarse
                </button>
            </form>

            <div class="login-link">
                ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
            </div>
        </div>
    </main>

    <?php include("../includes/footer.php"); ?>
</body>
</html>