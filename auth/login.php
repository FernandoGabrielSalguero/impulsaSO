<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 31536000); // 1 anio
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/AuthModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

$usuario = trim($_POST['usuario'] ?? '');
$contrasena = (string)($_POST['contrasena'] ?? '');

if ($usuario === '' || $contrasena === '') {
    registrarAuditoria($pdo, [
        'evento' => 'login_error',
        'estado' => 'invalid',
        'datos' => [
            'usuario' => $usuario,
        ],
    ]);
    header('Location: /index.php?login_error=invalid');
    exit;
}

$auth = new AuthModel($pdo);
$user = $auth->login($usuario, $contrasena);

if (is_array($user) && isset($user['error'])) {
    registrarAuditoria($pdo, [
        'evento' => 'login_error',
        'estado' => $user['error'],
        'datos' => [
            'usuario' => $usuario,
        ],
    ]);

    if ($user['error'] === 'inactive') {
        header('Location: /index.php?login_error=inactive');
        exit;
    }

    header('Location: /index.php?login_error=invalid');
    exit;
}

if ($user) {
    $_SESSION['usuario_id'] = $user['Id'];
    $_SESSION['usuario'] = $user['Usuario'];
    $_SESSION['nombre'] = $user['Nombre'];
    $_SESSION['correo'] = $user['Correo'];
    $_SESSION['telefono'] = $user['Telefono'];
    $_SESSION['rol'] = $user['Rol'];
    $_SESSION['estado'] = $user['Estado'];
    $_SESSION['saldo'] = $user['Saldo'] ?? 0.00;

    registrarAuditoria($pdo, [
        'evento' => 'login_ok',
        'estado' => 'ok',
        'usuario_id' => $user['Id'],
        'usuario_login' => $user['Usuario'],
        'rol' => $user['Rol'],
    ]);

    switch ($user['Rol']) {
        case 'administrador':
            header('Location: /views/admin/admin_dashboard.php');
            break;
        default:
            die('Rol no reconocido: ' . $user['Rol']);
    }

    exit;
}

registrarAuditoria($pdo, [
    'evento' => 'login_error',
    'estado' => 'invalid',
    'datos' => [
        'usuario' => $usuario,
    ],
]);

header('Location: /index.php?login_error=invalid');
exit;
