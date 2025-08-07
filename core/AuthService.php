<?php

require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/SessionManager.php';

class AuthService
{
    private $authModel;

    public function __construct($pdo)
    {
        $this->authModel = new AuthModel($pdo);
    }

public function login(string $username, string $password): bool
{
    $user = $this->authModel->login($username, $password);
    if (!$user) return false;

    // Obtener permisos desde la base
    $permissions = $this->authModel->getPermissionsByUserId($user['Id']);

    $userSessionData = [
        'id' => $user['Id'],
        'username' => $user['Usuario'],
        'name' => $user['Nombre'],
        'email' => $user['Correo'],
        'phone' => $user['Telefono'],
        'role' => $user['Rol'],
        'status' => $user['Estado'],
        'balance' => $user['Saldo'] ?? 0.00,
        'permissions' => $permissions, // ✅ agregamos permisos
    ];

    SessionManager::setUser($userSessionData);
    return true;
}

    public function logout()
    {
        SessionManager::destroy();
    }
}
