<?php 
session_start();
require_once("../config/db.php");
require_once("../includes/helpers.php"); // ✅ AGREGAR ESTA LÍNEA
global $pdo;

// Solo administradores pueden acceder
if (!esAdmin()) {
    header("Location: login.php");
    exit;
}

// ... resto del código del archivo inventario.php sin cambios
?>

// Manejar acciones (agregar, editar, eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'agregar') {
        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $precio = $_POST['precio'] ?? 0;
        $categoria = $_POST['categoria'] ?? 'Bebida Caliente';
        $stock = $_POST['stock'] ?? 0;
        $stock_minimo = $_POST['stock_minimo'] ?? 5;
        $imagen = $_POST['imagen'] ?? 'default.jpg';
        
        $sql = "INSERT INTO productos (nombre, descripcion, precio, categoria, stock, stock_minimo, imagen) 
                VALUES (:nombre, :descripcion, :precio, :categoria, :stock, :stock_minimo, :imagen)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':precio' => $precio,
            ':categoria' => $categoria,
            ':stock' => $stock,
            ':stock_minimo' => $stock_minimo,
            ':imagen' => $imagen
        ]);
        
        header("Location: inventario.php?msg=agregado");
        exit;
    }
    
    if ($accion === 'editar') {
        $id = $_POST['id'] ?? 0;
        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $precio = $_POST['precio'] ?? 0;
        $categoria = $_POST['categoria'] ?? 'Bebida Caliente';
        $stock = $_POST['stock'] ?? 0;
        $stock_minimo = $_POST['stock_minimo'] ?? 5;
        $imagen = $_POST['imagen'] ?? 'default.jpg';
        
        $sql = "UPDATE productos SET nombre=:nombre, descripcion=:descripcion, precio=:precio, 
                categoria=:categoria, stock=:stock, stock_minimo=:stock_minimo, imagen=:imagen 
                WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':precio' => $precio,
            ':categoria' => $categoria,
            ':stock' => $stock,
            ':stock_minimo' => $stock_minimo,
            ':imagen' => $imagen
        ]);
        
        header("Location: inventario.php?msg=editado");
        exit;
    }
    
    if ($accion === 'eliminar') {
        $id = $_POST['id'] ?? 0;
        $sql = "DELETE FROM productos WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        header("Location: inventario.php?msg=eliminado");
        exit;
    }
    
    if ($accion === 'ajustar_stock') {
        $id = $_POST['id'] ?? 0;
        $cantidad = $_POST['cantidad'] ?? 0;
        $tipo = $_POST['tipo'] ?? 'agregar';
        
        if ($tipo === 'agregar') {
            $sql = "UPDATE productos SET stock = stock + :cantidad WHERE id = :id";
        } else {
            $sql = "UPDATE productos SET stock = stock - :cantidad WHERE id = :id AND stock >= :cantidad";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id, ':cantidad' => abs($cantidad)]);
        
        header("Location: inventario.php?msg=stock_ajustado");
        exit;
    }
}

// Obtener todos los productos
$query = $pdo->query("SELECT * FROM productos ORDER BY nombre ASC");
$productos = $query->fetchAll(PDO::FETCH_ASSOC);

