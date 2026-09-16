USE amemt;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE cirugia_personal;
TRUNCATE TABLE cirugias;
TRUNCATE TABLE fichajes;
TRUNCATE TABLE alquileres;
TRUNCATE TABLE novedades;
TRUNCATE TABLE configuracion_sistema;
TRUNCATE TABLE usuarios;
TRUNCATE TABLE tipos_procedimiento;
TRUNCATE TABLE tipos_personal;
TRUNCATE TABLE rate_limits;
TRUNCATE TABLE auditoria;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO tipos_personal (nombre, descripcion) VALUES
('Cirujano', 'Profesional encargado de realizar procedimientos quirúrgicos'),
('Anestesista', 'Profesional encargado de la anestesia'),
('Enfermero', 'Personal de enfermería'),
('Administrativo', 'Personal administrativo'),
('Mantenimiento', 'Personal de mantenimiento');

INSERT INTO tipos_procedimiento (nombre, activo) VALUES
('Cirugía general', 1),
('Cirugía traumatológica', 1),
('Cirugía cardiovascular', 1),
('Cirugía ambulatoria', 1),
('Consulta prequirúrgica', 1);

INSERT INTO usuarios
(nombre, apellido, dni, especialidad, tipo_contrato, id_tipo_personal, username, password_hash, es_admin, activo)
VALUES
('Juan', 'Pérez', '30111222', 'Cirugía general', 'Por Cirugía', 1, 'jperez', '$2y$10$xSwr/mKdB6G8vFdZ3N1fJOU2vNYsUusqo9o3.CukG9CFm5RCtM1Em', 1, 1),
('María', 'Gómez', '31222333', 'Anestesiología', 'Por Cirugía', 2, 'mgomez', '$2y$10$xSwr/mKdB6G8vFdZ3N1fJOU2vNYsUusqo9o3.CukG9CFm5RCtM1Em', 0, 1),
('Carlos', 'Rodríguez', '32333444', 'Enfermería', 'Fijo', 3, 'crodriguez', '$2y$10$xSwr/mKdB6G8vFdZ3N1fJOU2vNYsUusqo9o3.CukG9CFm5RCtM1Em', 0, 1),
('Lucía', 'Fernández', '33444555', NULL, 'Fijo', 4, 'lfernandez', '$2y$10$xSwr/mKdB6G8vFdZ3N1fJOU2vNYsUusqo9o3.CukG9CFm5RCtM1Em', 1, 1),
('Pedro', 'Martínez', '34555666', NULL, 'Alquiler', 5, 'pmartinez', '$2y$10$xSwr/mKdB6G8vFdZ3N1fJOU2vNYsUusqo9o3.CukG9CFm5RCtM1Em', 0, 1);

INSERT INTO configuracion_sistema
(nombre_institucion, direccion, telefono, email, alerta_salida, validacion_manual, exportacion_auto)
VALUES
('AMEMT', 'Tandil, Buenos Aires', '0249-4440000',
'contacto@amemt.com.ar', 1, 1, 0);

INSERT INTO cirugias
(fecha, hora_inicio, id_tipo_procedimiento, observaciones, registrado_por)
VALUES
(CURDATE(), '09:00:00', 1, 'Cirugía programada.', 4),
(CURDATE(), '11:00:00', 2, 'Procedimiento traumatológico.', 4);

INSERT INTO cirugia_personal
(id_cirugia, id_usuario, rol_en_cirugia)
VALUES
(1, 1, 'Cirujano principal'),
(1, 2, 'Anestesista'),
(1, 3, 'Enfermero'),
(2, 1, 'Cirujano'),
(2, 2, 'Anestesista'),
(2, 3, 'Enfermero');

INSERT INTO fichajes
(id_usuario, fecha, hora_entrada, hora_salida, horas_trabajadas, validado_admin, validado_por, obs_validacion)
VALUES
(1, CURDATE(), '07:45:00', NULL, NULL, 0, NULL, NULL),
(2, CURDATE(), '08:00:00', '14:00:00', 6.00, 1, 4, 'Validado.'),
(3, CURDATE(), '07:50:00', '15:30:00', 7.67, 1, 4, 'Validado.');

INSERT INTO alquileres
(id_usuario, nombre_responsable, institucion, fecha, hora_entrada, hora_salida, horas_uso, dato_facturacion, registrado_por)
VALUES
(NULL, 'Dr. Romero', 'Clínica Norte', CURDATE(), '08:00:00', '10:30:00', 2.50, 'Facturado - Transferencia', 4),
(NULL, 'Dr. Fernández', 'Sanatorio Sur', CURDATE(), '14:00:00', '17:00:00', 3.00, 'Pendiente', 4),
(NULL, 'Dr. Romero', 'Clínica Norte', DATE_SUB(CURDATE(), INTERVAL 3 DAY), '09:00:00', '11:00:00', 2.00, 'Facturado', 4),
(NULL, 'Dra. López', 'Hospital de Tandil', DATE_SUB(CURDATE(), INTERVAL 5 DAY), '10:00:00', '13:30:00', 3.50, 'Pendiente', 4);

INSERT INTO novedades
(id_usuario, tipo, fecha_desde, fecha_hasta, observaciones, registrado_por)
VALUES
(3, 'Licencia', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 9 DAY),
'Licencia programada.', 4),
(2, 'Capacitación', DATE_ADD(CURDATE(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 3 DAY),
'Capacitación interna.', 4);
