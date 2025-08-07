<?php
// config/database.php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=control_gastos", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<div style='text-align: center; padding: 50px; font-family: Arial;'>
            <h2>⚠️ Error de conexión</h2>
            <p>No se pudo conectar a la base de datos.</p>
            <p><small>Verifica que MySQL esté iniciado en XAMPP.</small></p>
         </div>");
}
?>