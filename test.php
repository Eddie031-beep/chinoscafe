<?php
include("config/db.php");
echo "✅ Conectado correctamente a la base de datos 'chinocafe'";
?>
a<?php
require_once("config/db.php");

try {
    $query = $pdo->query("SELECT NOW() AS fecha_actual");
    $resultado = $query->fetch(PDO::FETCH_ASSOC);
    echo "✅ Conectado correctamente a la base de datos 'chinoscafe_db'<br>";
    echo "📅 Fecha actual del servidor MySQL: " . $resultado['fecha_actual'];
} catch (PDOException $e) {
    echo "❌ Error ejecutando consulta: " . $e->getMessage();
}
?>
