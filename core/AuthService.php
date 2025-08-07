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
    $permissions = $this->authModel->getPermissionsByUserId((int)$user['id']);


$userSessionData = [
    'id' => $user['id'],
    'username' => $user['user_name'],
    'email' => $user['email'],
    'role' => $user['rol'],
];

    SessionManager::setUser($userSessionData);
    return true;
}

    public function logout()
    {
        SessionManager::destroy();
    }
}
