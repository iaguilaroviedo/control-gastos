<?php
// ahorros.php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $fecha = $_POST['fecha'] ?? date('Y-m-d');
    $monto = $_POST['monto'] ?? 0;
    $concepto = $_POST['concepto'] ?? 'Ahorro';

    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO ahorros (fecha, monto, concepto) VALUES (?, ?, ?)");
        $stmt->execute([$fecha, $monto, $concepto]);
    }

    if ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM ahorros WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: ahorros.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM ahorros ORDER BY fecha DESC");
$ahorros = $stmt->fetchAll();
$total = array_sum(array_column($ahorros, 'monto'));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ahorros</title>
    <!-- Igual que antes -->
</head>
<body>
    <!-- Menú hamburguesa -->
    <div class="container-fluid">
        <h2>💰 Ahorros</h2>
        <p><strong>Total Ahorrado:</strong> $<?= number_format($total, 2) ?></p>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" required>
            <input type="number" step="0.01" name="monto" placeholder="Monto" required>
            <input type="text" name="concepto" placeholder="Concepto" required>
            <button type="submit">Agregar</button>
        </form>
        <table class="table">
            <thead><tr><th>Fecha</th><th>Monto</th><th>Concepto</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach ($ahorros as $a): ?>
                <tr>
                    <td><?= $a['fecha'] ?></td>
                    <td>$<?= number_format($a['monto'], 2) ?></td>
                    <td><?= htmlspecialchars($a['concepto']) ?></td>
                    <td>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $a['id'] ?>">
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