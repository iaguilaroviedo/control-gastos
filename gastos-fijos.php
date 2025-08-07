<?php
// gastos-fijos.php - Módulo de Gastos Fijos
require_once 'config/database.php';

// Procesar acciones (Insertar, Actualizar, Eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $gasto = $_POST['gasto'] ?? '';
        $monto_estimado = $_POST['monto_estimado'] ?? 0;
        $fecha_pago = $_POST['fecha_pago'] ?? '';

        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO gastos_fijos (gasto, monto_estimado, fecha_pago) VALUES (?, ?, ?)");
            $stmt->execute([$gasto, $monto_estimado, $fecha_pago]);
        }

        if ($action === 'update') {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("UPDATE gastos_fijos SET gasto=?, monto_estimado=?, fecha_pago=? WHERE id=?");
            $stmt->execute([$gasto, $monto_estimado, $fecha_pago, $id]);
        }

        if ($action === 'delete') {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM gastos_fijos WHERE id = ?");
            $stmt->execute([$id]);
        }
        header("Location: gastos-fijos.php");
        exit;
    }
}

// Obtener todos los gastos fijos
$stmt = $pdo->query("SELECT * FROM gastos_fijos");
$gastos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastos Fijos - Control de Gastos</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .container-fluid {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 15px;
        }
        h2 {
            color: #2E7D32;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-container {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            margin-bottom: 40px;
        }
        .form-container h4 {
            color: #2E7D32;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #2E7D32;
            color: white;
            font-weight: 500;
        }
        .table td, .table th {
            padding: 12px;
            vertical-align: middle;
        }
        .btn-edit {
            background-color: #FFC107;
            border: none;
            color: black;
            font-size: 14px;
            padding: 6px 12px;
        }
        .btn-delete {
            background-color: #D32F2F;
            color: white;
            border: none;
            font-size: 14px;
            padding: 6px 12px;
        }
        .btn-success {
            background-color: #2E7D32;
            border: none;
            font-size: 16px;
            padding: 10px 20px;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        /* --- MENÚ HAMBURGUESA (Popup) --- */
        .hamburger {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #2E7D32;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }
        .hamburger:hover {
            transform: scale(1.1);
        }
        .menu-popup {
            position: fixed;
            top: 0;
            left: -300px;
            width: 280px;
            height: 100%;
            background-color: white;
            box-shadow: 5px 0 15px rgba(0,0,0,0.1);
            z-index: 1001;
            transition: left 0.4s ease;
            padding: 60px 20px 20px;
            overflow-y: auto;
        }
        .menu-popup.active {
            left: 0;
        }
        .menu-content h3 {
            margin-bottom: 20px;
            color: #2E7D32;
            text-align: center;
        }
        .menu-content ul {
            list-style: none;
            padding: 0;
        }
        .menu-content ul li {
            margin: 15px 0;
        }
        .menu-content ul li a {
            color: #333;
            text-decoration: none;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 8px;
            transition: background-color 0.3s;
        }
        .menu-content ul li a:hover,
        .menu-content ul li a.active {
            background-color: #e8f5e9;
            color: #2E7D32;
            font-weight: 500;
        }
        .menu-content ul li a i {
            width: 24px;
            text-align: center;
        }
        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 30px;
            color: #555;
            cursor: pointer;
        }
        .menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s ease;
        }
        .menu-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        /* Plantas decorativas */
        .plant-left,
        .plant-right {
            position: absolute;
            z-index: -1;
        }
        .plant-left {
            left: 0;
            bottom: 0;
            width: 200px;
            margin-bottom: 100px;
        }
        .plant-right {
            right: 0;
            bottom: 0;
            width: 200px;
            margin-bottom: 100px;
        }
    </style>
