CREATE DATABASE IF NOT EXISTS amemt
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE amemt;

CREATE TABLE IF NOT EXISTS tipos_personal (
    id_tipo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tipos_procedimiento (
    id_tipo_proc INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    dni VARCHAR(20) NOT NULL UNIQUE,
    especialidad VARCHAR(150),
    tipo_contrato VARCHAR(30) NOT NULL DEFAULT 'Fijo',
    id_tipo_personal INT,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    es_admin TINYINT(1) NOT NULL DEFAULT 0,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    sesion_token VARCHAR(64) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_tipo_personal
        FOREIGN KEY (id_tipo_personal) REFERENCES tipos_personal(id_tipo)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS configuracion_sistema (
    id_config INT AUTO_INCREMENT PRIMARY KEY,
    nombre_institucion VARCHAR(150),
    direccion VARCHAR(255),
    telefono VARCHAR(50),
    email VARCHAR(150),
    alerta_salida TINYINT(1) NOT NULL DEFAULT 0,
    validacion_manual TINYINT(1) NOT NULL DEFAULT 0,
    exportacion_auto TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cirugias (
    id_cirugia INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    id_tipo_procedimiento INT NOT NULL,
    observaciones TEXT,
    registrado_por INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cirugias_tipo_procedimiento
        FOREIGN KEY (id_tipo_procedimiento) REFERENCES tipos_procedimiento(id_tipo_proc),
    CONSTRAINT fk_cirugias_registrado_por
        FOREIGN KEY (registrado_por) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cirugia_personal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cirugia INT NOT NULL,
    id_usuario INT NOT NULL,
    rol_en_cirugia VARCHAR(100),
    CONSTRAINT fk_cp_cirugia FOREIGN KEY (id_cirugia)
        REFERENCES cirugias(id_cirugia) ON DELETE CASCADE,
    CONSTRAINT fk_cp_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS alquileres (
    id_alquiler INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NULL,
    nombre_responsable VARCHAR(150),
    institucion VARCHAR(150),
    fecha DATE NOT NULL,
    hora_entrada TIME,
    hora_salida TIME,
    horas_uso DECIMAL(10,2),
    dato_facturacion TEXT,
    registrado_por INT NOT NULL,
    CONSTRAINT fk_alquiler_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
    CONSTRAINT fk_alquiler_registrado FOREIGN KEY (registrado_por)
        REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS fichajes (
    id_fichaje INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha DATE NOT NULL,
    hora_entrada TIME,
    hora_salida TIME,
    horas_trabajadas DECIMAL(10,2),
    validado_admin TINYINT(1) NOT NULL DEFAULT 0,
    validado_por INT NULL,
    obs_validacion TEXT,
    CONSTRAINT fk_fichajes_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario),
    CONSTRAINT fk_fichajes_validado FOREIGN KEY (validado_por)
        REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS novedades (
    id_novedad INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    fecha_desde DATE,
    fecha_hasta DATE,
    observaciones TEXT,
    registrado_por INT NOT NULL,
    CONSTRAINT fk_novedades_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario),
    CONSTRAINT fk_novedades_registrado FOREIGN KEY (registrado_por)
        REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB;

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
