<?php

session_start();

// Protección: debe estar logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}

// Protección: solo emprendedores
if (($_SESSION['rol'] ?? '') !== 'impulsa_emprendedor') {
    header('Location: /index.php');
    exit;
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/landing_page_requestModel.php';

$userId  = (int) $_SESSION['user_id'];
$model   = new LandingPageRequestModel($pdo);
$request = $model->obtener($userId);
