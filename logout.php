<?php
require_once 'config/auth.php';
requireAuth(); // Protege la página
session_start();
require_once 'config/auth.php';
logout();
?>