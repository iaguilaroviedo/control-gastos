<?

session_start();
require_once 'config/auth.php';
requireAuth(); // Asegura que el usuario esté logueado
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú - Control de Gastos</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        .menu-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .menu-item {
            margin: 20px 0;
        }
        .menu-item a {
            font-size: 1.2rem;
            text-decoration: none;
            color: #2E7D32;
            font-weight: 500;
            display: block;
            padding: 12px 20px;
            border: 2px solid #2E7D32;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .menu-item a:hover {
            background-color: #2E7D32;
            color: white;
        }
    </style>
</head>
<body>
    <div class="menu-container">
        <h2>📋 Menú de Control de Deudas</h2>
        <div class="menu-item">
            <a href="deudas.php">Gestionar Deudas</a>
        </div>
        <div class="menu-item">
            <a href="index.php">Volver al Dashboard</a>
        </div>
    </div>
</body>
</html>