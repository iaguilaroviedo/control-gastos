<?php
require_once '../config/database.php';
require_once '../controllers/DeudaController.php';

$controller = new DeudaController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'pagar') {
        $controller->pagar();
    }
}
?>