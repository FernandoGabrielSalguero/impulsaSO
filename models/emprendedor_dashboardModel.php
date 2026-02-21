<?php

require_once __DIR__ . '/../config.php';

class EmprendedorDashboardModel
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Devuelve el perfil completo del emprendedor (auth + info + contacto).
     *
     * @return array<string, mixed>
     */
    public function obtenerPerfil(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT
                ua.id,
                ua.correo,
                ua.rol,
                ua.email_verified_at,
                ua.created_at,
                ui.nombre,
                ui.apellido,
                ui.apodo,
                ui.fecha_nacimiento,
                uc.check_correo,
                uc.permison_correo,
                uc.whatsapp,
                uc.check_whatsapp,
                uc.permison_whatsapp
             FROM user_auth ua
             LEFT JOIN user_info     ui ON ui.user_auth_id = ua.id
             LEFT JOIN user_contacto uc ON uc.user_auth_id = ua.id
             WHERE ua.id = :id
             LIMIT 1"
        );
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
}
