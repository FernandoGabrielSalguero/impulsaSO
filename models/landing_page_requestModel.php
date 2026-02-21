<?php

require_once __DIR__ . '/../config.php';

class LandingPageRequestModel
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Devuelve la solicitud existente del usuario junto con nombre y whatsapp del perfil.
     * Si no existe solicitud, devuelve solo nombre y whatsapp para precargar el formulario.
     *
     * @return array<string, mixed>
     */
    public function obtener(int $userId): array
    {
        // Intentar traer solicitud + perfil
        $stmt = $this->db->prepare(
            "SELECT
                lpr.*,
                ui.nombre  AS perfil_nombre,
                uc.whatsapp AS perfil_whatsapp
             FROM landing_page_request lpr
             LEFT JOIN user_info     ui ON ui.user_auth_id = lpr.user_auth_id
             LEFT JOIN user_contacto uc ON uc.user_auth_id = lpr.user_auth_id
             WHERE lpr.user_auth_id = :uid
             LIMIT 1"
        );
        $stmt->execute(['uid' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $row;
        }

        // Sin solicitud: traer solo nombre y whatsapp del perfil para precarga
        $stmt2 = $this->db->prepare(
            "SELECT ui.nombre, uc.whatsapp
             FROM user_auth ua
             LEFT JOIN user_info     ui ON ui.user_auth_id = ua.id
             LEFT JOIN user_contacto uc ON uc.user_auth_id = ua.id
             WHERE ua.id = :uid
             LIMIT 1"
        );
        $stmt2->execute(['uid' => $userId]);
        $perfil = $stmt2->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'perfil_nombre'   => $perfil['nombre']   ?? '',
            'perfil_whatsapp' => $perfil['whatsapp']  ?? '',
        ];
    }

    /**
     * INSERT … ON DUPLICATE KEY UPDATE (UPSERT por UNIQUE en user_auth_id).
     *
     * @param array<string, mixed> $data
     */
    public function guardar(int $userId, array $data): bool
    {
        $sql = "INSERT INTO landing_page_request
                    (user_auth_id, nombre_emprendimiento, fecha_inicio, descripcion,
                     dominio_registrado, hosting_propio, cantidad_colaboradores,
                     nombre_fundador, vende_productos, vende_servicios, ya_factura,
                     espacio_fisico, pais, provincia, localidad, calle, numero,
                     telefono_contacto)
                VALUES
                    (:uid, :nombre_emprendimiento, :fecha_inicio, :descripcion,
                     :dominio_registrado, :hosting_propio, :cantidad_colaboradores,
                     :nombre_fundador, :vende_productos, :vende_servicios, :ya_factura,
                     :espacio_fisico, :pais, :provincia, :localidad, :calle, :numero,
                     :telefono_contacto)
                ON DUPLICATE KEY UPDATE
                    nombre_emprendimiento  = VALUES(nombre_emprendimiento),
                    fecha_inicio           = VALUES(fecha_inicio),
                    descripcion            = VALUES(descripcion),
                    dominio_registrado     = VALUES(dominio_registrado),
                    hosting_propio         = VALUES(hosting_propio),
                    cantidad_colaboradores = VALUES(cantidad_colaboradores),
                    nombre_fundador        = VALUES(nombre_fundador),
                    vende_productos        = VALUES(vende_productos),
                    vende_servicios        = VALUES(vende_servicios),
                    ya_factura             = VALUES(ya_factura),
                    espacio_fisico         = VALUES(espacio_fisico),
                    pais                   = VALUES(pais),
                    provincia              = VALUES(provincia),
                    localidad              = VALUES(localidad),
                    calle                  = VALUES(calle),
                    numero                 = VALUES(numero),
                    telefono_contacto      = VALUES(telefono_contacto)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'uid'                    => $userId,
            'nombre_emprendimiento'  => $data['nombre_emprendimiento'],
            'fecha_inicio'           => $data['fecha_inicio'],
            'descripcion'            => $data['descripcion'],
            'dominio_registrado'     => $data['dominio_registrado'],
            'hosting_propio'         => $data['hosting_propio'],
            'cantidad_colaboradores' => $data['cantidad_colaboradores'],
            'nombre_fundador'        => $data['nombre_fundador'],
            'vende_productos'        => $data['vende_productos'],
            'vende_servicios'        => $data['vende_servicios'],
            'ya_factura'             => $data['ya_factura'],
            'espacio_fisico'         => $data['espacio_fisico'],
            'pais'                   => $data['pais'],
            'provincia'              => $data['provincia'],
            'localidad'              => $data['localidad'],
            'calle'                  => $data['calle'],
            'numero'                 => $data['numero'],
            'telefono_contacto'      => $data['telefono_contacto'],
        ]);
    }
}
