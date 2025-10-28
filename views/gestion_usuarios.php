<?php
session_start();
require_once("../config/db.php");
require_once("../includes/helpers.php");

// Verificar que sea administrador
requerirAdmin();

// Manejar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'agregar') {
        $nombre = sanitizar($_POST['nombre'] ?? '');
        $correo = sanitizar($_POST['correo'] ?? '');
        $password = $_POST['password'] ?? '';
        $rol = sanitizar($_POST['rol'] ?? 'empleado');
        
        if (!empty($nombre) && !empty($correo) && !empty($password)) {
            try {
                // Verificar si el correo ya existe
                $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
                $stmt->execute([$correo]);
                
                if ($stmt->fetch()) {
                    $error = "Este correo ya está registrado";
                } else {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    
                    $sql = "INSERT INTO usuarios (nombre, correo, password_hash, rol) VALUES (?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$nombre, $correo, $password_hash, $rol]);
                    
                    header("Location: gestion_usuarios.php?msg=agregado");
                    exit;
                }
            } catch (PDOException $e) {
                $error = "Error al crear usuario: " . $e->getMessage();
            }
        } else {
            $error = "Completa todos los campos obligatorios";
        }
    }
    
    if ($accion === 'editar') {
        $id = (int)($_POST['id'] ?? 0);
        $nombre = sanitizar($_POST['nombre'] ?? '');
        $correo = sanitizar($_POST['correo'] ?? '');
        $rol = sanitizar($_POST['rol'] ?? 'empleado');
        $password = $_POST['password'] ?? '';
        
        if ($id > 0 && !empty($nombre) && !empty($correo)) {
            try {
                if (!empty($password)) {
                    // Actualizar con nueva contraseña
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "UPDATE usuarios SET nombre = ?, correo = ?, password_hash = ?, rol = ? WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$nombre, $correo, $password_hash, $rol, $id]);
                } else {
                    // Actualizar sin cambiar contraseña
                    $sql = "UPDATE usuarios SET nombre = ?, correo = ?, rol = ? WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$nombre, $correo, $rol, $id]);
                }
                
                header("Location: gestion_usuarios.php?msg=editado");
                exit;
            } catch (PDOException $e) {
                $error = "Error al actualizar usuario: " . $e->getMessage();
            }
        }
    }
    
    if ($accion === 'cambiar_estado') {
        $id = (int)($_POST['id'] ?? 0);
        $activo = (int)($_POST['activo'] ?? 0);
        
        if ($id > 0 && $id != $_SESSION['usuario_id']) {
            $sql = "UPDATE usuarios SET activo = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$activo, $id]);
            
            $msg = $activo ? 'activado' : 'desactivado';
            header("Location: gestion_usuarios.php?msg=$msg");
            exit;
        }
    }
}

