<?php
require_once '../models/DeudaModel.php';

class DeudaController {
    private $model;

    public function __construct($pdo) {
        $this->model = new DeudaModel($pdo);
    }

    public function index() {
        return $this->model->getAll();
    }

    public function pagar() {
        $id = $_POST['id'];
        $saldo_actual = $_POST['saldo_actual'];
        $pago = $_POST['pago'];
        $nuevo_saldo = $saldo_actual - $pago;

        if ($this->model->updateSaldo($id, $nuevo_saldo)) {
            echo json_encode(['success' => true, 'nuevo_saldo' => $nuevo_saldo]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
}
?>