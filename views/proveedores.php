<?php 
session_start();
require_once("../config/db.php");
global $pdo;

// Manejar acciones (agregar, editar, eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'agregar') {
        $nombre = $_POST['nombre'] ?? '';
        $empresa = $_POST['empresa'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        
        $sql = "INSERT INTO proveedores (nombre, empresa, telefono, correo, direccion) 
                VALUES (:nombre, :empresa, :telefono, :correo, :direccion)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':empresa' => $empresa,
            ':telefono' => $telefono,
            ':correo' => $correo,
            ':direccion' => $direccion
        ]);
        
        header("Location: proveedores.php?msg=agregado");
        exit;
    }
    
    if ($accion === 'editar') {
        $id = $_POST['id'] ?? 0;
        $nombre = $_POST['nombre'] ?? '';
        $empresa = $_POST['empresa'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        
        $sql = "UPDATE proveedores SET nombre=:nombre, empresa=:empresa, telefono=:telefono, 
                correo=:correo, direccion=:direccion WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':nombre' => $nombre,
            ':empresa' => $empresa,
            ':telefono' => $telefono,
            ':correo' => $correo,
            ':direccion' => $direccion
        ]);
        
        header("Location: proveedores.php?msg=editado");
        exit;
    }
    
    if ($accion === 'eliminar') {
        $id = $_POST['id'] ?? 0;
        $sql = "UPDATE proveedores SET activo=0 WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        header("Location: proveedores.php?msg=eliminado");
        exit;
    }
    
    if ($accion === 'activar') {
        $id = $_POST['id'] ?? 0;
        $sql = "UPDATE proveedores SET activo=1 WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        header("Location: proveedores.php?msg=activado");
        exit;
    }
}

// Obtener proveedores
$filtro = $_GET['filtro'] ?? 'activos';
$sql = "SELECT * FROM proveedores ";
if ($filtro === 'activos') {
    $sql .= "WHERE activo = 1 ";
} elseif ($filtro === 'inactivos') {
    $sql .= "WHERE activo = 0 ";
}
$sql .= "ORDER BY nombre ASC";

$query = $pdo->query($sql);
$proveedores = $query->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas
$statsQuery = $pdo->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as activos,
    SUM(CASE WHEN activo = 0 THEN 1 ELSE 0 END) as inactivos
    FROM proveedores");
