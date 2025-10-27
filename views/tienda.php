<?php 
require_once("../config/db.php");
global $pdo;
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda | Chinos Café</title>

    <!-- CSS global (navbar, logo, etc.) -->
<link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">

<!-- CSS específico de la tienda -->
<link rel="stylesheet" href="../assets/css/tienda.css?v=<?php echo time(); ?>">
</head>
<body class="page-tienda">

    <!-- ✅ Header global -->
    <?php include("../includes/header.php"); ?>

    <!-- 🟤 HERO PRINCIPAL -->
    <section class="hero-tienda">
        <div class="contenido-hero">
            <h1>Descubre Nuestros Sabores</h1>
            <p>Desde un espresso intenso hasta un latte cremoso — Chinos Café te ofrece lo mejor de cada grano.</p>
            <a href="#productos" class="btn-hero">Explorar Tienda</a>
        </div>
    </section>

    <!-- 🟤 FILTROS -->
    <section class="filtros">
        <button class="filtro-btn activo" data-categoria="todos">Todos</button>
        <button class="filtro-btn" data-categoria="Bebida Caliente">Bebidas Calientes</button>
        <button class="filtro-btn" data-categoria="Bebida Fría">Bebidas Frías</button>
        <button class="filtro-btn" data-categoria="Postre">Postres</button>
    </section>

    <!-- 🟤 PRODUCTOS -->
    <main id="productos" class="productos">
        <h2 class="titulo-seccion">Nuestros Productos</h2>
        <div class="grid" id="productosGrid">
        <?php
try {
    $query = $pdo->query("SELECT * FROM productos");
    if ($query->rowCount() === 0) {
        echo "<p class='sin-productos'>☕ No se encontraron productos disponibles.</p>";
    } else {
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            // --- Datos seguros
            $id     = (int)($row['id'] ?? 0);
            $nombre = htmlspecialchars($row['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
            $desc   = htmlspecialchars($row['descripcion'] ?? '', ENT_QUOTES, 'UTF-8');
            $precio = htmlspecialchars($row['precio'] ?? '0.00', ENT_QUOTES, 'UTF-8');

            // --- Imagen robusta
            $imagenBD = $row['imagen'] ?? '';
            $img = "../assets/img/" . $imagenBD;
            if (!is_file($img)) {
                $base = pathinfo($imagenBD, PATHINFO_FILENAME);
                if (is_file("../assets/img/{$base}.jpg")) {
                    $img = "../assets/img/{$base}.jpg";
                } elseif (is_file("../assets/img/{$base}.jpeg")) {
                    $img = "../assets/img/{$base}.jpeg";
                } else {
                    $img = "../assets/img/default.jpg";
                }
            }

            // --- Categoría (si tu tabla no la tiene, la inferimos por nombre)
            if (isset($row['categoria'])) {
                $categoria = $row['categoria'];
            } else {
                $n = mb_strtolower($row['nombre'] ?? '', 'UTF-8');
                if (str_contains($n, 'latte') || str_contains($n, 'capp') || str_contains($n, 'capucci') || str_contains($n, 'espresso')) {
                    $categoria = 'Bebida Caliente';
                } elseif (str_contains($n, 'cold brew') || str_contains($n, 'frapp') || str_contains($n, 'frío') || str_contains($n, 'fria')) {
                    $categoria = 'Bebida Fría';
                } else {
                    $categoria = 'Postre';
                }
            }
            $categoria = htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8');

            // --- Render tarjeta
            echo "
            <div class='card' data-categoria='{$categoria}'>
                <div class='img-container'>
                    <img src='{$img}' alt='{$nombre}'>
                </div>
                <div class='info'>
                    <h3>{$nombre}</h3>
                    <p>{$desc}</p>
                    <span class='precio'>\$ {$precio}</span>
                </div>
            </div>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
}
?>

        </div>
    </main>

    <!-- ✅ Footer global -->
    <?php include("../includes/footer.php"); ?>

    <!-- ✅ Script JS -->
    <script src="../assets/js/tienda.js"></script>
</body>
</html>
