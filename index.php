<?php
// index.php - Dashboard Principal
session_start();
require_once 'config/auth.php';
requireAuth(); // Protege la página

require_once 'config/database.php';

$user_id = getCurrentUserId();

// Obtener ingreso quincenal desde la tabla 'ingresos'
$stmt = $pdo->prepare("SELECT monto FROM ingresos WHERE user_id = ?");
$stmt->execute([$user_id]);
$ingreso_row = $stmt->fetch();
$ingreso_quincenal = $ingreso_row ? (float)$ingreso_row['monto'] : 0.00; // Valor por defecto: 00.00

// Obtener total de gastos fijos
$stmt = $pdo->query("SELECT SUM(monto_estimado) as total FROM gastos_fijos");
$total_gastos_fijos = (float)($stmt->fetch()['total'] ?? 0);

// Obtener total de pagos de deudas
$stmt = $pdo->query("SELECT SUM(pago_quincenal) as total FROM deudas");
$total_pago_deudas = (float)($stmt->fetch()['total'] ?? 0);

// Calcular ahorro / sobrante
$ahorro_sobrante = $ingreso_quincenal - $total_gastos_fijos - $total_pago_deudas;

// Obtener deudas desde la base de datos
try {
    $stmt = $pdo->query("SELECT id, nombre, institucion, pago_quincenal, saldo_restante FROM deudas");
    $deudas = $stmt->fetchAll();
} catch (Exception $e) {
    $deudas = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Gastos Quincenal</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #FFFFFF;
            color: #000000;
            margin: 0;
            padding: 0;
        }
        .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        .header {
            text-align: center;
            margin-top: 60px;
            position: relative;
            z-index: 1;
        }
        .header h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #2E7D32;
        }
        .summary-card {
            background-color: #F5F5F5;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            text-align: center;
            height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .summary-card h2 {
            font-size: 18px;
            font-weight: 500;
            margin: 0;
            color: #333;
        }
        .summary-card p {
            font-size: 24px;
            font-weight: 700;
            margin: 10px 0 0;
            color: #2E7D32;
        }
        .section-title {
            text-align: center;
            margin: 60px 0 30px;
            font-size: 32px;
            font-weight: 700;
            color: #2E7D32;
        }
        .table-responsive {
            overflow-x: auto;
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
        .btn-success {
            background-color: #2E7D32;
            border-color: #2E7D32;
            font-size: 14px;
            padding: 6px 12px;
        }
        .btn-success:hover {
            background-color: #2C6C2D;
            border-color: #2C6C2D;
        }
        .footer {
            text-align: center;
            padding: 30px 0;
            background-color: #F5F5F5;
            border-top: 1px solid #E0E0E0;
            margin-top: 60px;
        }
        .footer p {
            font-size: 14px;
            color: #757575;
            margin: 0;
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
                <li><a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
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
    <!-- Header -->
    <div class="container-fluid">
        <div class="header">
            <h1>💰 Control de Gastos Quincenales</h1>
        </div>
    </div>
    <!-- Resumen General -->
    <div class="container-fluid">
        <div class="row mb-5">
            <div class="col-md-3">
                <div class="summary-card">
                    <h2>Ingreso Quincenal</h2>
                    <p>$<?= number_format($ingreso_quincenal, 2) ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <h2>Gastos Fijos</h2>
                    <p>$<?= number_format($total_gastos_fijos, 2) ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <h2>Pagos de Deudas</h2>
                    <p>$<?= number_format($total_pago_deudas, 2) ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card" style="background-color: #e8f5e9; border: 1px solid #c8e6c9;">
                    <h2>Ahorro / Sobrante</h2>
                    <p style="color: #2E7D32; font-weight: 700;">$<?= number_format($ahorro_sobrante, 2) ?></p>
                </div>
            </div>
        </div>
    </div>
    <!-- Sección de Deudas -->
    <div class="container-fluid">
        <h2 class="section-title">Deudas Activas</h2>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-success">
                    <tr>
                        <th>Deuda</th>
                        <th>Institución</th>
                        <th>Pago Quincenal</th>
                        <th>Saldo Restante</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($deudas)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay deudas registradas. Ve a "Gestionar Deudas" para agregar.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($deudas as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['nombre']) ?></td>
                                <td><?= htmlspecialchars($d['institucion']) ?></td>
                                <td>$<?= number_format($d['pago_quincenal'], 2) ?></td>
                                <td>$<?= number_format($d['saldo_restante'], 2) ?></td>
                                <td>
                                    <a href="deudas.php" class="btn btn-success btn-sm">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Footer -->
    <footer class="footer">
        <p>Diseñado con ❤️ para tu estabilidad financiera</p>
    </footer>
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