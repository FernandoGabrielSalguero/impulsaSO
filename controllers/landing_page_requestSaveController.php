<?php

session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

if (($_SESSION['rol'] ?? '') !== 'impulsa_emprendedor') {
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
require_once __DIR__ . '/../models/landing_page_requestModel.php';

$userId = (int) $_SESSION['user_id'];

// Sanitizar y capturar campos
$nombreEmprendimiento  = trim((string)($_POST['nombre_emprendimiento']  ?? ''));
$fechaInicio           = trim((string)($_POST['fecha_inicio']           ?? ''));
$descripcion           = trim((string)($_POST['descripcion']            ?? ''));
$dominioRegistrado     = isset($_POST['dominio_registrado'])  && $_POST['dominio_registrado']  === '1' ? 1 : 0;
$hostingPropio         = isset($_POST['hosting_propio'])      && $_POST['hosting_propio']      === '1' ? 1 : 0;
$cantidadColaboradores = max(0, (int)($_POST['cantidad_colaboradores'] ?? 0));
$nombreFundador        = trim((string)($_POST['nombre_fundador']        ?? ''));
$vendeProductos        = isset($_POST['vende_productos'])     && $_POST['vende_productos']     === '1' ? 1 : 0;
$vendeServicios        = isset($_POST['vende_servicios'])     && $_POST['vende_servicios']     === '1' ? 1 : 0;
$yaFactura             = isset($_POST['ya_factura'])          && $_POST['ya_factura']          === '1' ? 1 : 0;
$espacioFisico         = isset($_POST['espacio_fisico'])      && $_POST['espacio_fisico']      === '1' ? 1 : 0;
$telefonoContacto      = trim((string)($_POST['telefono_contacto']      ?? ''));

// Campos de dirección (solo si espacio_fisico = 1)
if ($espacioFisico) {
    $pais      = trim((string)($_POST['pais']      ?? '')) ?: null;
    $provincia = trim((string)($_POST['provincia'] ?? '')) ?: null;
    $localidad = trim((string)($_POST['localidad'] ?? '')) ?: null;
    $calle     = trim((string)($_POST['calle']     ?? '')) ?: null;
    $numero    = trim((string)($_POST['numero']    ?? '')) ?: null;
} else {
    $pais = $provincia = $localidad = $calle = $numero = null;
}

// Validaciones de campos requeridos
if ($nombreEmprendimiento === '') {
    echo json_encode(['ok' => false, 'error' => 'El nombre del emprendimiento es requerido']);
    exit;
}
if ($fechaInicio === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaInicio)) {
    echo json_encode(['ok' => false, 'error' => 'La fecha de inicio es requerida y debe ser válida']);
    exit;
}
if ($descripcion === '') {
    echo json_encode(['ok' => false, 'error' => 'La descripción es requerida']);
    exit;
}
if ($nombreFundador === '') {
    echo json_encode(['ok' => false, 'error' => 'El nombre del fundador es requerido']);
    exit;
}
if ($telefonoContacto === '') {
    echo json_encode(['ok' => false, 'error' => 'El teléfono de contacto es requerido']);
    exit;
}
if (!$vendeProductos && !$vendeServicios) {
    echo json_encode(['ok' => false, 'error' => 'Debés seleccionar al menos una opción: vende productos o vende servicios']);
    exit;
}
if ($espacioFisico && ($pais === null || $provincia === null || $localidad === null || $calle === null || $numero === null)) {
    echo json_encode(['ok' => false, 'error' => 'Si tenés espacio físico, completá todos los campos de dirección']);
    exit;
}

$model = new LandingPageRequestModel($pdo);
$ok    = $model->guardar($userId, [
    'nombre_emprendimiento'  => $nombreEmprendimiento,
    'fecha_inicio'           => $fechaInicio,
    'descripcion'            => $descripcion,
    'dominio_registrado'     => $dominioRegistrado,
    'hosting_propio'         => $hostingPropio,
    'cantidad_colaboradores' => $cantidadColaboradores,
    'nombre_fundador'        => $nombreFundador,
    'vende_productos'        => $vendeProductos,
    'vende_servicios'        => $vendeServicios,
    'ya_factura'             => $yaFactura,
    'espacio_fisico'         => $espacioFisico,
    'pais'                   => $pais,
    'provincia'              => $provincia,
    'localidad'              => $localidad,
    'calle'                  => $calle,
    'numero'                 => $numero,
    'telefono_contacto'      => $telefonoContacto,
]);

if (!$ok) {
    echo json_encode(['ok' => false, 'error' => 'Error al guardar en la base de datos']);
    exit;
}

echo json_encode(['ok' => true]);
