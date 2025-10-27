<?php
require_once("../config/db.php"); // Ajusta la ruta según donde esté el archivo

echo "<h3>Información de conexión:</h3>";
echo "Host: " . $host . "<br>";
echo "Database: " . $db . "<br>";

try {
    $stmt = $pdo->query("SELECT DATABASE() as db");
    $current_db = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Base de datos actual: " . $current_db['db'] . "<br><br>";

    // Verificar tabla usuarios
    $stmt = $pdo->query("DESCRIBE usuarios");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Columnas en 'usuarios': " . implode(", ", $columns);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>