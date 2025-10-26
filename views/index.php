<?php 
session_start();
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
    <title>Chinos Café</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <section class="hero">
        <div class="hero-content">
            <h2>El mejor café artesanal te espera</h2>
            <p>Disfruta de la experiencia Chinos Café, con granos seleccionados y un ambiente único.</p>
            <a href="#contacto" class="btn">Haz tu pedido</a>
        </div>
    </section>

    <!-- 🟤 SECCIÓN DE PRODUCTOS -->
    <section id="productos" class="productos">
        <h2>Nuestros Productos</h2>
        <div class="grid">
            <?php
            try {
                $query = $pdo->query("SELECT * FROM productos LIMIT 6");

                if ($query->rowCount() === 0) {
                    echo "<p style='color:red;'>⚠️ No se encontraron productos en la base de datos.</p>";
                } else {
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                        $img = "../assets/img/" . $row['imagen'];

                        // Verificar si la imagen existe, o usar una de respaldo
                        if (!file_exists($img)) {
                            $nombreBase = pathinfo($row['imagen'], PATHINFO_FILENAME);
                            if (file_exists("../assets/img/{$nombreBase}.jpg")) {
                                $img = "../assets/img/{$nombreBase}.jpg";
                            } elseif (file_exists("../assets/img/{$nombreBase}.jpeg")) {
                                $img = "../assets/img/{$nombreBase}.jpeg";
                            } else {
                                $img = "../assets/img/default.jpg"; // imagen de respaldo
                            }
                        }

                        // Mostrar la tarjeta del producto
                        echo "<div class='card'>";
                        echo "<img src='$img' alt='{$row['nombre']}'>";
                        echo "<h3>{$row['nombre']}</h3>";
                        echo "<p>{$row['descripcion']}</p>";
                        echo "<span>\${$row['precio']}</span>";
                        echo "</div>";
                    }
                }
            } catch (PDOException $e) {
                echo "<p style='color:red;'>Error: {$e->getMessage()}</p>";
            }
            ?>
        </div>
    </section>

    <!-- 🟢 SECCIÓN DE CONTACTO -->
    <section id="contacto" class="contacto">
        <h2>Contáctanos</h2>
        <form method="POST" action="../php/guardar_contacto.php">
            <input type="text" name="nombre" placeholder="Tu nombre" required>
            <input type="email" name="correo" placeholder="Tu correo" required>
            <textarea name="mensaje" placeholder="Tu mensaje" required></textarea>
            <button type="submit">Enviar mensaje</button>
        </form>
    </section>

    <?php include("../includes/footer.php"); ?>
</body>
</html>
