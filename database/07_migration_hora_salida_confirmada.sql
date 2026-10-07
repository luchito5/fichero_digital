USE amemt;

-- Alquileres: la hora de salida se puede registrar de forma aproximada al dar de alta
-- el alquiler y luego confirmarse con la hora exacta cuando el quirófano se libera.
ALTER TABLE alquileres
    ADD COLUMN IF NOT EXISTS hora_salida_confirmada TINYINT(1) NOT NULL DEFAULT 0 AFTER hora_salida;
