<?php require_once("../config/db.php"); ?>
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

    <section id="productos" class="productos">
        <h2>Nuestros Productos</h2>
        <div class="grid">
            <?php
            $query = $pdo->query("SELECT * FROM productos LIMIT 6");
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='card'>";
                echo "<img src='../assets/img/{$row['imagen']}' alt='{$row['nombre']}'>";
                echo "<h3>{$row['nombre']}</h3>";
                echo "<p>{$row['descripcion']}</p>";
                echo "<span>\${$row['precio']}</span>";
                echo "</div>";
            }
            ?>
        </div>
    </section>

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
