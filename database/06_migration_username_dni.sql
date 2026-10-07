USE amemt;

-- username era el DNI: los usuarios cargados con letras se igualan a su DNI real.
UPDATE usuarios SET username = dni WHERE username IS NULL OR username <> dni;

-- Consolidación: la columna de acceso pasa a llamarse `dni`
-- (dni ya existe y tiene los mismos valores), se elimina `username`.
ALTER TABLE usuarios DROP INDEX username, DROP COLUMN username;