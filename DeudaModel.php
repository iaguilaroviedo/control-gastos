<?php
session_start();
require_once 'config/auth.php';
requireAuth(); // Asegura que el usuario esté logueado
require_once '../config/database.php';

class DeudaModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM deudas");
        return $stmt->fetchAll();
    }

    public function updateSaldo($id, $nuevoSaldo) {
        $stmt = $this->pdo->prepare("UPDATE deudas SET saldo_restante = ? WHERE id = ?");
        return $stmt->execute([$nuevoSaldo, $id]);
    }
}
?>