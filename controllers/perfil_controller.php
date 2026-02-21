<?php

session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/perfilModel.php';

$userId          = (int) $_SESSION['user_id'];
$nombre          = trim((string)($_POST['nombre']           ?? ''));
$apellido        = trim((string)($_POST['apellido']         ?? ''));
$apodo           = trim((string)($_POST['apodo']            ?? ''));
$fechaNacimiento = trim((string)($_POST['fecha_nacimiento'] ?? ''));

if ($fechaNacimiento !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaNacimiento)) {
    $fechaNacimiento = '';
}

$model = new PerfilModel($pdo);
$ok    = $model->actualizarInfo($userId, [
    'nombre'           => $nombre,
    'apellido'         => $apellido,
    'apodo'            => $apodo,
    'fecha_nacimiento' => $fechaNacimiento,
]);

if (!$ok) {
    echo json_encode(['ok' => false, 'error' => 'Error al guardar en la base de datos']);
    exit;
}

// Sincronizar sesión
$_SESSION['nombre']           = $nombre           ?: null;
$_SESSION['apellido']         = $apellido         ?: null;
$_SESSION['apodo']            = $apodo            ?: null;
$_SESSION['fecha_nacimiento'] = $fechaNacimiento  ?: null;

echo json_encode(['ok' => true]);
