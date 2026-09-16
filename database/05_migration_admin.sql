USE amemt;

-- Alquileres: responsables externos (id_usuario pasa a ser opcional).
ALTER TABLE alquileres
    MODIFY id_usuario INT NULL,
    ADD COLUMN IF NOT EXISTS nombre_responsable VARCHAR(150) NULL AFTER id_usuario,
    ADD COLUMN IF NOT EXISTS institucion VARCHAR(150) NULL AFTER nombre_responsable;

-- Sesión global: token por usuario para poder invalidar TODAS las sesiones.
ALTER TABLE usuarios
    ADD COLUMN IF NOT EXISTS sesion_token VARCHAR(64) NULL AFTER activo;