-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-09-2026 a las 16:45:38
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `amemt`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alquileres`
--

CREATE TABLE `alquileres` (
  `id_alquiler` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `nombre_responsable` varchar(150) DEFAULT NULL,
  `institucion` varchar(150) DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `horas_uso` decimal(10,2) DEFAULT NULL,
  `dato_facturacion` text DEFAULT NULL,
  `registrado_por` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alquileres`
--

INSERT INTO `alquileres` (`id_alquiler`, `id_usuario`, `nombre_responsable`, `institucion`, `fecha`, `hora_entrada`, `hora_salida`, `horas_uso`, `dato_facturacion`, `registrado_por`) VALUES
(1, 1, NULL, NULL, '2026-09-07', '18:00:00', '20:00:00', 2.00, 'Facturado - Transferencia', 4),
(2, 2, NULL, NULL, '2026-09-07', '14:00:00', '17:30:00', 3.50, 'Facturado - Efectivo', 4),
(3, 3, NULL, NULL, '2026-09-08', '17:00:00', '19:00:00', 2.00, 'Pendiente de facturación', 4),
(4, NULL, 'Dr. Test', 'Clinica Test', '2026-09-16', '09:00:00', '11:00:00', 2.00, 'Facturado', 1),
(5, NULL, 'Dr.Lopez Rosetti', 'Clinica Chacabuco', '2026-09-15', '13:00:00', '14:30:00', 1.50, 'Debito. $200.000', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `id_usuario` int(10) UNSIGNED DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `detalle` varchar(255) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`id`, `fecha`, `id_usuario`, `accion`, `detalle`, `ip`) VALUES
(50, '2026-09-15 10:38:19', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(51, '2026-09-15 10:38:23', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(52, '2026-09-15 10:38:29', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(53, '2026-09-15 10:38:34', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(54, '2026-09-15 10:38:37', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(55, '2026-09-15 11:02:54', 1, 'login', 'Ingreso correcto', '::1'),
(56, '2026-09-15 11:03:26', 1, 'login', 'Ingreso correcto', '::1'),
(57, '2026-09-15 11:03:26', 1, 'cirugia_creada', 'ID 4', '::1'),
(58, '2026-09-15 11:03:26', 1, 'novedad_creada', 'Vacaciones', '::1'),
(59, '2026-09-15 11:03:26', 1, 'alquiler_creado', 'Dr. Test', '::1'),
(60, '2026-09-15 11:04:22', 1, 'login', 'Ingreso correcto', '::1'),
(61, '2026-09-15 11:04:22', 1, 'configuracion_editada', '', '::1'),
(62, '2026-09-15 11:04:23', 1, 'procedimiento_creado', 'Artroscopia', '::1'),
(63, '2026-09-15 11:04:23', 1, 'password_cambiada', 'Configuración', '::1'),
(64, '2026-09-15 11:04:39', 2, 'login', 'Ingreso correcto', '::1'),
(65, '2026-09-15 11:04:39', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(66, '2026-09-15 11:05:26', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(67, '2026-09-15 11:08:10', 2, 'login', 'Ingreso correcto', '::1'),
(68, '2026-09-15 11:08:10', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(69, '2026-09-15 11:08:41', 2, 'login', 'Ingreso correcto', '::1'),
(70, '2026-09-15 11:08:41', 1, 'login', 'Ingreso correcto', '::1'),
(71, '2026-09-15 11:08:42', 1, 'sesiones_invalidadas', 'Todas las sesiones', '::1'),
(72, '2026-09-15 11:08:50', 1, 'login', 'Ingreso correcto', '::1'),
(73, '2026-09-15 11:09:57', 1, 'login', 'Ingreso correcto', '::1'),
(74, '2026-09-15 11:10:51', 1, 'login', 'Ingreso correcto', '::1'),
(75, '2026-09-15 11:10:51', 1, 'cuenta_editada', 'Configuración', '::1'),
(76, '2026-09-15 11:10:59', 1, 'login', 'Ingreso correcto', '::1'),
(77, '2026-09-15 11:11:39', 1, 'login', 'Ingreso correcto', '::1'),
(78, '2026-09-15 11:14:44', NULL, 'login_fallido', 'DNI: ariel@gmail.com', '::1'),
(79, '2026-09-15 11:14:57', 1, 'login', 'Ingreso correcto', '::1'),
(80, '2026-09-15 11:16:02', 1, 'empleado_editado', 'ID 1', '::1'),
(81, '2026-09-15 11:16:14', 1, 'empleado_editado', 'ID 1', '::1'),
(82, '2026-09-15 11:16:35', 1, 'empleado_baja', 'ID 7', '::1'),
(83, '2026-09-15 11:16:43', 1, 'empleado_editado', 'ID 7', '::1'),
(84, '2026-09-15 11:18:22', 1, 'empleado_creado', 'ID 12 - Leandro Lopez', '::1'),
(85, '2026-09-15 11:19:55', 1, 'cirugia_creada', 'ID 5', '::1'),
(86, '2026-09-15 11:20:09', 1, 'cirugia_eliminada', 'ID 4', '::1'),
(87, '2026-09-15 11:23:11', 1, 'cirugia_eliminada', 'ID 5', '::1'),
(88, '2026-09-15 11:23:43', 1, 'novedad_creada', 'Vacaciones', '::1'),
(89, '2026-09-15 11:25:18', 1, 'alquiler_creado', 'Dr.Lopez Rosetti', '::1'),
(90, '2026-09-15 11:33:44', 1, 'cuenta_editada', 'Configuración', '::1'),
(91, '2026-09-15 11:34:09', 1, 'sesiones_invalidadas', 'Todas las sesiones', '::1'),
(92, '2026-09-15 11:38:45', 1, 'procedimiento_creado', 'Cirugia Corazon', '::1'),
(93, '2026-09-15 11:41:05', NULL, 'login_fallido', 'DNI: 31222333', '::1'),
(94, '2026-09-15 11:41:13', NULL, 'login_fallido', 'DNI: 31222333', '::1'),
(95, '2026-09-15 11:41:29', 2, 'login', 'Ingreso correcto', '::1'),
(96, '2026-09-15 11:41:43', 2, 'fichaje_entrada', 'Hora: 16:41', '::1'),
(97, '2026-09-15 11:42:22', 2, 'fichaje_salida', 'Hora: 16:42', '::1'),
(98, '2026-09-15 11:42:41', 10, 'login', 'Ingreso correcto', '::1'),
(99, '2026-09-15 11:42:47', 10, 'fichaje_entrada', 'Hora: 16:42', '::1'),
(100, '2026-09-15 11:43:29', 10, 'fichaje_salida', 'Hora: 16:43', '::1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cirugias`
--

CREATE TABLE `cirugias` (
  `id_cirugia` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `id_tipo_procedimiento` int(11) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `registrado_por` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cirugias`
--

INSERT INTO `cirugias` (`id_cirugia`, `fecha`, `hora_inicio`, `id_tipo_procedimiento`, `observaciones`, `registrado_por`, `created_at`) VALUES
(1, '2026-09-08', '08:00:00', 1, 'Cirugía programada sin complicaciones.', 4, '2026-09-09 21:21:39'),
(2, '2026-09-08', '10:30:00', 2, 'Paciente ingresado para procedimiento traumatológico.', 4, '2026-09-09 21:21:39'),
(3, '2026-09-09', '09:00:00', 4, 'Procedimiento ambulatorio.', 4, '2026-09-09 21:21:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cirugia_personal`
--

CREATE TABLE `cirugia_personal` (
  `id` int(11) NOT NULL,
  `id_cirugia` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `rol_en_cirugia` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cirugia_personal`
--

INSERT INTO `cirugia_personal` (`id`, `id_cirugia`, `id_usuario`, `rol_en_cirugia`) VALUES
(1, 1, 1, 'Cirujano principal'),
(2, 1, 2, 'Anestesista'),
(3, 1, 3, 'Enfermero'),
(4, 2, 1, 'Cirujano'),
(5, 2, 2, 'Anestesista'),
(6, 2, 3, 'Enfermero'),
(7, 3, 1, 'Cirujano'),
(8, 3, 3, 'Enfermero');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_sistema`
--

CREATE TABLE `configuracion_sistema` (
  `id_config` int(11) NOT NULL,
  `nombre_institucion` varchar(150) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `alerta_salida` tinyint(1) NOT NULL DEFAULT 0,
  `validacion_manual` tinyint(1) NOT NULL DEFAULT 0,
  `exportacion_auto` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_sistema`
--

INSERT INTO `configuracion_sistema` (`id_config`, `nombre_institucion`, `direccion`, `telefono`, `email`, `alerta_salida`, `validacion_manual`, `exportacion_auto`) VALUES
(1, 'AMEMT - Fichero Digital', 'Av. Espora 456, Tandil', '0249-000000', 'admin@amemt.com', 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fichajes`
--

CREATE TABLE `fichajes` (
  `id_fichaje` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `horas_trabajadas` decimal(10,2) DEFAULT NULL,
  `validado_admin` tinyint(1) NOT NULL DEFAULT 0,
  `validado_por` int(11) DEFAULT NULL,
  `obs_validacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `fichajes`
--

INSERT INTO `fichajes` (`id_fichaje`, `id_usuario`, `fecha`, `hora_entrada`, `hora_salida`, `horas_trabajadas`, `validado_admin`, `validado_por`, `obs_validacion`) VALUES
(1, 1, '2026-09-08', '07:45:00', '16:00:00', 8.25, 1, 4, 'Fichaje validado correctamente.'),
(4, 4, '2026-09-08', '08:00:00', '16:00:00', 8.00, 1, 4, 'Control administrativo realizado.'),
(5, 6, '2026-09-09', '18:48:32', NULL, NULL, 0, NULL, NULL),
(12, 2, '2026-09-15', '11:41:43', '11:42:22', 0.01, 0, NULL, NULL),
(13, 10, '2026-09-15', '11:42:47', '11:43:29', 0.01, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `novedades`
--

CREATE TABLE `novedades` (
  `id_novedad` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `fecha_desde` date DEFAULT NULL,
  `fecha_hasta` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `registrado_por` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `novedades`
--

INSERT INTO `novedades` (`id_novedad`, `id_usuario`, `tipo`, `fecha_desde`, `fecha_hasta`, `observaciones`, `registrado_por`) VALUES
(1, 3, 'Licencia', '2026-09-15', '2026-09-17', 'Licencia programada.', 4),
(2, 2, 'Capacitación', '2026-09-12', '2026-09-12', 'Capacitación interna de anestesiología.', 4),
(3, 1, 'Reunión', '2026-09-10', '2026-09-10', 'Reunión del equipo quirúrgico.', 4),
(4, 2, 'Vacaciones', '2026-10-01', '2026-10-10', 'Prueba novedad', 1),
(5, 12, 'Vacaciones', '2026-09-15', '2026-09-30', 'Se va a cataratas.', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rate_limits`
--

CREATE TABLE `rate_limits` (
  `clave` varchar(190) NOT NULL,
  `intentos` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `ventana_inicio` datetime NOT NULL,
  `bloqueado_hasta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rate_limits`
--

INSERT INTO `rate_limits` (`clave`, `intentos`, `ventana_inicio`, `bloqueado_hasta`) VALUES
('fichaje:usuario:10', 2, '2026-09-15 11:42:47', NULL),
('fichaje:usuario:2', 2, '2026-09-15 11:41:43', NULL),
('login:dni:ariel@gmail.com', 1, '2026-09-15 11:14:44', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_personal`
--

CREATE TABLE `tipos_personal` (
  `id_tipo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_personal`
--

INSERT INTO `tipos_personal` (`id_tipo`, `nombre`, `descripcion`) VALUES
(1, 'Cirujano', 'Profesional encargado de realizar procedimientos quirúrgicos'),
(2, 'Anestesista', 'Profesional encargado de la anestesia durante las cirugías'),
(3, 'Enfermero', 'Personal de enfermería'),
(4, 'Administrativo', 'Personal encargado de tareas administrativas'),
(5, 'Mantenimiento', 'Personal encargado del mantenimiento de las instalaciones');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_procedimiento`
--

CREATE TABLE `tipos_procedimiento` (
  `id_tipo_proc` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_procedimiento`
--

INSERT INTO `tipos_procedimiento` (`id_tipo_proc`, `nombre`, `activo`) VALUES
(1, 'Cirugía general', 1),
(2, 'Cirugía traumatológica', 1),
(3, 'Cirugía cardiovascular', 1),
(4, 'Cirugía ambulatoria', 1),
(5, 'Consulta prequirúrgica', 1),
(6, 'Artroscopia', 1),
(7, 'Cirugia Corazon', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `especialidad` varchar(150) DEFAULT NULL,
  `id_tipo_personal` int(11) DEFAULT NULL,
  `tipo_contrato` varchar(30) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `es_admin` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `sesion_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `dni`, `especialidad`, `id_tipo_personal`, `tipo_contrato`, `username`, `password_hash`, `es_admin`, `activo`, `sesion_token`, `created_at`) VALUES
(1, 'Natalia', 'Diaz', '30111222', 'Cirugía general', 1, 'Alquiler', '30111222', '$2y$10$ZJMgVgjr3fXSG0hx/9JjbugWt29HFpkwKcpMprdBoNqqqPslta0f2', 1, 1, 'fdb332abfdad30b432c3223d7efe957cfefea396c57bbc83ce2062c05e197ac4', '2026-09-09 21:21:39'),
(2, 'María', 'Gómez', '31222333', 'Anestesiología', 2, 'Por Hora', 'mgomez', '$2y$10$KJIhpy5iTkQ.L/Ox31.p.eV1yDneQXOG/v.bgZkeI9TOgRjCh6WV.', 0, 1, 'e001aa7d37673032a2ce939a5539c8dcc3b71d72ffa62675ba885b1886618d5b', '2026-09-09 21:21:39'),
(3, 'Carlos', 'Rodríguez', '32333444', 'Enfermería', 3, 'Fijo', 'crodriguez', '$2y$10$oLykXIDMqYjKIURNEcO3SOpa9MdVmD013BFzQL4RKVSzGWM6Cy8Oq', 0, 1, NULL, '2026-09-09 21:21:39'),
(4, 'Lucía', 'Fernández', '33444555', '', 4, 'Fijo', 'lfernandez', '$2y$10$oLykXIDMqYjKIURNEcO3SOpa9MdVmD013BFzQL4RKVSzGWM6Cy8Oq', 1, 1, NULL, '2026-09-09 21:21:39'),
(5, 'Pedro', 'Martínez', '34555666', '', 5, 'Alquiler', 'pmartinez', '$2y$10$oLykXIDMqYjKIURNEcO3SOpa9MdVmD013BFzQL4RKVSzGWM6Cy8Oq', 0, 1, NULL, '2026-09-09 21:21:39'),
(6, 'Nicolas', 'Odasso', '48228401', 'Cirujano', 1, 'Por Hora', '48228401', '$2y$10$oLykXIDMqYjKIURNEcO3SOpa9MdVmD013BFzQL4RKVSzGWM6Cy8Oq', 0, 1, NULL, '2026-09-09 21:45:10'),
(7, 'Ricardo', 'Bochini', '48888666', 'Instrumentador', 5, 'Alquiler', '48888666', '$2y$10$22nts.RGGdB2jyjcv68UW.g86S6Sai.TuCkr3B2O80dhFGChVtnNC', 0, 1, NULL, '2026-09-09 22:26:58'),
(8, 'Quique', 'Laloz', '18999000', 'Instrumentador', 5, 'Alquiler', '18999000', '$2y$10$6plrgFXTOP6BR7yGMs0PQ.JDvD8riAzm3npfirdd4TrVLkWFT5d2K', 0, 1, NULL, '2026-09-09 22:31:13'),
(9, 'Lucrecia', 'Gutierrez', '30300300', 'Instrumentadora', 2, 'Fijo', '30300300', '$2y$10$oLykXIDMqYjKIURNEcO3SOpa9MdVmD013BFzQL4RKVSzGWM6Cy8Oq', 0, 1, NULL, '2026-09-09 22:38:54'),
(10, 'riqui', 'luco', '40400400', 'Cirujano', 4, 'Por Cirugía', '40400400', '$2y$10$oLykXIDMqYjKIURNEcO3SOpa9MdVmD013BFzQL4RKVSzGWM6Cy8Oq', 0, 1, '656786e5eb2ed94ae4450aec6f1d85fbe94425a50b235df5a230ecfc56a5684c', '2026-09-09 22:39:21'),
(12, 'Leandro', 'Lopez', '40600700', 'Cirujano', 1, 'Fijo', '40600700', '$2y$10$Zp/ZoAZugbOZtq.ke/v9guPonbPMp4NbQaHtqzBiK1XKESE/Mzrze', 0, 1, NULL, '2026-09-15 14:18:22');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alquileres`
--
ALTER TABLE `alquileres`
  ADD PRIMARY KEY (`id_alquiler`),
  ADD KEY `fk_alquileres_usuario` (`id_usuario`),
  ADD KEY `fk_alquileres_registrado_por` (`registrado_por`);

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_aud_fecha` (`fecha`),
  ADD KEY `idx_aud_usuario` (`id_usuario`);

--
-- Indices de la tabla `cirugias`
--
ALTER TABLE `cirugias`
  ADD PRIMARY KEY (`id_cirugia`),
  ADD KEY `fk_cirugias_tipo_procedimiento` (`id_tipo_procedimiento`),
  ADD KEY `fk_cirugias_registrado_por` (`registrado_por`);

--
-- Indices de la tabla `cirugia_personal`
--
ALTER TABLE `cirugia_personal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cirugia_personal_cirugia` (`id_cirugia`),
  ADD KEY `fk_cirugia_personal_usuario` (`id_usuario`);

--
-- Indices de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  ADD PRIMARY KEY (`id_config`);

--
-- Indices de la tabla `fichajes`
--
ALTER TABLE `fichajes`
  ADD PRIMARY KEY (`id_fichaje`),
  ADD KEY `fk_fichajes_usuario` (`id_usuario`),
  ADD KEY `fk_fichajes_validado_por` (`validado_por`);

--
-- Indices de la tabla `novedades`
--
ALTER TABLE `novedades`
  ADD PRIMARY KEY (`id_novedad`),
  ADD KEY `fk_novedades_usuario` (`id_usuario`),
  ADD KEY `fk_novedades_registrado_por` (`registrado_por`);

--
-- Indices de la tabla `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`clave`),
  ADD KEY `idx_rl_bloqueo` (`bloqueado_hasta`),
  ADD KEY `idx_rl_ventana` (`ventana_inicio`);

--
-- Indices de la tabla `tipos_personal`
--
ALTER TABLE `tipos_personal`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Indices de la tabla `tipos_procedimiento`
--
ALTER TABLE `tipos_procedimiento`
  ADD PRIMARY KEY (`id_tipo_proc`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_usuarios_tipo_personal` (`id_tipo_personal`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alquileres`
--
ALTER TABLE `alquileres`
  MODIFY `id_alquiler` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `cirugias`
--
ALTER TABLE `cirugias`
  MODIFY `id_cirugia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `cirugia_personal`
--
ALTER TABLE `cirugia_personal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `fichajes`
--
ALTER TABLE `fichajes`
  MODIFY `id_fichaje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `novedades`
--
ALTER TABLE `novedades`
  MODIFY `id_novedad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tipos_personal`
--
ALTER TABLE `tipos_personal`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tipos_procedimiento`
--
ALTER TABLE `tipos_procedimiento`
  MODIFY `id_tipo_proc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alquileres`
--
ALTER TABLE `alquileres`
  ADD CONSTRAINT `fk_alquileres_registrado_por` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_alquileres_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `cirugias`
--
ALTER TABLE `cirugias`
  ADD CONSTRAINT `fk_cirugias_registrado_por` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_cirugias_tipo_procedimiento` FOREIGN KEY (`id_tipo_procedimiento`) REFERENCES `tipos_procedimiento` (`id_tipo_proc`);

--
-- Filtros para la tabla `cirugia_personal`
--
ALTER TABLE `cirugia_personal`
  ADD CONSTRAINT `fk_cirugia_personal_cirugia` FOREIGN KEY (`id_cirugia`) REFERENCES `cirugias` (`id_cirugia`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cirugia_personal_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `fichajes`
--
ALTER TABLE `fichajes`
  ADD CONSTRAINT `fk_fichajes_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_fichajes_validado_por` FOREIGN KEY (`validado_por`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `novedades`
--
ALTER TABLE `novedades`
  ADD CONSTRAINT `fk_novedades_registrado_por` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_novedades_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_tipo_personal` FOREIGN KEY (`id_tipo_personal`) REFERENCES `tipos_personal` (`id_tipo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
