<?php
// views/inventario.php
session_start();
require_once("../config/db.php");
require_once("../includes/helpers.php");

// Verificar que sea administrador
requerirAdmin();

// Manejar acciones (agregar, editar, eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'agregar') {
        $nombre = sanitizar($_POST['nombre'] ?? '');
        $descripcion = sanitizar($_POST['descripcion'] ?? '');
        $precio = (float)($_POST['precio'] ?? 0);
        $categoria = sanitizar($_POST['categoria'] ?? 'Bebida Caliente');
        $stock = (int)($_POST['stock'] ?? 0);
        $stock_minimo = (int)($_POST['stock_minimo'] ?? 5);
        $imagen = sanitizar($_POST['imagen'] ?? 'default.jpg');
        
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
        $id = (int)($_POST['id'] ?? 0);
        $nombre = sanitizar($_POST['nombre'] ?? '');
        $descripcion = sanitizar($_POST['descripcion'] ?? '');
        $precio = (float)($_POST['precio'] ?? 0);
        $categoria = sanitizar($_POST['categoria'] ?? 'Bebida Caliente');
        $stock = (int)($_POST['stock'] ?? 0);
        $stock_minimo = (int)($_POST['stock_minimo'] ?? 5);
        $imagen = sanitizar($_POST['imagen'] ?? 'default.jpg');
        
        $sql = "UPDATE productos SET 
                nombre = :nombre, 
                descripcion = :descripcion, 
                precio = :precio, 
                categoria = :categoria, 
                stock = :stock, 
                stock_minimo = :stock_minimo, 
                imagen = :imagen 
                WHERE id = :id";
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
        $id = (int)($_POST['id'] ?? 0);
        
        // Desactivar en lugar de eliminar
        $sql = "UPDATE productos SET activo = 0 WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        header("Location: inventario.php?msg=eliminado");
        exit;
    }
    
    if ($accion === 'ajustar_stock') {
        $id = (int)($_POST['id'] ?? 0);
        $cantidad = (int)($_POST['cantidad'] ?? 0);
        $tipo = $_POST['tipo'] ?? 'agregar';
        
        if ($tipo === 'agregar') {
            $sql = "UPDATE productos SET stock = stock + :cantidad WHERE id = :id";
        } else {
            $sql = "UPDATE productos SET stock = GREATEST(0, stock - :cantidad) WHERE id = :id";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id, ':cantidad' => abs($cantidad)]);
        
        header("Location: inventario.php?msg=stock_ajustado");
        exit;
    }
}

// Obtener todos los productos
$query = $pdo->query("SELECT * FROM productos WHERE activo = 1 ORDER BY nombre ASC");
$productos = $query->fetchAll(PDO::FETCH_ASSOC);

// Productos con bajo stock - INICIALIZAR AQUÍ
$queryBajo = $pdo->query("SELECT * FROM productos WHERE activo = 1 AND stock <= stock_minimo ORDER BY stock ASC");
$productosBajoStock = $queryBajo->fetchAll(PDO::FETCH_ASSOC);

