USE amemt;

-- Ejecutar si la base AMEMT ya fue creada con la versión anterior.
ALTER TABLE usuarios
ADD COLUMN IF NOT EXISTS tipo_contrato VARCHAR(30) NOT NULL DEFAULT 'Fijo' AFTER especialidad;

UPDATE usuarios SET tipo_contrato='Fijo' WHERE tipo_contrato IS NULL OR tipo_contrato='';
