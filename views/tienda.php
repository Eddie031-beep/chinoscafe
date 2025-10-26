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

    <!-- ✅ Estilos -->
    <link rel="stylesheet" href="../assets/css/tienda.css?v=<?php echo time(); ?>">
</head>
<body>

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
                        $img = "../assets/img/" . $row['imagen'];

                        echo "
                        <div class='card' data-categoria='{$row['categoria']}'>
                            <div class='img-container'>
                                <img src='$img' alt='{$row['nombre']}'>
                            </div>
                            <div class='info'>
                                <h3>{$row['nombre']}</h3>
                                <p>{$row['descripcion']}</p>
                                <span class='precio'>\$ {$row['precio']}</span>
                            </div>
                        </div>";
                    }
                }
            } catch (PDOException $e) {
                echo "<p style='color:red;'>Error: {$e->getMessage()}</p>";
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