// Categorías disponibles
$categorias = ['Bebida Caliente', 'Bebida Fría', 'Repostería', 'Snacks', 'Granos', 'Otros'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario | Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .inventario-container {
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
        
        .header-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--cafe-medio), var(--cafe-claro));
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 94, 60, 0.4);
        }
        
        /* Alertas de bajo stock */
        .alertas-stock {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .alertas-stock h3 {
            margin: 0 0 15px 0;
            color: #856404;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alerta-lista {
            display: grid;
            gap: 10px;
        }
        
        .alerta-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px;
            background: #fff;
            border-radius: 8px;
        }
        
        .alerta-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .alerta-info {
            flex: 1;
        }
        
        .alerta-nombre {
            font-weight: 600;
            color: var(--texto);
        }
        
        .alerta-stock {
            font-size: 0.85rem;
            color: #856404;
        }
        
        /* Tabla de productos */
        .productos-table-container {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        
        .productos-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .productos-table thead {
            background: var(--cafe-oscuro);
            color: #fff;
        }
        
        .productos-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .productos-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .productos-table tbody tr:hover {
            background: #f8f6f1;
        }
        
        .producto-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .stock-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .stock-ok {
            background: #d4edda;
            color: #155724;
        }
        
        .stock-low {
            background: #fff3cd;
            color: #856404;
        }
        
        .stock-critical {
            background: #f8d7da;
            color: #721c24;
        }
        
        .precio-badge {
            font-weight: 700;
            color: var(--cafe-medio);
            font-size: 1.1rem;
        }
        
        .table-actions {
            display: flex;
            gap: 8px;
        }
        
        .btn-sm {
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-edit {
            background: #4CAF50;
            color: #fff;
        }
        
        .btn-edit:hover {
            background: #388E3C;
        }
        
        .btn-stock {
            background: #2196F3;
            color: #fff;
        }
        
        .btn-stock:hover {
            background: #1565C0;
        }
        
        .btn-delete {
            background: #f44336;
            color: #fff;
        }
        
        .btn-delete:hover {
            background: #c62828;
        }
        
        /* MODAL */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
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
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #999;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--texto);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--cafe-medio);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
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
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .table-actions {
                flex-direction: column;
            }
            
            .productos-table {
                font-size: 0.85rem;
            }
            
            .productos-table th,
            .productos-table td {
                padding: 10px 8px;
            }
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <main class="inventario-container">
        <div class="page-header">
            <h2>📦 Gestión de Inventario</h2>
            <div class="header-actions">
                <button class="btn-primary" onclick="openModal('agregar')">
                    ➕ Agregar Producto
                </button>
            </div>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <span>✅</span>
                <?php
                    if ($_GET['msg'] === 'agregado') echo 'Producto agregado exitosamente';
                    if ($_GET['msg'] === 'editado') echo 'Producto actualizado exitosamente';
                    if ($_GET['msg'] === 'eliminado') echo 'Producto eliminado exitosamente';
                    if ($_GET['msg'] === 'stock_ajustado') echo 'Stock ajustado exitosamente';
                ?>
            </div>
        <?php endif; ?>

        <?php if (count($productosBajoStock) > 0): ?>
            <div class="alertas-stock">
                <h3>
                    <span>⚠️</span>
                    Productos con Stock Bajo (<?= count($productosBajoStock) ?>)
                </h3>
                <div class="alerta-lista">
                    <?php foreach ($productosBajoStock as $p): ?>
                        <div class="alerta-item">
                            <img src="../assets/img/<?= htmlspecialchars($p['imagen']) ?>" 
                                 alt="<?= htmlspecialchars($p['nombre']) ?>" 
                                 class="alerta-img">
                            <div class="alerta-info">
                                <div class="alerta-nombre"><?= htmlspecialchars($p['nombre']) ?></div>
                                <div class="alerta-stock">
                                    Stock actual: <strong><?= $p['stock'] ?></strong> / 
                                    Mínimo: <?= $p['stock_minimo'] ?>
                                </div>
                            </div>
                            <button class="btn-sm btn-stock" onclick="ajustarStock(<?= $p['id'] ?>, '<?= htmlspecialchars($p['nombre']) ?>')">
                                Ajustar Stock
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="productos-table-container">
            <table class="productos-table">
                <thead>
                    <tr>
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
                    <?php foreach ($productos as $p): ?>
                        <tr>
                            <td>
                                <img src="../assets/img/<?= htmlspecialchars($p['imagen']) ?>" 
                                     alt="<?= htmlspecialchars($p['nombre']) ?>" 
                                     class="producto-img">
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($p['nombre']) ?></strong>
                                <?php if (!empty($p['descripcion'])): ?>
                                    <br><small style="color: #666;"><?= htmlspecialchars(substr($p['descripcion'], 0, 50)) ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($p['categoria']) ?></td>
                            <td>
                                <span class="precio-badge">$<?= number_format($p['precio'], 2) ?></span>
                            </td>
                            <td>
                                <strong><?= $p['stock'] ?></strong> unidades
                            </td>
                            <td>
                                <?php
                                    if ($p['stock'] <= 0) {
                                        echo '<span class="stock-badge stock-critical">Sin Stock</span>';
                                    } elseif ($p['stock'] <= $p['stock_minimo']) {
                                        echo '<span class="stock-badge stock-low">Stock Bajo</span>';
                                    } else {
                                        echo '<span class="stock-badge stock-ok">Stock OK</span>';
                                    }
                                ?>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <button class="btn-sm btn-edit" onclick="editarProducto(<?= htmlspecialchars(json_encode($p)) ?>)">
                                        ✏️ Editar
                                    </button>
                                    <button class="btn-sm btn-stock" onclick="ajustarStock(<?= $p['id'] ?>, '<?= htmlspecialchars($p['nombre']) ?>')">
                                        📊 Stock
                                    </button>
                                    <button class="btn-sm btn-delete" onclick="eliminarProducto(<?= $p['id'] ?>)">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- MODAL PRODUCTO -->
    <div class="modal" id="productoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Agregar Producto</h3>
                <button class="modal-close" onclick="closeModal('productoModal')">✕</button>
            </div>
            
            <form method="POST" id="productoForm">
                <input type="hidden" name="accion" id="accion" value="agregar">
                <input type="hidden" name="id" id="productoId">
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Nombre del Producto *</label>
                        <input type="text" name="nombre" id="nombre" required>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Descripción</label>
                        <textarea name="descripcion" id="descripcion"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Categoría *</label>
                        <select name="categoria" id="categoria" required>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat ?>"><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Precio *</label>
                        <input type="number" step="0.01" name="precio" id="precio" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Stock Inicial *</label>
                        <input type="number" name="stock" id="stock" value="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Stock Mínimo *</label>
                        <input type="number" name="stock_minimo" id="stock_minimo" value="5" required>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Imagen (nombre del archivo)</label>
                        <input type="text" name="imagen" id="imagen" placeholder="cafe-latte.jpg">
                        <small style="color: #666;">La imagen debe estar en assets/img/</small>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('productoModal')">Cancelar</button>
                    <button type="submit" class="btn-submit">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL AJUSTAR STOCK -->
    <div class="modal" id="stockModal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3>Ajustar Stock</h3>
                <button class="modal-close" onclick="closeModal('stockModal')">✕</button>
            </div>
            
            <form method="POST" id="stockForm">
                <input type="hidden" name="accion" value="ajustar_stock">
                <input type="hidden" name="id" id="stockProductoId">
                
                <div class="form-group">
                    <label id="stockProductoNombre" style="color: var(--cafe-oscuro); font-size: 1.1rem;"></label>
                </div>
                
                <div class="form-group">
                    <label>Tipo de Ajuste</label>
                    <select name="tipo" id="stockTipo" required>
                        <option value="agregar">➕ Agregar al Stock</option>
                        <option value="quitar">➖ Quitar del Stock</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Cantidad</label>
                    <input type="number" name="cantidad" id="stockCantidad" min="1" required>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('stockModal')">Cancelar</button>
                    <button type="submit" class="btn-submit">Ajustar</button>
                </div>
            </form>
        </div>
    </div>

    <?php include("../includes/footer.php"); ?>

    <script>
        function openModal(accion) {
            document.getElementById('productoModal').classList.add('active');
            document.getElementById('accion').value = accion;
            document.getElementById('modalTitle').textContent = 'Agregar Producto';
            document.getElementById('productoForm').reset();
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }
        
        function editarProducto(producto) {
            document.getElementById('productoModal').classList.add('active');
            document.getElementById('accion').value = 'editar';
            document.getElementById('modalTitle').textContent = 'Editar Producto';
            
            document.getElementById('productoId').value = producto.id;
            document.getElementById('nombre').value = producto.nombre || '';
            document.getElementById('descripcion').value = producto.descripcion || '';
            document.getElementById('categoria').value = producto.categoria || '';
            document.getElementById('precio').value = producto.precio || '';
            document.getElementById('stock').value = producto.stock || '';
            document.getElementById('stock_minimo').value = producto.stock_minimo || '';
            document.getElementById('imagen').value = producto.imagen || '';
        }
        
        function ajustarStock(id, nombre) {
            document.getElementById('stockModal').classList.add('active');
            document.getElementById('stockProductoId').value = id;
            document.getElementById('stockProductoNombre').textContent = nombre;
            document.getElementById('stockForm').reset();
            document.getElementById('stockProductoId').value = id;
        }
        
        function eliminarProducto(id) {
            if (confirm('¿Estás seguro de eliminar este producto?')) {
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
        
        // Cerrar modal al hacer clic fuera
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>