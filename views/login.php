<?php
session_start();
require_once("../config/db.php");
require_once("../includes/helpers.php");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = sanitizar($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($correo) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ? AND activo = 1");
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($password, $usuario['password_hash'])) {
                // Iniciar sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol'] = $usuario['rol'];

                // Redirigir según el rol
                if ($usuario['rol'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $error = "❌ Correo o contraseña incorrectos.";
            }
        } catch (PDOException $e) {
            $error = "❌ Error al iniciar sesión. Intenta nuevamente.";
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
    <title>Iniciar Sesión | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 50px 6%;
            background: var(--crema);
        }

        .login-form {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-form h2 {
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

        .btn-login {
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

        .btn-login:hover {
            background: var(--cafe-oscuro);
        }

        .register-link {
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
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <main class="login-container">
        <div class="login-form">
            <h2>🔐 Iniciar Sesión</h2>

            <?php if (!empty($error)): ?>
                <div class="alert-error">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="correo" class="form-input" required 
                           value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>"
                           placeholder="tu.correo@ejemplo.com">
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-input" required 
                           placeholder="Tu contraseña">
                </div>

                <button type="submit" class="btn-login">
                    Iniciar Sesión
                </button>
            </form>

            <div class="register-link">
                ¿No tienes cuenta? <a href="register.php">Regístrate aquí</a>
            </div>
        </div>
    </main>

    <?php include("../includes/footer.php"); ?>
</body>
</html>