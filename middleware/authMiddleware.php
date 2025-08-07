<?php
require_once __DIR__ . '/../core/SessionManager.php';

SessionManager::start();

/**
 * Verifica que el usuario esté logueado y tenga el rol adecuado.
 * @param string $requiredRole
 */
function checkAccess(string $requiredRole)
{
    $user = SessionManager::getUser();

    if (!$user || ($user['role'] ?? '') !== $requiredRole) {
        header('Location: /index.php');
        exit;
    }
}
