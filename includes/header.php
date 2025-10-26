<header class="navbar">
    <div class="container">
        <a href="../views/index.php" class="logo">
            <img src="../assets/img/Logo.jpg" alt="Chinos Café Logo">
            <span>Chinos Café</span>
        </a>

        <nav class="menu">
            <ul>
                <li><a href="../views/index.php">Inicio</a></li>
                <li><a href="../views/tienda.php">Tienda</a></li>
                <li><a href="#productos">Productos</a></li>
                <li><a href="#contacto">Contacto</a></li>

                <?php
                // 🧩 Solo mostrar el enlace Admin si hay sesión activa de administrador
                if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
                    echo '<li><a href="../views/admin/dashboard.php">Admin</a></li>';
                }
                ?>
            </ul>
        </nav>
    </div>
</header>
