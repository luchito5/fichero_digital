-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-09-2026 a las 22:32:39
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
(4, NULL, 'Dr. Test', 'Clinica Test', '2026-09-16', '09:00:00', '11:00:00', 2.00, 'Facturado', 1);

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
(100, '2026-09-15 11:43:29', 10, 'fichaje_salida', 'Hora: 16:43', '::1'),
(101, '2026-09-15 13:37:39', NULL, 'login_fallido', 'DNI: 31222333', '::1'),
(102, '2026-09-15 13:38:51', NULL, 'login_fallido', 'DNI: 31222333', '::1'),
(103, '2026-09-15 13:38:57', NULL, 'login_fallido', 'DNI: 31222333', '::1'),
(104, '2026-09-15 13:39:12', 1, 'login', 'Ingreso correcto', '::1'),
(105, '2026-09-15 13:44:35', 1, 'empleado_creado', 'ID 13 - Jose Lopez', '::1'),
(106, '2026-09-15 13:46:14', 1, 'empleado_baja', 'ID 7', '::1'),
(107, '2026-09-15 13:46:22', 1, 'empleado_editado', 'ID 7', '::1'),
(108, '2026-09-15 14:08:12', 1, 'configuracion_editada', '', '::1'),
(109, '2026-09-15 14:08:58', 10, 'login', 'Ingreso correcto', '::1'),
(110, '2026-09-15 14:09:10', 10, 'fichaje_entrada', 'Hora: 19:09', '::1'),
(111, '2026-09-15 14:10:10', 1, 'login', 'Ingreso correcto', '::1'),
(112, '2026-09-15 14:10:34', 1, 'procedimiento_eliminado', 'ID 6', '::1'),
(113, '2026-09-15 14:10:42', 1, 'procedimiento_eliminado', 'ID 7', '::1'),
(114, '2026-09-15 14:13:59', 1, 'cuenta_editada', 'Configuración', '::1'),
(115, '2026-09-15 14:14:54', 1, 'sesiones_invalidadas', 'Todas las sesiones', '::1'),
(116, '2026-09-15 14:20:06', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(117, '2026-09-15 14:20:14', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(118, '2026-09-15 14:20:22', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(119, '2026-09-15 14:20:33', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(120, '2026-09-15 14:20:53', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(121, '2026-09-15 14:37:19', 1, 'login', 'Ingreso correcto', '::1'),
(122, '2026-09-15 14:37:40', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(123, '2026-09-15 14:37:55', NULL, 'login_fallido', 'DNI: ariel@gmail.com', '::1'),
(124, '2026-09-15 14:38:10', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(125, '2026-09-15 14:38:22', 1, 'login', 'Ingreso correcto', '::1'),
(126, '2026-09-15 14:49:32', 1, 'empleado_estado', 'ID 7 -> inactivo', '::1'),
(127, '2026-09-15 14:49:35', 1, 'empleado_estado', 'ID 7 -> activo', '::1'),
(128, '2026-09-15 14:49:36', 1, 'empleado_estado', 'ID 7 -> inactivo', '::1'),
(129, '2026-09-15 14:49:39', 1, 'empleado_estado', 'ID 7 -> activo', '::1'),
(130, '2026-09-15 14:49:44', 1, 'empleado_estado', 'ID 7 -> inactivo', '::1'),
(131, '2026-09-15 14:49:48', 1, 'empleado_estado', 'ID 7 -> activo', '::1'),
(132, '2026-09-15 14:49:52', 1, 'empleado_estado', 'ID 7 -> inactivo', '::1'),
(133, '2026-09-15 14:50:21', 1, 'empleado_estado', 'ID 7 -> activo', '::1'),
(134, '2026-09-15 14:50:48', 1, 'empleado_editado', 'ID 7', '::1'),
(135, '2026-09-15 14:59:03', 1, 'empleado_eliminado', 'ID 12', ''),
(136, '2026-09-15 14:59:16', 1, 'empleado_estado', 'ID 5 -> inactivo', ''),
(137, '2026-09-15 15:01:34', 1, 'empleado_eliminado', 'ID 7', '::1'),
(138, '2026-09-16 08:10:23', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(139, '2026-09-16 08:10:34', 1, 'login', 'Ingreso correcto', '::1'),
(140, '2026-09-16 08:14:14', 1, 'alquiler_creado', 'Dr. Test 2', '::1'),
(141, '2026-09-16 08:16:10', 1, 'alquiler_creado', 'Dr. Test 2', '::1'),
(142, '2026-09-16 08:17:34', 1, 'alquiler_creado', 'Dr. Test 2', '::1'),
(143, '2026-09-16 08:19:12', 1, 'alquiler_creado', 'Dr. Test 2', '::1'),
(144, '2026-09-16 08:19:22', 1, 'alquiler_eliminado', 'ID 9', '::1'),
(145, '2026-09-16 08:19:24', 1, 'alquiler_eliminado', 'ID 8', '::1'),
(146, '2026-09-16 08:19:26', 1, 'alquiler_eliminado', 'ID 7', '::1'),
(147, '2026-09-16 08:36:48', 1, 'login', 'Ingreso correcto', '::1'),
(150, '2026-09-16 08:48:48', 1, 'login', 'Ingreso correcto', '::1'),
(151, '2026-09-16 08:49:40', 1, 'procedimiento_eliminado', 'ID 3', '::1'),
(152, '2026-09-16 08:50:17', 1, 'procedimiento_eliminado', 'ID 3', '::1'),
(153, '2026-09-16 08:51:33', 1, 'cuenta_editada', 'Configuración', '::1'),
(154, '2026-09-16 08:52:14', 1, 'sesiones_invalidadas', 'Todas las sesiones - 0 fichajes cerrados', '::1'),
(155, '2026-09-16 09:00:56', NULL, 'login_fallido', 'DNI: 31222333', '::1'),
(156, '2026-09-16 09:01:05', NULL, 'login_fallido', 'DNI: 31222333', '::1'),
(157, '2026-09-16 09:01:27', NULL, 'login_fallido', 'DNI: 31222333', '::1'),
(158, '2026-09-16 09:01:37', NULL, 'login_fallido', 'DNI: 31222333', '::1'),
(159, '2026-09-16 09:01:52', NULL, 'login_fallido', 'DNI: 31222333', '::1'),
(164, '2026-09-16 09:25:56', NULL, 'login_fallido', 'DNI: ariel@gmail.com', '::1'),
(165, '2026-09-16 09:26:06', NULL, 'login_fallido', 'DNI: 31222333', '::1'),
(166, '2026-09-16 09:26:45', 2, 'login', 'Ingreso correcto', '::1'),
(167, '2026-09-16 09:27:52', 2, 'fichaje_entrada', 'Hora: 14:27', '::1'),
(168, '2026-09-16 09:28:34', 1, 'login', 'Ingreso correcto', '::1'),
(169, '2026-09-16 09:29:44', 1, 'fichaje_validado', 'ID 5', '::1'),
(170, '2026-09-16 09:29:53', 1, 'fichaje_validado', 'ID 14', '::1'),
(171, '2026-09-16 09:29:54', 1, 'fichaje_validado', 'ID 17', '::1'),
(172, '2026-09-16 09:29:56', 1, 'fichaje_validado', 'ID 5', '::1'),
(173, '2026-09-16 09:30:20', 1, 'fichaje_validado', 'ID 5', '::1'),
(176, '2026-09-16 09:46:19', 1, 'alquiler_eliminado', 'ID 6', '::1'),
(177, '2026-09-16 09:46:25', 1, 'alquiler_eliminado', 'ID 5', '::1'),
(178, '2026-09-16 09:47:20', 2, 'login', 'Ingreso correcto', '::1'),
(179, '2026-09-16 09:47:28', 2, 'fichaje_salida', 'Hora: 14:47', '::1'),
(180, '2026-09-16 09:48:20', 1, 'login', 'Ingreso correcto', '::1'),
(182, '2026-09-16 09:56:08', 1, 'empleado_editado', 'ID 1', '::1'),
(183, '2026-09-16 09:56:25', 1, 'empleado_editado', 'ID 4', '::1'),
(184, '2026-09-16 09:56:46', 1, 'empleado_editado', 'ID 1', '::1'),
(185, '2026-09-16 10:01:54', 1, 'empleado_creado', 'ID 19 - Test Form', ''),
(186, '2026-09-16 10:01:54', 1, 'empleado_editado', 'ID 19', ''),
(187, '2026-09-16 10:09:41', 1, 'empleado_creado', 'ID 20 - Test Estado', ''),
(188, '2026-09-16 10:09:41', 1, 'empleado_editado', 'ID 20', ''),
(189, '2026-09-16 10:09:41', 1, 'empleado_editado', 'ID 20', ''),
(190, '2026-09-16 10:10:21', 1, 'empleado_editado', 'ID 1', '::1'),
(191, '2026-09-16 10:10:30', 1, 'empleado_estado', 'ID 2 -> inactivo', '::1'),
(192, '2026-09-16 10:10:34', 1, 'empleado_estado', 'ID 2 -> activo', '::1'),
(193, '2026-09-16 11:36:32', 1, 'login', 'Ingreso correcto', '::1'),
(194, '2026-09-16 11:43:23', 1, 'empleado_editado', 'ID 1', '::1'),
(195, '2026-09-16 11:52:24', 1, 'sesiones_invalidadas', 'Todas las sesiones - 0 fichajes cerrados', '::1'),
(196, '2026-09-16 11:57:22', 3, 'login', 'Ingreso correcto', '::1'),
(197, '2026-09-16 17:16:19', NULL, 'login_fallido', 'DNI: 31222333', '::1'),
(198, '2026-09-16 17:16:28', 2, 'login', 'Ingreso correcto', '::1'),
(199, '2026-09-16 17:18:17', 3, 'login', 'Ingreso correcto', '::1'),
(200, '2026-09-16 17:19:38', 3, 'fichaje_entrada', 'Hora: 22:19', '::1'),
(201, '2026-09-16 17:19:39', 3, 'fichaje_salida', 'Hora: 22:19', '::1'),
(202, '2026-09-16 17:21:30', 3, 'fichaje_entrada', 'Hora: 22:21', '::1'),
(203, '2026-09-16 17:21:33', 3, 'fichaje_salida', 'Hora: 22:21', '::1'),
(204, '2026-09-16 17:24:50', NULL, 'login_fallido', 'DNI: 31222333', '::1'),
(205, '2026-09-16 17:25:25', 2, 'login', 'Ingreso correcto', '::1'),
(206, '2026-09-16 17:33:16', 2, 'fichaje_entrada', 'Hora: 22:33', '::1'),
(207, '2026-09-16 17:33:25', 2, 'fichaje_salida', 'Hora: 22:33', '::1'),
(208, '2026-09-16 17:33:40', 2, 'fichaje_entrada', 'Hora: 22:33', '::1'),
(209, '2026-09-16 17:34:11', 2, 'fichaje_salida', 'Hora: 22:34', '::1'),
(210, '2026-09-16 17:34:45', 2, 'fichaje_entrada', 'Hora: 22:34', '::1'),
(211, '2026-09-16 17:34:58', 2, 'fichaje_salida', 'Hora: 22:34', '::1'),
(212, '2026-09-16 17:35:01', 2, 'fichaje_entrada', 'Hora: 22:35', '::1'),
(213, '2026-09-16 17:35:07', 2, 'fichaje_salida', 'Hora: 22:35', '::1'),
(214, '2026-09-16 17:39:38', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(215, '2026-09-16 17:40:57', 1, 'login', 'Ingreso correcto', '::1'),
(216, '2026-09-16 17:46:18', 1, 'empleado_estado', 'ID 2 -> inactivo', '::1'),
(217, '2026-09-16 17:46:24', 1, 'empleado_estado', 'ID 2 -> activo', '::1'),
(218, '2026-09-16 17:46:47', 1, 'empleado_estado', 'ID 9 -> inactivo', '::1'),
(219, '2026-09-16 17:46:54', 1, 'empleado_estado', 'ID 9 -> activo', '::1'),
(220, '2026-09-16 17:47:13', 1, 'empleado_editado', 'ID 9', '::1'),
(221, '2026-09-16 17:47:54', 1, 'empleado_editado', 'ID 9', '::1'),
(222, '2026-09-16 17:52:11', 1, 'empleado_editado', 'ID 10', '::1'),
(223, '2026-09-16 17:52:21', 1, 'empleado_editado', 'ID 6', '::1'),
(224, '2026-09-16 20:22:42', 1, 'login', 'Ingreso correcto', '::1'),
(225, '2026-09-16 20:25:08', 1, 'empleado_estado', 'ID 4 -> inactivo', '::1'),
(226, '2026-09-16 20:25:10', 1, 'empleado_estado', 'ID 4 -> activo', '::1'),
(227, '2026-09-16 20:25:11', 1, 'empleado_estado', 'ID 6 -> inactivo', '::1'),
(228, '2026-09-16 20:25:12', 1, 'empleado_estado', 'ID 6 -> activo', '::1'),
(229, '2026-09-17 16:47:42', 1, 'login', 'Ingreso correcto', '::1'),
(230, '2026-09-17 17:02:02', 1, 'fichaje_aprobado', 'ID 17 - Validado por administración', '::1'),
(231, '2026-09-17 17:02:17', 1, 'fichaje_aprobado', 'ID 25 - Validado por administración', '::1'),
(232, '2026-09-17 17:02:20', 1, 'fichaje_aprobado', 'ID 24 - Validado por administración', '::1'),
(233, '2026-09-17 17:02:20', 1, 'fichaje_aprobado', 'ID 23 - Validado por administración', '::1'),
(234, '2026-09-17 17:02:21', 1, 'fichaje_aprobado', 'ID 22 - Validado por administración', '::1'),
(235, '2026-09-17 17:02:21', 1, 'fichaje_aprobado', 'ID 21 - Validado por administración', '::1'),
(236, '2026-09-17 17:02:21', 1, 'fichaje_aprobado', 'ID 20 - Validado por administración', '::1'),
(237, '2026-09-17 17:02:21', 1, 'fichaje_aprobado', 'ID 13 - Validado por administración', '::1'),
(238, '2026-09-17 17:02:22', 1, 'fichaje_aprobado', 'ID 12 - Validado por administración', '::1'),
(239, '2026-09-17 17:06:55', 2, 'login', 'Ingreso correcto', '::1'),
(240, '2026-09-17 17:06:57', 2, 'fichaje_entrada', 'Hora: 22:06', '::1'),
(241, '2026-09-17 17:07:40', 2, 'fichaje_salida', 'Hora: 22:07', '::1'),
(242, '2026-09-17 17:09:10', 2, 'fichaje_entrada', 'Hora: 17:09', '::1'),
(243, '2026-09-17 17:09:11', 2, 'fichaje_salida', 'Hora: 17:09', '::1'),
(244, '2026-09-17 17:18:09', 3, 'login', 'Ingreso correcto', '::1'),
(245, '2026-09-17 17:18:12', 3, 'fichaje_entrada', 'Hora: 17:18', '::1'),
(246, '2026-09-17 17:22:19', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(247, '2026-09-17 17:22:41', NULL, 'login_fallido', 'DNI: 30111221', '::1'),
(248, '2026-09-17 17:23:51', NULL, 'login_fallido', 'DNI: 30111221', '::1'),
(249, '2026-09-17 17:23:57', 1, 'login', 'Ingreso correcto', '::1'),
(250, '2026-09-17 17:24:47', NULL, 'login_fallido', 'DNI: 30111221', '::1'),
(251, '2026-09-17 17:24:54', 1, 'login', 'Ingreso correcto', '::1'),
(252, '2026-09-17 17:25:03', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(253, '2026-09-17 17:25:29', 1, 'login', 'Ingreso correcto', '::1'),
(254, '2026-09-17 17:25:43', 1, 'fichaje_aprobado', 'ID 29 - Validado por administración', '::1'),
(255, '2026-09-17 17:25:45', 1, 'fichaje_aprobado', 'ID 28 - Validado por administración', '::1'),
(256, '2026-09-17 17:26:12', 1, 'fichaje_rechazado', 'ID 26 - No lo vi salir.', '::1'),
(257, '2026-09-17 17:26:24', 1, 'fichaje_aprobado', 'ID 26 - No lo vi salir.', '::1'),
(258, '2026-09-17 17:26:49', 1, 'fichaje_rechazado', 'ID 29 - Validado por administración', '::1'),
(259, '2026-09-17 17:28:47', 4, 'fichaje_a_pendiente', 'ID 1', ''),
(260, '2026-09-17 17:29:08', 1, 'fichaje_a_pendiente', 'ID 29', '::1'),
(261, '2026-09-17 17:29:35', 3, 'login', 'Ingreso correcto', '::1'),
(262, '2026-09-17 17:29:37', 3, 'fichaje_salida', 'Hora: 17:29', '::1'),
(263, '2026-09-17 17:29:55', NULL, 'login_fallido', 'DNI: 31222333', '::1'),
(264, '2026-09-17 17:30:04', 1, 'login', 'Ingreso correcto', '::1'),
(265, '2026-09-17 17:30:11', 1, 'fichaje_aprobado', 'ID 29 - Validado por administración', '::1'),
(266, '2026-09-17 17:30:46', 4, 'login', 'Ingreso correcto', '::1'),
(267, '2026-09-17 17:30:57', 4, 'fichaje_a_pendiente', 'ID 29', '::1'),
(268, '2026-09-17 17:31:17', 4, 'fichaje_aprobado', 'ID 29 - Validado por administración', '::1'),
(269, '2026-09-17 17:31:48', 3, 'login', 'Ingreso correcto', '::1'),
(270, '2026-09-17 17:31:50', 3, 'fichaje_entrada', 'Hora: 17:31', '::1');

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
(1, 'AMEMT - Fichero Digital 2', 'Las Heras 3810', '0249-004040', 'administracion@gmail.com', 0, 0, 0);

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
(5, 6, '2026-09-09', '18:48:32', NULL, NULL, 1, 1, 'Validado por administración'),
(12, 2, '2026-09-15', '11:41:43', '11:42:22', 0.01, 1, 1, 'Validado por administración'),
(13, 10, '2026-09-15', '11:42:47', '11:43:29', 0.01, 1, 1, 'Validado por administración'),
(14, 10, '2026-09-15', '14:09:10', NULL, NULL, 1, 1, 'Validado por administración'),
(17, 2, '2026-09-16', '09:27:52', '09:47:28', 0.33, 1, 1, 'Validado por administración'),
(20, 3, '2026-09-16', '17:19:38', '17:19:39', 0.00, 1, 1, 'Validado por administración'),
(21, 3, '2026-09-16', '17:21:30', '17:21:33', 0.00, 1, 1, 'Validado por administración'),
(22, 2, '2026-09-16', '17:33:16', '17:33:25', 0.00, 1, 1, 'Validado por administración'),
(23, 2, '2026-09-16', '17:33:40', '17:34:11', 0.01, 1, 1, 'Validado por administración'),
(24, 2, '2026-09-16', '17:34:45', '17:34:58', 0.00, 1, 1, 'Validado por administración'),
(25, 2, '2026-09-16', '17:35:01', '17:35:07', 0.00, 1, 1, 'Validado por administración'),
(26, 2, '2026-09-17', '17:06:57', '17:07:40', 0.01, 1, 1, 'No lo vi salir.'),
(28, 2, '2026-09-17', '17:09:10', '17:09:11', 0.00, 1, 1, 'Validado por administración'),
(29, 3, '2026-09-17', '17:18:12', '17:29:37', 0.19, 1, 4, 'Validado por administración'),
(30, 3, '2026-09-17', '17:31:50', NULL, NULL, 0, NULL, NULL);

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
('fichaje:usuario:10', 1, '2026-09-15 14:09:10', NULL),
('fichaje:usuario:2', 4, '2026-09-17 17:06:57', NULL),
('fichaje:usuario:3', 3, '2026-09-17 17:18:12', NULL),
('login:dni:30111221', 3, '2026-09-17 17:22:41', NULL),
('login:dni:31222333', 1, '2026-09-17 17:29:55', NULL),
('login:dni:ariel@gmail.com', 1, '2026-09-16 09:25:56', NULL);

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
(5, 'Mantenimiento', 'Personal encargado del mantenimiento de las instalaciones'),
(6, 'Instrumentador', 'Personal que asiste al cirujano durante los procedimientos');

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
(4, 'Cirugía ambulatoria', 1),
(5, 'Consulta prequirúrgica', 1);

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
(1, 'Natalia', 'Diaz', '30111222', 'Administrativo', 4, 'Admin', '30111222', '$2y$10$/vaYdyxeQuk4yYOC0wp0beB0zbaa3H3S.HF3qq2MGK3yts/M6zZ.6', 1, 1, '5183350b7a50f9c90f3a3b9945f5ac18e3bd02f0ba014643993623c3bb9d6171', '2026-09-09 21:21:39'),
(2, 'María', 'Gómez', '31222333', 'Anestesista', 2, 'Por Hora', 'mgomez', '$2y$10$KJIhpy5iTkQ.L/Ox31.p.eV1yDneQXOG/v.bgZkeI9TOgRjCh6WV.', 0, 1, 'c17ab904b89e77ff6c082a723cf025d94ef1f37320eea97b3e317f89a99c5816', '2026-09-09 21:21:39'),
(3, 'Carlos', 'Rodríguez', '32333444', 'Enfermero', 3, 'Fijo', 'crodriguez', '$2y$10$oLykXIDMqYjKIURNEcO3SOpa9MdVmD013BFzQL4RKVSzGWM6Cy8Oq', 0, 1, '4af35b3c5db5d9e70aea7ac8d2effc26058f944378ce42ede300748e122a8b73', '2026-09-09 21:21:39'),
(4, 'Lucía', 'Fernández', '33444555', 'Administrativo', 4, 'Admin', 'lfernandez', '$2y$10$oLykXIDMqYjKIURNEcO3SOpa9MdVmD013BFzQL4RKVSzGWM6Cy8Oq', 1, 1, '62944384ce656497da0f794b607b1891a1d45140113845dc071546bb41ce5986', '2026-09-09 21:21:39'),
(5, 'Pedro', 'Martínez', '34555666', 'Mantenimiento', 5, 'Alquiler', 'pmartinez', '$2y$10$oLykXIDMqYjKIURNEcO3SOpa9MdVmD013BFzQL4RKVSzGWM6Cy8Oq', 0, 1, NULL, '2026-09-09 21:21:39'),
(6, 'Nicolas', 'Fortunato', '48228401', 'Cirujano', 1, 'Por Hora', '48228401', '$2y$10$oLykXIDMqYjKIURNEcO3SOpa9MdVmD013BFzQL4RKVSzGWM6Cy8Oq', 0, 1, NULL, '2026-09-09 21:45:10'),
(8, 'Quique', 'Laloz', '18999000', 'Instrumentador', 6, 'Alquiler', '18999000', '$2y$10$6plrgFXTOP6BR7yGMs0PQ.JDvD8riAzm3npfirdd4TrVLkWFT5d2K', 0, 1, NULL, '2026-09-09 22:31:13'),
(9, 'Lucrecia', 'Gutierrez', '30300300', 'Instrumentador', 6, 'Por Cirugía', '30300300', '$2y$10$oLykXIDMqYjKIURNEcO3SOpa9MdVmD013BFzQL4RKVSzGWM6Cy8Oq', 0, 1, NULL, '2026-09-09 22:38:54'),
(10, 'Ricardo', 'Gómez', '40400400', 'Cirujano', 1, 'Por Cirugía', '40400400', '$2y$10$oLykXIDMqYjKIURNEcO3SOpa9MdVmD013BFzQL4RKVSzGWM6Cy8Oq', 0, 1, NULL, '2026-09-09 22:39:21'),
(12, 'Leandro', 'Lopez', '40600700', 'Cirujano', 1, 'Fijo', '40600700', '/ZoAZugbOZtq.ke/v9guPonbPMp4NbQaHtqzBiK1XKESE/Mzrze', 0, 1, NULL, '2026-09-15 17:18:22'),
(13, 'Jose', 'Lopez', '44839066', 'Instrumentador', 6, 'Fijo', '44839066', '$2y$10$8DFgaX/qbKy3mejHqINUhOeBUQRDVGr80RqqZr53JCWA3.czvXeYq', 0, 1, NULL, '2026-09-15 16:44:35');

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
  MODIFY `id_alquiler` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;

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
  MODIFY `id_fichaje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `novedades`
--
ALTER TABLE `novedades`
  MODIFY `id_novedad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tipos_personal`
--
ALTER TABLE `tipos_personal`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tipos_procedimiento`
--
ALTER TABLE `tipos_procedimiento`
  MODIFY `id_tipo_proc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
