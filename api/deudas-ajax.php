<?php
// deudas-ajax.php
header('Content-Type: application/json');
require_once 'config/database.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $institucion = $_POST['institucion'] ?? '';
    $total_adeudado = $_POST['total_adeudado'] ?? 0;
    $pago_quincenal = $_POST['pago_quincenal'] ?? 0;
    $fecha_pago = $_POST['fecha_pago'] ?? '';
    $saldo_restante = $_POST['saldo_restante'] ?? 0;

    try {
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO deudas (nombre, institucion, total_adeudado, pago_quincenal, fecha_pago, saldo_restante) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $institucion, $total_adeudado, $pago_quincenal, $fecha_pago, $saldo_restante]);
            $response = ['success' => true, 'message' => 'Deuda agregada'];
        } else {
            $response['message'] = 'Acción no válida';
        }
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>