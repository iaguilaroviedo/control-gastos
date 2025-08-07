<?php
// deudas.php - Módulo de Gestionar Deudas
require_once 'config/database.php';

// Procesar acciones (Insertar, Actualizar, Eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $nombre = $_POST['nombre'] ?? '';
        $institucion = $_POST['institucion'] ?? '';
        $total_adeudado = $_POST['total_adeudado'] ?? 0;
        $pago_quincenal = $_POST['pago_quincenal'] ?? 0;
        $fecha_pago = $_POST['fecha_pago'] ?? '';
        $saldo_restante = $_POST['saldo_restante'] ?? 0;

        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO deudas (nombre, institucion, total_adeudado, pago_quincenal, fecha_pago, saldo_restante) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $institucion, $total_adeudado, $pago_quincenal, $fecha_pago, $saldo_restante]);
        }

        if ($action === 'update') {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("UPDATE deudas SET nombre=?, institucion=?, total_adeudado=?, pago_quincenal=?, fecha_pago=?, saldo_restante=? WHERE id=?");
            $stmt->execute([$nombre, $institucion, $total_adeudado, $pago_quincenal, $fecha_pago, $saldo_restante, $id]);
        }

        if ($action === 'delete') {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM deudas WHERE id = ?");
            $stmt->execute([$id]);
        }
        header("Location: deudas.php");
        exit;
    }
}

// Obtener todas las deudas
$stmt = $pdo->query("SELECT * FROM deudas ORDER BY nombre");
$deudas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Deudas - Control de Gastos</title>
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
        /* --- MENÚ HAMBURGUESA --- */
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
        /* Plantas decorativas (opcional) */
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
            <li><a href="gastos-fijos.php"><i class="fas fa-wrench"></i> Gastos Fijos</a></li>
            <li><a href="control-diario.php"><i class="fas fa-calendar"></i> Control Diario</a></li>
            <li><a href="ahorros.php"><i class="fas fa-piggy-bank"></i> Ahorros</a></li>
            <li><a href="perfil.php"><i class="fas fa-user"></i> Perfil</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
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
        <h2>💳 Control de Deudas</h2>

        <!-- Formulario para agregar o editar -->
        <div class="form-container">
            <h4 id="form-title">Agregar Nueva Deuda</h4>
            <form method="POST" id="deuda-form">
                <input type="hidden" name="action" id="action" value="add">
                <input type="hidden" name="id" id="deuda-id">

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Nombre de la Deuda</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Institución</label>
                        <input type="text" name="institucion" id="institucion" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Total Adeudado</label>
                        <input type="number" step="0.01" name="total_adeudado" id="total_adeudado" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Pago Quincenal</label>
                        <input type="number" step="0.01" name="pago_quincenal" id="pago_quincenal" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Fecha de Pago</label>
                        <input type="text" name="fecha_pago" id="fecha_pago" class="form-control" placeholder="15 y 30" required>
                    </div>
                    <div class="col-md-4">
                        <label>Saldo Restante</label>
                        <input type="number" step="0.01" name="saldo_restante" id="saldo_restante" class="form-control" required>
                    </div>
                </div>

                <div class="text-end">
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>

        <!-- Tabla de deudas -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-success">
                    <tr>
                        <th>Deuda</th>
                        <th>Institución</th>
                        <th>Total Adeudado</th>
                        <th>Pago Quincenal</th>
                        <th>Fecha de Pago</th>
                        <th>Saldo Restante</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($deudas)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No hay deudas registradas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($deudas as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['nombre']) ?></td>
                                <td><?= htmlspecialchars($d['institucion']) ?></td>
                                <td>$<?= number_format($d['total_adeudado'], 2) ?></td>
                                <td>$<?= number_format($d['pago_quincenal'], 2) ?></td>
                                <td><?= htmlspecialchars($d['fecha_pago']) ?></td>
                                <td>$<?= number_format($d['saldo_restante'], 2) ?></td>
                                <td>
                                    <button class="btn btn-edit btn-sm" onclick="editDeuda(<?= htmlspecialchars(json_encode($d)) ?>)">Editar</button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                        <button type="submit" class="btn btn-delete btn-sm" onclick="return confirm('¿Eliminar esta deuda?')">Eliminar</button>
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
        function editDeuda(deuda) {
            document.getElementById('form-title').textContent = 'Editar Deuda';
            document.getElementById('action').value = 'update';
            document.getElementById('deuda-id').value = deuda.id;
            document.getElementById('nombre').value = deuda.nombre;
            document.getElementById('institucion').value = deuda.institucion;
            document.getElementById('total_adeudado').value = deuda.total_adeudado;
            document.getElementById('pago_quincenal').value = deuda.pago_quincenal;
            document.getElementById('fecha_pago').value = deuda.fecha_pago;
            document.getElementById('saldo_restante').value = deuda.saldo_restante;
            window.scrollTo(0, 0);
        }

        function resetForm() {
            document.getElementById('form-title').textContent = 'Agregar Nueva Deuda';
            document.getElementById('action').value = 'add';
            document.getElementById('deuda-form').reset();
            document.getElementById('deuda-id').value = '';
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