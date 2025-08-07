/control-gastos/
│
├── index.php                     → Página principal (entrada)
├── menu.php
├── config/
│   └── database.php              → Conexión a MySQL
│
├── models/
│   ├── IngresoModel.php
│   ├── GastoFijoModel.php
│   ├── DeudaModel.php
│   └── ControlQuincenalModel.php
│
├── views/
│   ├── layout.php                → Plantilla base (header/footer)
│   ├── dashboard.php             → Resumen general
│   ├── gastos-fijos.php
│   ├── deudas.php
│   └── control-diario.php
│
├── controllers/
│   ├── IngresoController.php
│   ├── GastoFijoController.php
│   ├── DeudaController.php
│   └── ControlQuincenalController.php
│
├── assets/
│   ├── css/style.css
│   ├── js/main.js                → AJAX
│   └── js/deudas.js
│
└── api/
    └── deudas-ajax.php           → Endpoints para AJAX