<?php
// control-diario.php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $fecha = $_POST['fecha'] ?? date('Y-m-d');
    $concepto = $_POST['concepto'] ?? '';
    $monto = $_POST['monto'] ?? 0;

    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO control_diario (fecha, concepto, monto) VALUES (?, ?, ?)");
        $stmt->execute([$fecha, $concepto, $monto]);
    }

    if ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM control_diario WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: control-diario.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM control_diario ORDER BY fecha DESC");
$gastos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Control Diario</title>
    <!-- Igual que antes -->
</head>
<body>
    <!-- Menú hamburguesa -->
    <div class="container-fluid">
        <h2>📅 Control Diario</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" required>
            <input type="text" name="concepto" placeholder="Comida, transporte" required>
            <input type="number" step="0.01" name="monto" placeholder="Monto" required>
            <button type="submit">Agregar</button>
        </form>
        <table class="table">
            <thead><tr><th>Fecha</th><th>Concepto</th><th>Monto</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach ($gastos as $g): ?>
                <tr>
                    <td><?= $g['fecha'] ?></td>
                    <td><?= htmlspecialchars($g['concepto']) ?></td>
                    <td>$<?= number_format($g['monto'], 2) ?></td>
                    <td>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $g['id'] ?>">
                            <button type="submit" onclick="return confirm('¿Eliminar?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- JavaScript del menú -->
</body>
</html>