$stats = $statsQuery->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .proveedores {
            padding: 50px 6%;
            background: var(--crema);
            min-height: calc(100vh - 90px);
        }
        
        .prov-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .prov-header h2 {
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
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: background 0.3s;
        }
        
        .btn-primary:hover {
            background: var(--cafe-oscuro);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .filtros-prov {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filtro-btn {
            padding: 8px 16px;
            border: 2px solid var(--cafe-medio);
            background: #fff;
            color: var(--cafe-medio);
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .filtro-btn.activo,
        .filtro-btn:hover {
            background: var(--cafe-medio);
            color: #fff;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: var(--cafe-medio);
            font-size: 2rem;
        }
        
        .stat-card p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .grid-proveedores {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }
        
        .card-proveedor {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }
        
        .card-proveedor:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .card-proveedor.inactivo {
            opacity: 0.6;
            background: #f5f5f5;
        }
        
        .badge-estado {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-activo {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-inactivo {
            background: #f8d7da;
            color: #721c24;
        }
        
        .card-proveedor h3 {
            margin: 0 0 8px 0;
            color: var(--cafe-oscuro);
            font-size: 1.3rem;
        }
        
        .card-proveedor .empresa {
            color: var(--cafe-medio);
            font-weight: 600;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 8px 0;
            color: #555;
            font-size: 0.9rem;
        }
        
        .info-item svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }
        
        .acciones-card {
            display: flex;
            gap: 8px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .btn-sm {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-block;
            transition: opacity 0.2s;
            flex: 1;
            text-align: center;
        }
        
        .btn-sm:hover {
            opacity: 0.8;
        }
        
        .btn-edit {
            background: #17a2b8;
            color: #fff;
        }
        
        .btn-delete {
            background: #dc3545;
            color: #fff;
        }
        
        .btn-activate {
            background: #28a745;
            color: #fff;
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
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--texto);
            font-weight: 500;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .grid-proveedores {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <main class="proveedores">
        <div class="prov-header">
            <h2>🏢 Gestión de Proveedores</h2>
            <button class="btn-primary" onclick="abrirModalAgregar()">+ Agregar Proveedor</button>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <?php
                    $msg = $_GET['msg'];
                    if ($msg === 'agregado') echo '✅ Proveedor agregado exitosamente';
                    if ($msg === 'editado') echo '✅ Proveedor actualizado exitosamente';
                    if ($msg === 'eliminado') echo '✅ Proveedor desactivado exitosamente';
                    if ($msg === 'activado') echo '✅ Proveedor activado exitosamente';
                ?>
            </div>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="stats">
            <div class="stat-card">
                <h3><?= $stats['total'] ?></h3>
                <p>Total Proveedores</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['activos'] ?></h3>
                <p>Proveedores Activos</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['inactivos'] ?></h3>
                <p>Proveedores Inactivos</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filtros-prov">
            <a href="?filtro=todos" class="filtro-btn <?= $filtro === 'todos' ? 'activo' : '' ?>">Todos</a>
            <a href="?filtro=activos" class="filtro-btn <?= $filtro === 'activos' ? 'activo' : '' ?>">Activos</a>
            <a href="?filtro=inactivos" class="filtro-btn <?= $filtro === 'inactivos' ? 'activo' : '' ?>">Inactivos</a>
        </div>

        <!-- Grid de Proveedores -->
        <?php if (count($proveedores) > 0): ?>
            <div class="grid-proveedores">
                <?php foreach ($proveedores as $prov): ?>
                    <div class="card-proveedor <?= $prov['activo'] ? '' : 'inactivo' ?>">
                        <span class="badge-estado <?= $prov['activo'] ? 'badge-activo' : 'badge-inactivo' ?>">
                            <?= $prov['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                        
                        <h3><?= htmlspecialchars($prov['nombre']) ?></h3>
                        <div class="empresa"><?= htmlspecialchars($prov['empresa']) ?></div>
                        
                        <div class="info-item">
                            <svg fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
                            <span><?= htmlspecialchars($prov['telefono']) ?: 'No disponible' ?></span>
                        </div>
                        
                        <div class="info-item">
                            <svg fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                            <span style="word-break: break-all;"><?= htmlspecialchars($prov['correo']) ?: 'No disponible' ?></span>
                        </div>
                        
                        <?php if ($prov['direccion']): ?>
                            <div class="info-item">
                                <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                                <span><?= htmlspecialchars($prov['direccion']) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="acciones-card">
                            <button class="btn-sm btn-edit" onclick='editarProveedor(<?= json_encode($prov) ?>)'>✏️ Editar</button>
                            <?php if ($prov['activo']): ?>
                                <button class="btn-sm btn-delete" onclick="cambiarEstado(<?= $prov['id'] ?>, '<?= htmlspecialchars($prov['nombre']) ?>', 'eliminar')">🚫 Desactivar</button>
                            <?php else: ?>
                                <button class="btn-sm btn-activate" onclick="cambiarEstado(<?= $prov['id'] ?>, '<?= htmlspecialchars($prov['nombre']) ?>', 'activar')">✅ Activar</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
                <h3>No hay proveedores para mostrar</h3>
                <p>Comienza agregando tu primer proveedor</p>
            </div>
        <?php endif; ?>
    </main>

    <!-- Modal Agregar/Editar Proveedor -->
    <div id="modalProveedor" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Agregar Proveedor</h3>
                <button class="close-modal" onclick="cerrarModal('modalProveedor')">&times;</button>
            </div>
            <form method="POST" id="formProveedor">
                <input type="hidden" name="accion" id="accion" value="agregar">
                <input type="hidden" name="id" id="proveedorId">
                
                <div class="form-group">
                    <label>Nombre del Contacto *</label>
                    <input type="text" name="nombre" id="nombre" required placeholder="Ej: Juan Pérez">
                </div>
                
                <div class="form-group">
                    <label>Empresa *</label>
                    <input type="text" name="empresa" id="empresa" required placeholder="Ej: Café Premium S.A.">
                </div>
                
                <div class="form-group">
                    <label>Teléfono *</label>
                    <input type="tel" name="telefono" id="telefono" required placeholder="Ej: 6000-0000">
                </div>
                
                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input type="email" name="correo" id="correo" placeholder="contacto@empresa.com">
                </div>
                
                <div class="form-group">
                    <label>Dirección</label>
                    <textarea name="direccion" id="direccion" placeholder="Dirección completa del proveedor"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="cerrarModal('modalProveedor')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <?php include("../includes/footer.php"); ?>

    <script>
        function abrirModalAgregar() {
            document.getElementById('modalTitle').textContent = 'Agregar Proveedor';
            document.getElementById('accion').value = 'agregar';
            document.getElementById('formProveedor').reset();
            document.getElementById('proveedorId').value = '';
            document.getElementById('modalProveedor').classList.add('show');
        }

        function editarProveedor(proveedor) {
            document.getElementById('modalTitle').textContent = 'Editar Proveedor';
            document.getElementById('accion').value = 'editar';
            document.getElementById('proveedorId').value = proveedor.id;
            document.getElementById('nombre').value = proveedor.nombre;
            document.getElementById('empresa').value = proveedor.empresa;
            document.getElementById('telefono').value = proveedor.telefono;
            document.getElementById('correo').value = proveedor.correo || '';
            document.getElementById('direccion').value = proveedor.direccion || '';
            document.getElementById('modalProveedor').classList.add('show');
        }

        function cambiarEstado(id, nombre, accion) {
            const mensaje = accion === 'eliminar' 
                ? '¿Desactivar a "' + nombre + '"?' 
                : '¿Activar a "' + nombre + '"?';
            
            if (confirm(mensaje)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="accion" value="${accion}">
                    <input type="hidden" name="id" value="${id}">
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