</head>
<body>
    <!-- Botón de Menú Hamburguesa -->
    <div class="hamburger" id="hamburger">
        <i class="fas fa-bars"></i>
    </div>
    <!-- Popup del Menú -->
    <div class="menu-popup" id="menuPopup">
        <div class="menu-content">
            <span class="close-btn" id="closeBtn">&times;</span>
            <h3>Menú</h3>
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="deudas.php"><i class="fas fa-credit-card"></i> Gestionar Deudas</a></li>
                <li><a href="gastos-fijos.php" class="active"><i class="fas fa-wrench"></i> Gastos Fijos</a></li>
                <li><a href="control-diario.php"><i class="fas fa-calendar"></i> Control Diario</a></li>
                <li><a href="ahorros.php"><i class="fas fa-piggy-bank"></i> Ahorros</a></li>
            </ul>
        </div>
    </div>
    <!-- Fondo oscuro al abrir el menú -->
    <div class="menu-overlay" id="menuOverlay"></div>
    <!-- Plantas decorativas -->
    <img src="assets/images/plant-decorative-left.jpg" alt="Planta" class="plant-left">
    <img src="assets/images/plant-decorative-right.jpg" alt="Planta" class="plant-right">
    <!-- Contenido Principal -->
    <div class="container-fluid">
        <h2>🔧 Gastos Fijos</h2>
        <!-- Formulario para agregar o editar -->
        <div class="form-container">
            <h4 id="form-title">Agregar Nuevo Gasto</h4>
            <form method="POST" id="gasto-form">
                <input type="hidden" name="action" id="action" value="add">
                <input type="hidden" name="id" id="gasto-id">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Gasto</label>
                        <input type="text" name="gasto" id="gasto" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Monto Estimado</label>
                        <input type="number" step="0.01" name="monto_estimado" id="monto_estimado" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Fecha de Pago</label>
                        <input type="text" name="fecha_pago" id="fecha_pago" class="form-control" placeholder="15 y 30" required>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
        <!-- Tabla de gastos fijos -->
        <div class="table-responsive">
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
                    <?php if (empty($gastos)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No hay gastos fijos registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($gastos as $g): ?>
                            <tr>
                                <td><?= htmlspecialchars($g['nombre']) ?></td>
                                <td>$<?= number_format($g['monto_estimado'], 2) ?></td>
                                <td><?= htmlspecialchars($g['fecha_pago']) ?></td>
                                <td>
                                    <button class="btn btn-edit btn-sm" onclick="editGasto(<?= htmlspecialchars(json_encode($g)) ?>)">Editar</button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $g['id'] ?>">
                                        <button type="submit" class="btn btn-delete btn-sm" onclick="return confirm('¿Eliminar este gasto?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- JavaScript para el formulario -->
    <script>
        function editGasto(gasto) {
            document.getElementById('form-title').textContent = 'Editar Gasto';
            document.getElementById('action').value = 'update';
            document.getElementById('gasto-id').value = gasto.id;
            document.getElementById('gasto').value = gasto.gasto;
            document.getElementById('monto_estimado').value = gasto.monto_estimado;
            document.getElementById('fecha_pago').value = gasto.fecha_pago;
            window.scrollTo(0, 0);
        }
        function resetForm() {
            document.getElementById('form-title').textContent = 'Agregar Nuevo Gasto';
            document.getElementById('action').value = 'add';
            document.getElementById('gasto-form').reset();
            document.getElementById('gasto-id').value = '';
        }
    </script>
    <!-- JavaScript del menú hamburguesa -->
    <script>
        const hamburger = document.getElementById('hamburger');
        const menuPopup = document.getElementById('menuPopup');
        const menuOverlay = document.getElementById('menuOverlay');
        const closeBtn = document.getElementById('closeBtn');
        hamburger.addEventListener('click', () => {
            menuPopup.classList.add('active');
            menuOverlay.classList.add('active');
        });
        closeBtn.addEventListener('click', () => {
            menuPopup.classList.remove('active');
            menuOverlay.classList.remove('active');
        });
        menuOverlay.addEventListener('click', () => {
            menuPopup.classList.remove('active');
            menuOverlay.classList.remove('active');
        });
    </script>
</body>
</html>