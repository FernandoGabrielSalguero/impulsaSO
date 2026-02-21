-- Tabla: landing_page_request
-- Ejecutar en la DB antes de usar el módulo de solicitud de landing page.

CREATE TABLE landing_page_request (
    id                    INT AUTO_INCREMENT PRIMARY KEY,
    user_auth_id          INT NOT NULL UNIQUE,
    nombre_emprendimiento VARCHAR(255) NOT NULL,
    fecha_inicio          DATE NOT NULL,
    descripcion           TEXT NOT NULL,
    dominio_registrado    TINYINT(1) NOT NULL DEFAULT 0,
    hosting_propio        TINYINT(1) NOT NULL DEFAULT 0,
    cantidad_colaboradores INT UNSIGNED NOT NULL DEFAULT 0,
    nombre_fundador       VARCHAR(255) NOT NULL,
    vende_productos       TINYINT(1) NOT NULL DEFAULT 0,
    vende_servicios       TINYINT(1) NOT NULL DEFAULT 0,
    ya_factura            TINYINT(1) NOT NULL DEFAULT 0,
    espacio_fisico        TINYINT(1) NOT NULL DEFAULT 0,
    pais                  VARCHAR(100) NULL,
    provincia             VARCHAR(100) NULL,
    localidad             VARCHAR(100) NULL,
    calle                 VARCHAR(255) NULL,
    numero                VARCHAR(20) NULL,
    telefono_contacto     VARCHAR(30) NOT NULL,
    created_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_auth_id) REFERENCES user_auth(id) ON DELETE CASCADE
);
