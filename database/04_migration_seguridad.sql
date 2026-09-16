USE amemt;

-- Tablas de seguridad para rate limiting y auditoría.

CREATE TABLE IF NOT EXISTS rate_limits (
    clave VARCHAR(190) NOT NULL PRIMARY KEY,
    intentos INT UNSIGNED NOT NULL DEFAULT 0,
    ventana_inicio DATETIME NOT NULL,
    bloqueado_hasta DATETIME NULL,
    KEY idx_rl_bloqueo (bloqueado_hasta),
    KEY idx_rl_ventana (ventana_inicio)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS auditoria (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT UNSIGNED NULL,
    accion VARCHAR(100) NOT NULL,
    detalle VARCHAR(255) NULL,
    ip VARCHAR(45) NULL,
    KEY idx_aud_fecha (fecha),
    KEY idx_aud_usuario (id_usuario)
) ENGINE=InnoDB;