// Obtener usuarios
$query = $pdo->query("SELECT * FROM usuarios ORDER BY fecha_creacion DESC");
$usuarios = $query->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas
$stats = [
    'total' => count($usuarios),
    'admins' => count(array_filter($usuarios, fn($u) => $u['rol'] === 'admin')),
    'empleados' => count(array_filter($usuarios, fn($u) => $u['rol'] === 'empleado')),
    'activos' => count(array_filter($usuarios, fn($u) => $u['activo'] == 1))
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .usuarios-container {
            padding: 50px 6%;
            background: var(--crema);
            min-height: calc(100vh - 90px);
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .page-header h2 {
            color: var(--cafe-oscuro);
            margin: 0;
        }
        
        .btn-primary {
            background: var(--cafe-medio);
            color: #fff;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }
        
        .btn-primary:hover {
            background: var(--cafe-oscuro);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: var(--cafe-medio);
            font-size: 2.5rem;
        }
        
        .stat-card p {
            margin: 0;
            color: #666;
            font-size: 0.95rem;
        }
        
        .usuarios-table-container {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 14px rgba(0,0,0,0.12);
        }
        
        .tabla-usuarios {
            width: 100%;
            border-collapse: collapse;
        }
        
        .tabla-usuarios thead {
            background: var(--cafe-oscuro);
            color: #fff;
        }
        
        .tabla-usuarios th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .tabla-usuarios td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .tabla-usuarios tbody tr:hover {
            background: #f8f6f1;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .badge-admin {
            background: #dc3545;
            color: #fff;
        }
        
        .badge-empleado {
            background: #17a2b8;
            color: #fff;
        }
        
        .badge-activo {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-inactivo {
            background: #f8d7da;
            color: #721c24;
        }
        
        .btn-sm {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            margin: 0 3px;
            transition: opacity 0.2s;
        }
        
        .btn-sm:hover {
            opacity: 0.8;
        }
        
        .btn-edit {
            background: #17a2b8;
            color: #fff;
        }
        
        .btn-toggle {
            background: #ffc107;
            color: #000;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        
        .modal.show {
            display: flex;
        }
        
        .modal-content {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--cafe-claro);
        }
        
        .modal-header h3 {
            margin: 0;
            color: var(--cafe-oscuro);
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--texto);
            font-weight: 600;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--cafe-medio);
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }
        
        .btn-cancel {
            flex: 1;
            padding: 10px;
            background: #999;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .btn-submit {
            flex: 2;
            padding: 10px;
            background: var(--cafe-medio);
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .help-text {
            font-size: 0.85rem;
            color: #666;
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .tabla-usuarios {
                font-size: 0.85rem;
            }
            
            .tabla-usuarios th,
            .tabla-usuarios td {
                padding: 10px 8px;
            }
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <main class="usuarios-container">
        <div class="page-header">
            <h2>👥 Gestión de Usuarios</h2>
            <button class="btn-primary" onclick="abrirModalAgregar()">
                ➕ Agregar Usuario
            </button>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <span>✅</span>
                <?php
                    $msg = $_GET['msg'];
                    if ($msg === 'agregado') echo 'Usuario agregado exitosamente';
                    if ($msg === 'editado') echo 'Usuario actualizado exitosamente';
                    if ($msg === 'activado') echo 'Usuario activado exitosamente';
                    if ($msg === 'desactivado') echo 'Usuario desactivado exitosamente';
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <span>⚠️</span>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= $stats['total'] ?></h3>
                <p>Total Usuarios</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['admins'] ?></h3>
                <p>Administradores</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['empleados'] ?></h3>
                <p>Empleados</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['activos'] ?></h3>
                <p>Usuarios Activos</p>
            </div>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="usuarios-table-container">
            <table class="tabla-usuarios">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $user): ?>
                        <tr>
                            <td><strong>#<?= $user['id'] ?></strong></td>
                            <td><?= htmlspecialchars($user['nombre']) ?></td>
                            <td><?= htmlspecialchars($user['correo']) ?></td>
                            <td>
                                <span class="badge badge-<?= $user['rol'] ?>">
                                    <?= $user['rol'] === 'admin' ? '👑 Admin' : '👤 Empleado' ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $user['activo'] ? 'activo' : 'inactivo' ?>">
                                    <?= $user['activo'] ? '✓ Activo' : '✗ Inactivo' ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($user['fecha_creacion'])) ?></td>
                            <td>
                                <button class="btn-sm btn-edit" 
                                        onclick='editarUsuario(<?= json_encode($user, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                    ✏️ Editar
                                </button>
                                <?php if ($user['id'] != $_SESSION['usuario_id']): ?>
                                    <button class="btn-sm btn-toggle" 
                                            onclick="cambiarEstado(<?= $user['id'] ?>, '<?= htmlspecialchars($user['nombre'], ENT_QUOTES) ?>', <?= $user['activo'] ? 0 : 1 ?>)">
                                        <?= $user['activo'] ? '🚫' : '✅' ?>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal Agregar/Editar Usuario -->
    <div id="modalUsuario" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Agregar Usuario</h3>
                <button class="close-modal" onclick="cerrarModal('modalUsuario')">&times;</button>
            </div>
            
            <form method="POST" id="formUsuario">
                <input type="hidden" name="accion" id="accion" value="agregar">
                <input type="hidden" name="id" id="userId">
                
                <div class="form-group">
                    <label>Nombre Completo *</label>
                    <input type="text" name="nombre" id="nombre" required placeholder="Ej: Juan Pérez">
                </div>
                
                <div class="form-group">
                    <label>Correo Electrónico *</label>
                    <input type="email" name="correo" id="correo" required placeholder="correo@ejemplo.com">
                </div>
                
                <div class="form-group">
                    <label>Contraseña <span id="passwordLabel">*</span></label>
                    <input type="password" name="password" id="password" placeholder="Mínimo 6 caracteres">
                    <div class="help-text" id="passwordHelp">La contraseña debe tener al menos 6 caracteres</div>
                </div>
                
                <div class="form-group">
                    <label>Rol *</label>
                    <select name="rol" id="rol" required>
                        <option value="empleado">👤 Empleado</option>
                        <option value="admin">👑 Administrador</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="cerrarModal('modalUsuario')">Cancelar</button>
                    <button type="submit" class="btn-submit">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>

    <?php include("../includes/footer.php"); ?>

    <script>
        function abrirModalAgregar() {
            document.getElementById('modalTitle').textContent = 'Agregar Usuario';
            document.getElementById('accion').value = 'agregar';
            document.getElementById('formUsuario').reset();
            document.getElementById('userId').value = '';
            document.getElementById('password').required = true;
            document.getElementById('passwordLabel').textContent = '*';
            document.getElementById('passwordHelp').textContent = 'La contraseña debe tener al menos 6 caracteres';
            document.getElementById('modalUsuario').classList.add('show');
        }
        
        function editarUsuario(user) {
            document.getElementById('modalTitle').textContent = 'Editar Usuario';
            document.getElementById('accion').value = 'editar';
            document.getElementById('userId').value = user.id;
            document.getElementById('nombre').value = user.nombre || '';
            document.getElementById('correo').value = user.correo || '';
            document.getElementById('password').value = '';
            document.getElementById('password').required = false;
            document.getElementById('passwordLabel').textContent = '';
            document.getElementById('passwordHelp').textContent = 'Deja en blanco si no quieres cambiar la contraseña';
            document.getElementById('rol').value = user.rol || 'empleado';
            document.getElementById('modalUsuario').classList.add('show');
        }
        
        function cambiarEstado(id, nombre, nuevoEstado) {
            const accion = nuevoEstado ? 'activar' : 'desactivar';
            const mensaje = `¿Estás seguro de ${accion} a "${nombre}"?`;
            
            if (confirm(mensaje)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="accion" value="cambiar_estado">
                    <input type="hidden" name="id" value="${id}">
                    <input type="hidden" name="activo" value="${nuevoEstado}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function cerrarModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }
        
        // Cerrar modal al hacer clic fuera
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>