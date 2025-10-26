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
    <link rel="stylesheet" href="../assets/css/tienda.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <!-- 🟤 BANNER PRINCIPAL -->
    <section class="banner-tienda">
        <div class="overlay">
            <h1>Nuestra Tienda</h1>
            <p>Explora los sabores del mundo del café — desde un suave latte hasta un intenso espresso.</p>
        </div>
    </section>

    <!-- 🟤 FILTROS -->
    <section class="filtros">
        <button class="filtro-btn activo" data-categoria="todos">Todos</button>
        <button class="filtro-btn" data-categoria="Bebida Caliente">Bebidas Calientes</button>
        <button class="filtro-btn" data-categoria="Bebida Fría">Bebidas Frías</button>
        <button class="filtro-btn" data-categoria="Postre">Postres</button>
    </section>

    <!-- 🌅 Sección de transición entre banner y productos -->
    <section class="fondo-transicion">
        <h2>Descubre Nuestros Sabores Artesanales</h2>
    </section>

    <!-- 🟤 PRODUCTOS -->
    <main class="tienda">
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
                        if (!file_exists($img)) {
                            $nombreBase = pathinfo($row['imagen'], PATHINFO_FILENAME);
                            if (file_exists("../assets/img/{$nombreBase}.jpg")) $img = "../assets/img/{$nombreBase}.jpg";
                            elseif (file_exists("../assets/img/{$nombreBase}.jpeg")) $img = "../assets/img/{$nombreBase}.jpeg";
                            else $img = "../assets/img/default.jpg";
                        }

                        echo "<div class='card' data-categoria='{$row['categoria']}'>";
                        echo "<img src='$img' alt='{$row['nombre']}'>";
                        echo "<div class='info'>";
                        echo "<h3>{$row['nombre']}</h3>";
                        echo "<p>{$row['descripcion']}</p>";
                        echo "<span class='precio'>\$ {$row['precio']}</span>";
                        echo "</div>";
                        echo "</div>";
                    }
                }
            } catch (PDOException $e) {
                echo "<p style='color:red;'>Error: {$e->getMessage()}</p>";
            }
            ?>
        </div>
    </main>

    <?php include("../includes/footer.php"); ?>

    <script src="../assets/js/tienda.js"></script>
</body>
</html>
