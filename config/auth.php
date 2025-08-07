<?php
// config/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'database.php';

function register($nombre_completo, $email, $username, $password) {
    global $pdo;
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_completo, email, username, password) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$nombre_completo, $email, $username, $hashed]);
    } catch (Exception $e) {
        return false;
    }
}

function login($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nombre_completo'] = $user['nombre_completo'];
        return true;
    }
    return false;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireAuth() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function logout() {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>