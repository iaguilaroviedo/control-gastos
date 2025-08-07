<?php
// login.php - Inicio de Sesión
session_start();
require_once 'config/auth.php';

// Si ya está logueado, redirige al dashboard
if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (login($username, $password)) {
        header("Location: index.php");
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Control de Gastos</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            position: relative;
            min-height: 100vh;
        }
        .container {
            max-width: 420px;
            margin: 100px auto;
            padding: 0 15px;
            position: relative;
            z-index: 2;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 50px 40px;
            text-align: center;
        }
        .login-card h2 {
            color: #2E7D32;
            margin-bottom: 30px;
            font-size: 28px;
        }
        .login-card h2 i {
            margin-right: 10px;
        }
        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .btn {
            background-color: #2E7D32;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            width: 100%;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #2C6C2D;
        }
        .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            font-size: 14px;
        }
        .register-link {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        .register-link a {
            color: #2E7D32;
            text-decoration: none;
            font-weight: 500;
        }
        .register-link a:hover {
            text-decoration: underline;
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
    <!-- Plantas decorativas -->
    <img src="assets/images/plant-decorative-left.jpg" alt="Planta" class="plant-left">
    <img src="assets/images/plant-decorative-right.jpg" alt="Planta" class="plant-right">

    <!-- Contenido principal -->
    <div class="container">
        <div class="login-card">
            <h2><i class="fas fa-lock"></i> Iniciar Sesión</h2>
            
            <?php if ($error): ?>
                <div class="alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn">Entrar</button>
            </form>

            <div class="register-link">
                ¿No tienes cuenta? 
                <a href="register.php">Regístrate</a>
            </div>
        </div>
    </div>
</body>
</html>