// Productos con bajo stock
$queryBajo = $pdo->query("SELECT * FROM productos WHERE stock <= stock_minimo ORDER BY stock ASC");
$productosBajoStock = $queryBajo->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .inventario {
            padding: 50px 6%;
            background: var(--crema);
        }
        
        .inv-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .inv-header h2 {
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
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
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
        
        .tabla {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 14px rgba(0,0,0,0.12);
        }
        
        .tabla thead {
            background: var(--cafe-oscuro);
            color: #fff;
        }
        
        .tabla th,
        .tabla td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .tabla tbody tr:hover {
            background: #f8f6f1;
        }
        
        .tabla img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .stock-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .stock-ok {
            background: #d4edda;
            color: #155724;
        }
        
        .stock-bajo {
            background: #fff3cd;
            color: #856404;
        }
        
        .stock-critico {
            background: #f8d7da;
            color: #721c24;
        }
        
        .acciones {
            display: flex;
            gap: 8px;
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
        }
        
        .btn-sm:hover {
            opacity: 0.8;
        }
        
        .btn-edit {
            background: #17a2b8;
            color: #fff;
        }
        
        .btn-stock {
            background: #28a745;
            color: #fff;
        }
        
        .btn-delete {
            background: #dc3545;
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
        .form-group textarea,
        .form-group select {
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
        
        @media (max-width: 768px) {
            .tabla {
                font-size: 0.85rem;
            }
            
            .tabla th,
            .tabla td {
                padding: 10px 8px;
            }
            
            .acciones {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <main class="inventario">
        <div class="inv-header">
            <h2>📦 Gestión de Inventario</h2>
            <button class="btn-primary" onclick="abrirModalAgregar()">+ Agregar Producto</button>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <?php
                    $msg = $_GET['msg'];
                    if ($msg === 'agregado') echo '✅ Producto agregado exitosamente';
                    if ($msg === 'editado') echo '✅ Producto actualizado exitosamente';
                    if ($msg === 'eliminado') echo '✅ Producto eliminado exitosamente';
                    if ($msg === 'stock_ajustado') echo '✅ Stock ajustado correctamente';
                ?>
            </div>
        <?php endif; ?>

        <?php if (count($productosBajoStock) > 0): ?>
            <div class="alert alert-warning">
                ⚠️ <strong><?= count($productosBajoStock) ?> producto(s)</strong> con stock bajo o crítico.
            </div>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="stats">
            <div class="stat-card">
                <h3><?= count($productos) ?></h3>
                <p>Total Productos</p>
            </div>
            <div class="stat-card">
                <h3><?= array_sum(array_column($productos, 'stock')) ?></h3>
                <p>Unidades en Stock</p>
            </div>
            <div class="stat-card">
                <h3><?= count($productosBajoStock) ?></h3>
                <p>Productos Bajo Stock</p>
            </div>
            <div class="stat-card">
                <h3>$<?= number_format(array_sum(array_map(fn($p) => $p['precio'] * $p['stock'], $productos)), 2) ?></h3>
                <p>Valor Total Inventario</p>
            </div>
        </div>

        <!-- Tabla de Productos -->
        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $prod): ?>
                    <?php
                        $stockClass = 'stock-ok';
                        $stockLabel = 'Normal';
                        if ($prod['stock'] <= 0) {
                            $stockClass = 'stock-critico';
                            $stockLabel = 'Agotado';
                        } elseif ($prod['stock'] <= $prod['stock_minimo']) {
                            $stockClass = 'stock-bajo';
                            $stockLabel = 'Bajo';
                        }
                    ?>
                    <tr>
                        <td><?= $prod['id'] ?></td>
                        <td>
                            <img src="../assets/img/<?= htmlspecialchars($prod['imagen']) ?>" alt="<?= htmlspecialchars($prod['nombre']) ?>">
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($prod['nombre']) ?></strong><br>
                            <small style="color: #666;"><?= htmlspecialchars(substr($prod['descripcion'], 0, 50)) ?>...</small>
                        </td>
                        <td><?= htmlspecialchars($prod['categoria']) ?></td>
                        <td>$<?= number_format($prod['precio'], 2) ?></td>
                        <td><?= $prod['stock'] ?> / <?= $prod['stock_minimo'] ?></td>
                        <td><span class="stock-badge <?= $stockClass ?>"><?= $stockLabel ?></span></td>
                        <td class="acciones">
                            <button class="btn-sm btn-edit" onclick='editarProducto(<?= json_encode($prod) ?>)'>✏️ Editar</button>
                            <button class="btn-sm btn-stock" onclick="ajustarStock(<?= $prod['id'] ?>, '<?= htmlspecialchars($prod['nombre']) ?>')">📊 Stock</button>
                            <button class="btn-sm btn-delete" onclick="eliminarProducto(<?= $prod['id'] ?>, '<?= htmlspecialchars($prod['nombre']) ?>')">🗑️</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <!-- Modal Agregar/Editar Producto -->
    <div id="modalProducto" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Agregar Producto</h3>
                <button class="close-modal" onclick="cerrarModal('modalProducto')">&times;</button>
            </div>
            <form method="POST" id="formProducto">
                <input type="hidden" name="accion" id="accion" value="agregar">
                <input type="hidden" name="id" id="productoId">
                
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" name="nombre" id="nombre" required>
                </div>
                
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descripcion" id="descripcion"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Precio ($) *</label>
                    <input type="number" step="0.01" name="precio" id="precio" required>
                </div>
                
                <div class="form-group">
                    <label>Categoría *</label>
                    <select name="categoria" id="categoria" required>
                        <option value="Bebida Caliente">Bebida Caliente</option>
                        <option value="Bebida Fría">Bebida Fría</option>
                        <option value="Postre">Postre</option>
                        <option value="Panadería">Panadería</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Stock Inicial *</label>
                    <input type="number" name="stock" id="stock" required>
                </div>
                
                <div class="form-group">
                    <label>Stock Mínimo *</label>
                    <input type="number" name="stock_minimo" id="stock_minimo" value="5" required>
                </div>
                
                <div class="form-group">
                    <label>Nombre de Imagen</label>
                    <input type="text" name="imagen" id="imagen" placeholder="ejemplo.jpg">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="cerrarModal('modalProducto')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Ajustar Stock -->
    <div id="modalStock" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Ajustar Stock</h3>
                <button class="close-modal" onclick="cerrarModal('modalStock')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="accion" value="ajustar_stock">
                <input type="hidden" name="id" id="stockProductoId">
                
                <p id="stockProductoNombre" style="margin-bottom: 20px; color: var(--cafe-oscuro); font-weight: 600;"></p>
                
                <div class="form-group">
                    <label>Tipo de Ajuste</label>
                    <select name="tipo" id="tipoAjuste" required>
                        <option value="agregar">➕ Agregar Stock</option>
                        <option value="retirar">➖ Retirar Stock</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Cantidad</label>
                    <input type="number" name="cantidad" id="cantidad" min="1" required>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="cerrarModal('modalStock')">Cancelar</button>
                    <button type="submit" class="btn-primary">Ajustar Stock</button>
                </div>
            </form>
        </div>
    </div>

    <?php include("../includes/footer.php"); ?>

    <script>
        function abrirModalAgregar() {
            document.getElementById('modalTitle').textContent = 'Agregar Producto';
            document.getElementById('accion').value = 'agregar';
            document.getElementById('formProducto').reset();
            document.getElementById('productoId').value = '';
            document.getElementById('modalProducto').classList.add('show');
        }

        function editarProducto(producto) {
            document.getElementById('modalTitle').textContent = 'Editar Producto';
            document.getElementById('accion').value = 'editar';
            document.getElementById('productoId').value = producto.id;
            document.getElementById('nombre').value = producto.nombre;
            document.getElementById('descripcion').value = producto.descripcion;
            document.getElementById('precio').value = producto.precio;
            document.getElementById('categoria').value = producto.categoria;
            document.getElementById('stock').value = producto.stock;
            document.getElementById('stock_minimo').value = producto.stock_minimo;
            document.getElementById('imagen').value = producto.imagen;
            document.getElementById('modalProducto').classList.add('show');
        }

        function ajustarStock(id, nombre) {
            document.getElementById('stockProductoId').value = id;
            document.getElementById('stockProductoNombre').textContent = '📦 ' + nombre;
            document.getElementById('modalStock').classList.add('show');
        }

        function eliminarProducto(id, nombre) {
            if (confirm('¿Estás seguro de eliminar "' + nombre + '"?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="accion" value="eliminar">
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