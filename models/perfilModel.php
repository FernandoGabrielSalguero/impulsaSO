<?php

require_once __DIR__ . '/../config.php';

class PerfilModel
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Actualiza los datos personales del usuario en user_info.
     */
    public function actualizarInfo(int $userId, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE user_info
             SET nombre           = :nombre,
                 apellido         = :apellido,
                 apodo            = :apodo,
                 fecha_nacimiento = :fecha_nacimiento
             WHERE user_auth_id = :id"
        );

        return $stmt->execute([
            'nombre'           => $data['nombre']           ?: null,
            'apellido'         => $data['apellido']         ?: null,
            'apodo'            => $data['apodo']            ?: null,
            'fecha_nacimiento' => $data['fecha_nacimiento'] ?: null,
            'id'               => $userId,
        ]);
    }
}
