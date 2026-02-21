<?php

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
            FROM Usuarios
            WHERE Usuario = :usuario
            LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario' => $usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return null;
        }
        if (($user['Estado'] ?? '') !== 'activo') {
            return ['error' => 'inactive'];
        }

        $hash = $user['Contrasena'] ?? '';
        $isHashed = preg_match('/^\$2y\$/', $hash);

        if (
            (!$isHashed && $hash === $contrasenaIngresada) ||
            ($isHashed && password_verify($contrasenaIngresada, $hash))
        ) {
            return $user;
        }

        return null;
    }

    public function register($nombre, $correo, $contrasena, $rol = 'representante')
    {
        $correo = trim((string) $correo);
        $usuario = $correo;

        $existsSql = "SELECT Id FROM Usuarios WHERE Correo = :correo OR Usuario = :usuario LIMIT 1";
        $existsStmt = $this->db->prepare($existsSql);
        $existsStmt->execute([
            'correo' => $correo,
            'usuario' => $usuario,
        ]);
        if ($existsStmt->fetch(PDO::FETCH_ASSOC)) {
            return ['error' => 'exists'];
        }

        $hash = password_hash($contrasena, PASSWORD_BCRYPT);
        $sql = "INSERT INTO Usuarios (Nombre, Usuario, Correo, Contrasena, Rol, Estado)
                VALUES (:nombre, :usuario, :correo, :contrasena, :rol, 'activo')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nombre' => $nombre,
            'usuario' => $usuario,
            'correo' => $correo,
            'contrasena' => $hash,
            'rol' => $rol,
        ]);

        return [
            'id' => $this->db->lastInsertId(),
            'usuario' => $usuario,
            'correo' => $correo,
            'rol' => $rol,
        ];
    }

}
