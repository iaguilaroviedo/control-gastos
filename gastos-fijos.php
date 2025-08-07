<?php
// gastos-fijos.php
require_once 'config/database.php';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $gasto = $_POST['gasto'] ?? '';
    $monto_estimado = $_POST['monto_estimado'] ?? 0;
    $fecha_pago = $_POST['fecha_pago'] ?? '';

    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO gastos_fijos (gasto, monto_estimado, fecha_pago) VALUES (?, ?, ?)");
        $stmt->execute([$gasto, $monto_estimado, $fecha_pago]);
    }

    if ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM gastos_fijos WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: gastos-fijos.php");
    exit;
}

// Obtener gastos fijos
$stmt = $pdo->query("SELECT * FROM gastos_fijos");
$gastos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastos Fijos</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #ffffff; }
        .container-fluid { max-width: 1200px; margin: 60px auto; padding: 0 15px; }
        h2 { color: #2E7D32; text-align: center; }
        .form-container { background: #f9f9f9; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); margin-bottom: 40px; }
        .table th { background-color: #2E7D32; color: white; }
        .btn-delete { background-color: #D32F2F; color: white; border: none; }
        .btn-success { background-color: #2E7D32; border: none; }
        /* Menú hamburguesa (igual que en deudas.php) */
        .hamburger, .menu-popup, .menu-overlay, .plant-left, .plant-right { /* ... igual que antes ... */ }
    </style>
</head>
<body>
    <!-- Menú hamburguesa (igual que en deudas.php) -->
    <div class="container-fluid">
        <h2>🔧 Gastos Fijos</h2>
        <div class="form-container">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Gasto</label>
                        <input type="text" name="gasto" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Monto Estimado</label>
                        <input type="number" step="0.01" name="monto_estimado" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Fecha de Pago</label>
                        <input type="text" name="fecha_pago" class="form-control" placeholder="15 y 30" required>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-success">Agregar</button>
                </div>
            </form>
        </div>
        <table class="table table-hover">
            <thead class="table-success">
                <tr>
                    <th>Gasto</th>
                    <th>Monto Estimado</th>
                    <th>Fecha de Pago</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gastos as $g): ?>
                <tr>
                    <td><?= htmlspecialchars($g['gasto']) ?></td>
                    <td>$<?= number_format($g['monto_estimado'], 2) ?></td>
                    <td><?= htmlspecialchars($g['fecha_pago']) ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $g['id'] ?>">
                            <button type="submit" class="btn btn-delete btn-sm" onclick="return confirm('¿Eliminar?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- JavaScript del menú hamburguesa -->
    <script>
        // (Igual que en deudas.php)
    </script>
</body>
</html>