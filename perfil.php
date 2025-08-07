<?php
// perfil.php - Módulo de Perfil de Usuario
session_start();
require_once 'config/auth.php';
requireAuth(); // Protege la página

require_once 'config/database.php';

$user_id = getCurrentUserId();

$error = '';
$success = '';

// Obtener datos del usuario
$stmt = $pdo->prepare("SELECT nombre_completo, email, username FROM usuarios WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("Error: Usuario no encontrado.");
}

// 🔹 Obtener Ingreso Quincenal desde la tabla 'ingresos'
$stmt = $pdo->prepare("SELECT monto FROM ingresos WHERE user_id = ?");
$stmt->execute([$user_id]);
$ingreso_row = $stmt->fetch();
$ingreso_quincenal_actual = $ingreso_row ? (float)$ingreso_row['monto'] : 0.00;

// Procesar actualización del perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $nombre_completo = $_POST['nombre_completo'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validaciones
        if (empty($nombre_completo) || empty($email)) {
            $error = 'Nombre y correo son obligatorios.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Correo inválido.';
        } else {
            // Verificar si el correo ya existe (excluyendo al usuario actual)
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                $error = 'El correo ya está en uso.';
            } else {
                // Actualizar perfil
                $stmt = $pdo->prepare("UPDATE usuarios SET nombre_completo = ?, email = ? WHERE id = ?");
                $stmt->execute([$nombre_completo, $email, $user_id]);

                // Actualizar contraseña si se proporciona
                if (!empty($password)) {
                    if ($password !== $confirm_password) {
                        $error = 'Las contraseñas no coinciden.';
                    } elseif (strlen($password) < 6) {
                        $error = 'La contraseña debe tener al menos 6 caracteres.';
                    } else {
                        $hashed = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                        $stmt->execute([$hashed, $user_id]);
                    }
                }

                if (empty($error)) {
                    $success = 'Perfil actualizado correctamente.';
                    $_SESSION['nombre_completo'] = $nombre_completo;
                }
            }
        }
    }

    // 🔹 Procesar actualización del ingreso quincenal
    if ($action === 'update_income') {
        $nuevo_ingreso = $_POST['ingreso_quincenal'] ?? 0;

        if ($nuevo_ingreso < 0) {
            $error = 'El ingreso no puede ser negativo.';
        } else {
            // Verificar si ya existe un registro
            $stmt = $pdo->prepare("SELECT id FROM ingresos WHERE user_id = ?");
            $stmt->execute([$user_id]);

            if ($stmt->fetch()) {
                // Actualizar
                $stmt = $pdo->prepare("UPDATE ingresos SET monto = ? WHERE user_id = ?");
                $stmt->execute([$nuevo_ingreso, $user_id]);
            } else {
                // Insertar
                $stmt = $pdo->prepare("INSERT INTO ingresos (user_id, monto) VALUES (?, ?)");
                $stmt->execute([$user_id, $nuevo_ingreso]);
            }
            $success = 'Ingreso quincenal actualizado correctamente.';
            // Actualizar valor actual
            $ingreso_quincenal_actual = $nuevo_ingreso;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - Control de Gastos</title>
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
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
        }
        .card-header {
            background-color: #2E7D32;
            color: white;
            font-weight: 500;
            padding: 15px 20px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .card-body {
            padding: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: 500;
            color: #333;
        }
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 10px;
        }
        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: 500;
        }
        .btn-primary {
            background-color: #2E7D32;
            border-color: #2E7D32;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 12px;
            font-size: 14px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
                <li><a href="gastos-fijos.php"><i class="fas fa-wrench"></i> Gastos Fijos</a></li>
                <li><a href="control-diario.php"><i class="fas fa-calendar"></i> Control Diario</a></li>
                <li><a href="ahorros.php"><i class="fas fa-piggy-bank"></i> Ahorros</a></li>
                <li><a href="perfil.php" class="active"><i class="fas fa-user"></i> Perfil</a></li>
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
        <h2>👤 Perfil de Usuario</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Formulario de Perfil -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user-edit"></i> Información Personal
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_profile">
                            <div class="form-group">
                                <label>Nombre Completo</label>
                                <input type="text" name="nombre_completo" class="form-control" value="<?= htmlspecialchars($user['nombre_completo']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Correo Electrónico</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Nombre de Usuario</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                                <small class="text-muted">No se puede cambiar.</small>
                            </div>
                            <div class="form-group">
                                <label>Contraseña Nueva (opcional)</label>
                                <input type="password" name="password" class="form-control" placeholder="Deja en blanco para no cambiarla">
                            </div>
                            <div class="form-group">
                                <label>Confirmar Contraseña</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Confirma la nueva contraseña">
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Formulario de Ingreso Quincenal -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-wallet"></i> Ingreso Quincenal
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_income">
                            <div class="form-group">
                                <label>Ingresa tu Ingreso Quincenal</label>
                                <input type="number" step="0.01" name="ingreso_quincenal" class="form-control" value="<?= $ingreso_quincenal_actual ?>" required>
                                <small class="text-muted">Este valor se usa en el resumen del Dashboard.</small>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Guardar Ingreso</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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