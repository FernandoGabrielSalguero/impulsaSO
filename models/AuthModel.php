<?php

require_once __DIR__ . '/../config.php';

class AuthModel
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function login($usuario, $contrasenaIngresada)
{
    $sql = "SELECT 
        Id,
        Usuario,
        Contrasena,
        Rol,
        Estado,
        Nombre,
        Correo,
        Telefono,
        Saldo
        FROM users
        WHERE Usuario = :usuario
        LIMIT 1";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['usuario' => $usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || $user['Estado'] !== 'activo') {
        return false;
    }

    $hash = $user['Contrasena'] ?? '';
    $isHashed = preg_match('/^\$2y\$/', $hash);

    if (
        (!$isHashed && $hash === $contrasenaIngresada) ||
        ($isHashed && password_verify($contrasenaIngresada, $hash))
    ) {
        return $user;
    }

    return false;
}

public function getPermissionsByUserId(int $userId): array
{
    $sql = "
        SELECT DISTINCT p.name
        FROM user_roles ur
        JOIN role_permissions rp ON ur.role_id = rp.role_id
        JOIN permissions p ON rp.permission_id = p.id
        WHERE ur.user_id = :user_id
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}


}
