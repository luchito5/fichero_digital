-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-10-2026 a las 16:19:31
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
  `hora_salida_confirmada` tinyint(1) NOT NULL DEFAULT 0,
  `horas_uso` decimal(10,2) DEFAULT NULL,
  `dato_facturacion` text DEFAULT NULL,
  `registrado_por` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(270, '2026-09-17 17:31:50', 3, 'fichaje_entrada', 'Hora: 17:31', '::1'),
(271, '2026-09-18 08:19:10', 1, 'login', 'Ingreso correcto', '::1'),
(272, '2026-09-18 08:19:51', 1, 'fichaje_validado', 'ID 30', '::1'),
(273, '2026-09-18 08:20:09', 1, 'fichaje_a_pendiente', 'ID 30', '::1'),
(274, '2026-09-18 08:47:14', 1, 'fichaje_corregido', 'ID 30', '::1'),
(275, '2026-09-18 08:48:05', 2, 'login', 'Ingreso correcto', '::1'),
(276, '2026-09-18 08:48:10', 2, 'fichaje_entrada', 'Hora: 08:48', '::1'),
(277, '2026-09-18 08:48:52', 2, 'fichaje_salida', 'Hora: 08:48', '::1'),
(278, '2026-09-18 08:49:00', 2, 'fichaje_entrada', 'Hora: 08:49', '::1'),
(279, '2026-09-18 08:49:15', 1, 'login', 'Ingreso correcto', '::1'),
(280, '2026-09-18 08:49:48', 1, 'fichaje_aprobado', 'ID 31 - Validado por administración', '::1'),
(281, '2026-09-18 08:52:58', 2, 'login', 'Ingreso correcto', '::1'),
(282, '2026-09-18 08:53:00', 2, 'fichaje_salida', 'Hora: 08:53', '::1'),
(283, '2026-09-18 08:53:54', 1, 'login', 'Ingreso correcto', '::1'),
(284, '2026-09-18 08:54:04', 1, 'fichaje_aprobado', 'ID 32 - Validado por administración', '::1'),
(285, '2026-09-18 09:33:04', 2, 'login', 'Ingreso correcto', '::1'),
(286, '2026-09-18 19:37:16', NULL, 'login_fallido', 'DNI: 31222333', '::1'),
(287, '2026-09-18 19:37:26', 1, 'login', 'Ingreso correcto', '::1'),
(288, '2026-09-18 19:40:17', 2, 'login', 'Ingreso correcto', '::1'),
(289, '2026-09-21 12:49:53', 1, 'login', 'Ingreso correcto', '::1'),
(290, '2026-09-21 13:03:32', 1, 'fichaje_a_pendiente', 'ID 30', '::1'),
(291, '2026-09-21 13:03:38', 1, 'fichaje_aprobado', 'ID 30 - Validado por administración', '::1'),
(292, '2026-09-26 12:44:52', 1, 'login', 'Ingreso correcto', '::1'),
(293, '2026-09-26 12:58:01', NULL, 'novedades_purgadas', '3 novedad(es) con fecha hasta vencida', ''),
(294, '2026-09-26 13:29:13', 1, 'novedades_purgadas', '3 novedad(es) con fecha hasta vencida', '::1'),
(295, '2026-09-26 13:36:25', 2, 'login', 'Ingreso correcto', '::1'),
(304, '2026-09-26 13:46:00', 1, 'login', 'Ingreso correcto', '::1'),
(305, '2026-09-26 13:46:22', 1, 'cirugia_actualizada', 'ID 3', '::1'),
(306, '2026-09-26 13:46:31', 1, 'cirugia_actualizada', 'ID 3', '::1'),
(307, '2026-09-26 13:46:43', 1, 'novedad_actualizada', 'ID 5', '::1'),
(308, '2026-09-26 13:48:24', 1, 'alquiler_actualizado', 'ID 4', '::1'),
(316, '2026-09-26 14:01:18', 1, 'alquiler_actualizado', 'ID 4', '::1'),
(317, '2026-09-26 14:01:29', 1, 'alquiler_salida_confirmada', 'ID 4 - 11:30', '::1'),
(318, '2026-09-26 14:01:33', 1, 'alquiler_salida_confirmada', 'ID 4 - 11:30', '::1'),
(319, '2026-09-26 14:03:05', 1, 'alquiler_salida_confirmada', 'ID 4 - 11:30', '::1'),
(320, '2026-09-26 14:30:43', 1, 'login', 'Ingreso correcto', '::1'),
(321, '2026-09-26 14:30:58', 1, 'empleado_editado', 'ID 4', '::1'),
(322, '2026-09-28 10:47:25', 1, 'login', 'Ingreso correcto', '::1'),
(323, '2026-10-01 18:57:47', 2, 'login', 'Ingreso correcto', '::1'),
(324, '2026-10-01 18:57:52', 2, 'fichaje_entrada', 'Hora: 18:57', '::1'),
(325, '2026-10-01 18:58:32', 1, 'login', 'Ingreso correcto', '::1'),
(326, '2026-10-01 18:59:35', 2, 'login', 'Ingreso correcto', '::1'),
(327, '2026-10-01 19:00:37', NULL, 'login_fallido', 'DNI: 31222333', '::1'),
(328, '2026-10-01 19:00:41', NULL, 'login_fallido', 'DNI: 31222333', '::1'),
(329, '2026-10-01 19:00:47', 1, 'login', 'Ingreso correcto', '::1'),
(330, '2026-10-01 19:02:12', 1, 'fichaje_validado', 'ID 33', '::1'),
(331, '2026-10-01 20:32:48', 1, 'empleado_editado', 'ID 5', '::1'),
(332, '2026-10-01 20:39:53', 1, 'empleado_eliminado', 'ID 5', '::1'),
(333, '2026-10-01 20:47:02', 1, 'empleado_eliminado', 'ID 4', '::1'),
(334, '2026-10-01 20:47:05', 1, 'empleado_eliminado', 'ID 6', '::1'),
(335, '2026-10-01 20:47:07', 1, 'empleado_eliminado', 'ID 2', '::1'),
(336, '2026-10-01 20:47:13', 1, 'empleado_eliminado', 'ID 10', '::1'),
(337, '2026-10-01 20:47:14', 1, 'empleado_eliminado', 'ID 9', '::1'),
(338, '2026-10-01 20:47:16', 1, 'empleado_eliminado', 'ID 8', '::1'),
(339, '2026-10-01 20:47:18', 1, 'empleado_eliminado', 'ID 13', '::1'),
(340, '2026-10-01 20:47:19', 1, 'empleado_eliminado', 'ID 12', '::1'),
(341, '2026-10-01 20:47:21', 1, 'empleado_eliminado', 'ID 3', '::1'),
(342, '2026-10-01 20:48:53', 1, 'alquiler_eliminado', 'ID 4', '::1'),
(343, '2026-10-01 20:49:07', 1, 'procedimiento_eliminado', 'ID 5', '::1'),
(344, '2026-10-01 20:49:13', 1, 'procedimiento_eliminado', 'ID 5', '::1'),
(345, '2026-10-01 21:16:40', 1, 'procedimiento_creado', 'Cirugia corazon', '::1'),
(346, '2026-10-01 21:23:48', 1, 'empleado_creado', 'ID 21 - Morena Morinigo', '::1'),
(347, '2026-10-01 21:25:33', 21, 'login', 'Ingreso correcto', '::1'),
(348, '2026-10-01 21:25:36', 21, 'fichaje_entrada', 'Hora: 21:25', '::1'),
(349, '2026-10-01 21:28:08', 1, 'login', 'Ingreso correcto', '::1'),
(350, '2026-10-01 21:36:24', 1, 'fichaje_aprobado', 'ID 34 - Validado por administración', '::1'),
(351, '2026-10-01 21:37:00', 1, 'empleado_estado', 'ID 21 -> inactivo', '::1'),
(352, '2026-10-01 21:37:04', 1, 'empleado_estado', 'ID 21 -> activo', '::1'),
(353, '2026-10-01 21:37:12', 1, 'empleado_estado', 'ID 21 -> inactivo', '::1'),
(354, '2026-10-01 21:37:24', NULL, 'login_fallido', 'DNI: 30400500', '::1'),
(355, '2026-10-01 21:37:36', 1, 'login', 'Ingreso correcto', '::1'),
(356, '2026-10-01 21:37:39', 1, 'empleado_estado', 'ID 21 -> activo', '::1'),
(357, '2026-10-01 21:37:44', 1, 'empleado_estado', 'ID 21 -> inactivo', '::1'),
(358, '2026-10-01 21:38:12', 1, 'empleado_estado', 'ID 21 -> activo', '::1'),
(359, '2026-10-02 07:48:10', 1, 'login', 'Ingreso correcto', '::1'),
(360, '2026-10-02 07:50:22', 21, 'login', 'Ingreso correcto', '::1'),
(361, '2026-10-02 07:50:31', 21, 'fichaje_entrada', 'Hora: 07:50', '::1'),
(362, '2026-10-02 07:52:05', 21, 'fichaje_salida', 'Hora: 07:52', '::1'),
(363, '2026-10-02 07:52:09', 21, 'fichaje_entrada', 'Hora: 07:52', '::1'),
(364, '2026-10-02 07:52:11', 21, 'fichaje_salida', 'Hora: 07:52', '::1'),
(365, '2026-10-02 07:53:11', 1, 'login', 'Ingreso correcto', '::1'),
(366, '2026-10-02 08:06:08', 1, 'empleado_creado', 'ID 22 - Fabiana Nesci', '::1'),
(367, '2026-10-02 08:21:50', 21, 'login', 'Ingreso correcto', '::1'),
(368, '2026-10-02 08:22:12', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(369, '2026-10-02 08:22:17', 1, 'login', 'Ingreso correcto', '::1'),
(370, '2026-10-02 08:41:04', 1, 'empleado_eliminado', 'ID 22', '::1'),
(371, '2026-10-02 08:42:09', 1, 'cirugia_creada', 'ID 6', '::1'),
(372, '2026-10-02 08:42:13', 1, 'cirugia_eliminada', 'ID 6', '::1'),
(373, '2026-10-02 08:42:25', 1, 'novedad_creada', 'Vacaciones', '::1'),
(374, '2026-10-02 08:42:28', 1, 'novedad_eliminada', 'ID 6', '::1'),
(375, '2026-10-02 08:42:52', 1, 'alquiler_creado', 'Dr test', '::1'),
(376, '2026-10-02 08:42:58', 1, 'alquiler_salida_confirmada', 'ID 11 - 11:30', '::1'),
(377, '2026-10-02 08:43:04', 1, 'alquiler_eliminado', 'ID 11', '::1'),
(378, '2026-10-02 08:43:15', 1, 'fichaje_rechazado', 'ID 36 - .', '::1'),
(388, '2026-10-02 09:00:28', 1, 'procedimiento_eliminado', 'ID 9', '::1'),
(389, '2026-10-02 09:00:42', 1, 'procedimiento_creado', 'Cirugia Corazom', '::1'),
(390, '2026-10-02 09:00:47', 1, 'procedimiento_eliminado', 'ID 13', '::1'),
(391, '2026-10-02 09:01:14', 21, 'login', 'Ingreso correcto', '::1'),
(392, '2026-10-02 09:01:15', 21, 'fichaje_entrada', 'Hora: 09:01', '::1'),
(393, '2026-10-02 09:01:28', 1, 'login', 'Ingreso correcto', '::1'),
(398, '2026-10-02 09:11:33', 1, 'login', 'Ingreso correcto', '::1'),
(399, '2026-10-02 09:11:39', 1, 'sesiones_invalidadas', 'Todas las sesiones - 1 fichajes cerrados', '::1'),
(400, '2026-10-02 09:24:19', 21, 'login', 'Ingreso correcto', '::1'),
(401, '2026-10-02 09:24:20', 21, 'fichaje_entrada', 'Hora: 09:24', '::1'),
(402, '2026-10-02 09:24:22', 21, 'fichaje_salida', 'Hora: 09:24', '::1'),
(403, '2026-10-02 09:24:23', 21, 'fichaje_entrada', 'Hora: 09:24', '::1'),
(404, '2026-10-02 09:24:24', 21, 'fichaje_salida', 'Hora: 09:24', '::1'),
(405, '2026-10-02 09:24:25', 21, 'fichaje_entrada', 'Hora: 09:24', '::1'),
(406, '2026-10-02 09:24:27', 21, 'fichaje_salida', 'Hora: 09:24', '::1'),
(407, '2026-10-02 09:24:28', 21, 'fichaje_entrada', 'Hora: 09:24', '::1'),
(408, '2026-10-02 09:24:29', 21, 'fichaje_salida', 'Hora: 09:24', '::1'),
(409, '2026-10-02 09:24:30', 21, 'fichaje_entrada', 'Hora: 09:24', '::1'),
(410, '2026-10-02 09:24:47', 1, 'login', 'Ingreso correcto', '::1'),
(412, '2026-10-02 09:30:50', 1, 'fichaje_validado', 'ID 42', '::1'),
(414, '2026-10-02 09:34:13', 1, 'procedimiento_eliminado', 'ID 1', '::1'),
(415, '2026-10-02 09:34:20', 1, 'procedimiento_creado', 'Cirugía general', '::1'),
(416, '2026-10-02 09:35:16', 1, 'fichaje_aprobado', 'ID 41 - Validado por administración', '::1'),
(417, '2026-10-02 09:35:17', 1, 'fichaje_aprobado', 'ID 40 - Validado por administración', '::1'),
(418, '2026-10-02 09:35:18', 1, 'fichaje_aprobado', 'ID 39 - Validado por administración', '::1'),
(419, '2026-10-02 09:35:18', 1, 'fichaje_aprobado', 'ID 38 - Validado por administración', '::1'),
(420, '2026-10-02 09:35:18', 1, 'fichaje_aprobado', 'ID 37 - Validado por administración', '::1'),
(421, '2026-10-02 09:35:20', 1, 'fichaje_aprobado', 'ID 35 - Validado por administración', '::1'),
(422, '2026-10-02 09:35:50', 1, 'sesiones_invalidadas', 'Todas las sesiones - 1 fichajes cerrados', '::1'),
(423, '2026-10-02 09:36:13', 21, 'login', 'Ingreso correcto', '::1'),
(432, '2026-10-02 09:48:39', 1, 'login', 'Ingreso correcto', '::1'),
(433, '2026-10-02 09:48:45', NULL, 'login_fallido', 'DNI: 33', '::1'),
(434, '2026-10-02 09:48:49', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(435, '2026-10-02 09:48:50', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(436, '2026-10-02 09:48:51', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(437, '2026-10-02 09:48:52', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(438, '2026-10-02 09:51:58', 1, 'login', 'Ingreso correcto', '::1'),
(439, '2026-10-02 09:52:15', 21, 'login', 'Ingreso correcto', '::1'),
(440, '2026-10-02 09:52:17', 21, 'fichaje_entrada', 'Hora: 09:52', '::1'),
(441, '2026-10-02 09:52:19', 21, 'fichaje_salida', 'Hora: 09:52', '::1'),
(442, '2026-10-02 09:52:20', 21, 'fichaje_entrada', 'Hora: 09:52', '::1'),
(443, '2026-10-02 09:52:21', 21, 'fichaje_salida', 'Hora: 09:52', '::1'),
(444, '2026-10-02 09:52:23', 21, 'fichaje_entrada', 'Hora: 09:52', '::1'),
(445, '2026-10-02 09:52:24', 21, 'fichaje_salida', 'Hora: 09:52', '::1'),
(446, '2026-10-02 09:52:25', 21, 'fichaje_entrada', 'Hora: 09:52', '::1'),
(447, '2026-10-02 09:52:26', 21, 'fichaje_salida', 'Hora: 09:52', '::1'),
(448, '2026-10-02 09:52:27', 21, 'fichaje_entrada', 'Hora: 09:52', '::1'),
(449, '2026-10-02 09:52:28', 21, 'fichaje_salida', 'Hora: 09:52', '::1'),
(450, '2026-10-02 09:55:14', 21, 'login', 'Ingreso correcto', '::1'),
(451, '2026-10-02 09:56:03', 21, 'fichaje_entrada', 'Hora: 09:56', '::1'),
(452, '2026-10-02 09:56:06', 21, 'fichaje_salida', 'Hora: 09:56', '::1'),
(453, '2026-10-02 09:56:47', 1, 'login', 'Ingreso correcto', '::1'),
(454, '2026-10-02 09:57:33', 21, 'login', 'Ingreso correcto', '::1'),
(455, '2026-10-02 10:12:55', 1, 'login', 'Ingreso correcto', '::1'),
(456, '2026-10-02 10:14:06', 1, 'empleado_editado', 'ID 1', '::1'),
(457, '2026-10-02 10:14:52', 1, 'login', 'Ingreso correcto', '::1'),
(458, '2026-10-02 10:15:06', 1, 'cuenta_editada', 'Configuración', '::1'),
(459, '2026-10-02 10:15:08', 1, 'cuenta_editada', 'Configuración', '::1'),
(460, '2026-10-02 10:15:16', 1, 'empleado_estado', 'ID 21 -> inactivo', '::1'),
(461, '2026-10-02 10:15:17', 1, 'empleado_estado', 'ID 21 -> activo', '::1'),
(462, '2026-10-02 10:18:23', 21, 'login', 'Ingreso correcto', '::1'),
(463, '2026-10-02 10:20:03', 21, 'fichaje_entrada', 'Hora: 10:20', '::1'),
(464, '2026-10-02 10:20:21', 21, 'fichaje_salida', 'Hora: 10:20', '::1'),
(465, '2026-10-02 10:32:42', 1, 'login', 'Ingreso correcto', '::1'),
(466, '2026-10-02 10:33:40', 1, 'empleado_editado', 'ID 21', '::1'),
(467, '2026-10-02 10:43:47', 21, 'login', 'Ingreso correcto', '::1'),
(468, '2026-10-02 10:47:20', 1, 'login', 'Ingreso correcto', '::1'),
(469, '2026-10-02 10:55:47', 1, 'cuenta_editada', 'Configuración', '::1'),
(470, '2026-10-02 11:52:50', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(471, '2026-10-02 11:52:53', 1, 'login', 'Ingreso correcto', '::1'),
(472, '2026-10-02 12:01:11', 1, 'empleado_creado', 'ID 32 - test test', '::1'),
(473, '2026-10-02 12:01:21', 1, 'empleado_eliminado', 'ID 32', '::1'),
(474, '2026-10-02 12:01:45', 1, 'alquiler_creado', 'test', '::1'),
(475, '2026-10-02 12:01:47', 1, 'alquiler_salida_confirmada', 'ID 12 - 10:30', '::1'),
(476, '2026-10-02 12:01:51', 1, 'alquiler_eliminado', 'ID 12', '::1'),
(477, '2026-10-02 12:03:52', 1, 'procedimiento_eliminado', 'ID 14', '::1'),
(478, '2026-10-02 12:03:58', 1, 'procedimiento_eliminado', 'ID 14', '::1'),
(479, '2026-10-02 12:07:19', 1, 'empleado_estado', 'ID 21 -> inactivo', '::1'),
(480, '2026-10-02 12:07:20', 1, 'empleado_estado', 'ID 21 -> activo', '::1'),
(481, '2026-10-02 12:25:16', 1, 'procedimiento_creado', 'Cirugía general', '::1'),
(482, '2026-10-02 12:25:29', 1, 'procedimiento_eliminado', 'ID 15', '::1'),
(483, '2026-10-02 12:25:31', 1, 'procedimiento_eliminado', 'ID 15', '::1'),
(484, '2026-10-02 12:25:40', 1, 'procedimiento_creado', 'Cirugía general', '::1'),
(485, '2026-10-02 12:26:00', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(486, '2026-10-02 12:26:01', 1, 'fichaje_aprobado', 'ID 48 - Validado por administración', '::1'),
(487, '2026-10-02 12:26:02', 1, 'fichaje_aprobado', 'ID 47 - Validado por administración', '::1'),
(488, '2026-10-02 12:26:02', 1, 'fichaje_aprobado', 'ID 46 - Validado por administración', '::1'),
(489, '2026-10-02 12:26:03', 1, 'fichaje_aprobado', 'ID 45 - Validado por administración', '::1'),
(490, '2026-10-02 12:26:04', 1, 'fichaje_aprobado', 'ID 44 - Validado por administración', '::1'),
(491, '2026-10-02 12:26:07', 1, 'fichaje_rechazado', 'ID 43 - .', '::1'),
(492, '2026-10-02 12:26:43', 1, 'sesiones_invalidadas', 'Todas las sesiones - 0 fichajes cerrados', '::1'),
(493, '2026-10-02 12:43:32', 1, 'sesiones_invalidadas', 'Todas las sesiones - 0 fichajes cerrados', '::1'),
(494, '2026-10-02 12:43:47', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(495, '2026-10-02 12:43:49', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(496, '2026-10-02 12:43:50', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(497, '2026-10-02 12:43:50', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(498, '2026-10-02 12:43:50', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(499, '2026-10-02 12:43:50', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(500, '2026-10-02 12:43:51', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(501, '2026-10-02 12:43:51', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(502, '2026-10-02 12:43:51', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(503, '2026-10-02 12:43:51', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(504, '2026-10-02 12:43:51', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(505, '2026-10-02 12:43:52', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(506, '2026-10-02 12:43:52', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(507, '2026-10-02 12:43:52', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(508, '2026-10-02 12:43:52', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(509, '2026-10-02 12:43:52', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(510, '2026-10-02 12:43:53', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(511, '2026-10-02 12:43:53', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(512, '2026-10-02 12:43:53', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(513, '2026-10-02 12:43:53', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(514, '2026-10-02 12:43:54', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(515, '2026-10-02 12:43:54', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(516, '2026-10-02 12:43:54', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(517, '2026-10-02 12:43:54', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(518, '2026-10-02 12:43:54', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(519, '2026-10-02 12:43:55', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(520, '2026-10-02 12:43:55', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(521, '2026-10-02 12:43:55', 1, 'fichaje_aprobado', 'ID 49 - Validado por administración', '::1'),
(522, '2026-10-02 12:45:03', 21, 'login', 'Ingreso correcto', '::1'),
(523, '2026-10-02 12:47:50', 21, 'fichaje_entrada', 'Hora: 12:47', '::1'),
(524, '2026-10-02 12:47:51', 21, 'fichaje_salida', 'Hora: 12:47', '::1'),
(525, '2026-10-02 12:47:52', 21, 'fichaje_entrada', 'Hora: 12:47', '::1'),
(526, '2026-10-02 12:47:53', 21, 'fichaje_salida', 'Hora: 12:47', '::1'),
(527, '2026-10-02 12:47:54', 21, 'fichaje_entrada', 'Hora: 12:47', '::1'),
(528, '2026-10-02 12:47:55', 21, 'fichaje_salida', 'Hora: 12:47', '::1'),
(529, '2026-10-02 12:47:56', 21, 'fichaje_entrada', 'Hora: 12:47', '::1'),
(530, '2026-10-02 12:47:57', 21, 'fichaje_salida', 'Hora: 12:47', '::1'),
(531, '2026-10-02 12:47:58', 21, 'fichaje_entrada', 'Hora: 12:47', '::1'),
(532, '2026-10-02 12:47:59', 21, 'fichaje_salida', 'Hora: 12:47', '::1'),
(533, '2026-10-02 13:03:46', 1, 'login', 'Ingreso correcto', '::1'),
(534, '2026-10-02 13:08:12', 1, 'procedimiento_eliminado', 'ID 16', '::1'),
(535, '2026-10-02 13:08:14', 1, 'procedimiento_eliminado', 'ID 16', '::1'),
(536, '2026-10-02 13:08:21', 1, 'procedimiento_creado', 'Cirugía general', '::1'),
(537, '2026-10-03 08:46:39', 1, 'login', 'Ingreso correcto', '::1'),
(538, '2026-10-03 08:54:20', 1, 'procedimiento_eliminado', 'ID 17', '::1'),
(539, '2026-10-03 08:59:31', 1, 'procedimiento_creado', 'Cirugía corazon', '::1'),
(540, '2026-10-03 08:59:34', 1, 'procedimiento_eliminado', 'ID 18', '::1'),
(541, '2026-10-03 08:59:44', 1, 'procedimiento_creado', 'Cirugía general', '::1'),
(542, '2026-10-03 10:16:10', 1, 'cirugia_creada', 'ID 8', '::1'),
(543, '2026-10-03 10:16:19', 1, 'cirugia_eliminada', 'ID 8', '::1'),
(544, '2026-10-03 10:36:02', 1, 'novedad_creada', 'Vacaciones', '::1'),
(545, '2026-10-03 10:36:08', 1, 'novedad_creada', 'Licencia', '::1'),
(546, '2026-10-03 10:36:11', 1, 'novedad_creada', 'ART', '::1'),
(547, '2026-10-03 10:36:13', 1, 'novedad_creada', 'Capacitación', '::1'),
(548, '2026-10-03 11:11:10', 1, 'novedad_creada', 'Licencia', '::1'),
(549, '2026-10-03 11:13:31', 1, 'fichaje_aprobado', 'ID 54 - Validado por administración', '::1'),
(550, '2026-10-03 11:13:32', 1, 'fichaje_aprobado', 'ID 53 - Validado por administración', '::1'),
(551, '2026-10-03 11:13:33', 1, 'fichaje_aprobado', 'ID 52 - Validado por administración', '::1'),
(552, '2026-10-03 11:13:33', 1, 'fichaje_aprobado', 'ID 51 - Validado por administración', '::1'),
(553, '2026-10-03 11:13:36', 1, 'fichaje_aprobado', 'ID 50 - Validado por administración', '::1'),
(554, '2026-10-03 12:27:38', 21, 'login', 'Ingreso correcto', '::1'),
(555, '2026-10-03 12:28:50', 21, 'fichaje_entrada', 'Hora: 12:28', '::1'),
(556, '2026-10-03 12:28:52', 21, 'fichaje_salida', 'Hora: 12:28', '::1'),
(557, '2026-10-03 12:28:54', 21, 'fichaje_entrada', 'Hora: 12:28', '::1'),
(558, '2026-10-03 12:28:57', 21, 'fichaje_salida', 'Hora: 12:28', '::1'),
(559, '2026-10-03 12:29:55', 21, 'login', 'Ingreso correcto', '::1'),
(560, '2026-10-03 12:37:54', 21, 'login', 'Ingreso correcto', '::1'),
(561, '2026-10-03 12:38:01', 21, 'fichaje_entrada', 'Hora: 12:38', '::1'),
(562, '2026-10-03 12:38:03', 21, 'fichaje_salida', 'Hora: 12:38', '::1'),
(563, '2026-10-03 12:38:06', 21, 'fichaje_entrada', 'Hora: 12:38', '::1'),
(564, '2026-10-03 12:38:08', 21, 'fichaje_salida', 'Hora: 12:38', '::1'),
(565, '2026-10-03 12:43:43', NULL, 'empleado_creado', 'ID 33 - Prueba Temporal', ''),
(566, '2026-10-03 12:43:43', NULL, 'empleado_creado', 'ID 34 - Prueba Temporal', ''),
(567, '2026-10-03 12:43:43', NULL, 'empleado_editado', 'ID 21', ''),
(568, '2026-10-03 12:48:41', 1, 'login', 'Ingreso correcto', '::1'),
(569, '2026-10-03 12:51:46', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(570, '2026-10-03 12:51:47', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(571, '2026-10-03 12:51:49', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(572, '2026-10-03 12:51:50', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(573, '2026-10-03 12:51:50', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(574, '2026-10-03 13:06:01', 1, 'login', 'Ingreso correcto', '::1'),
(575, '2026-10-03 13:06:09', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(576, '2026-10-03 13:06:10', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(577, '2026-10-03 13:06:12', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(578, '2026-10-03 13:06:13', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(579, '2026-10-03 13:06:14', NULL, 'login_fallido', 'DNI: 30111222', '::1'),
(580, '2026-10-03 13:24:14', 1, 'login', 'Ingreso correcto', '::1'),
(581, '2026-10-03 13:24:52', 1, 'sesiones_invalidadas', 'Todas las sesiones - 0 fichajes cerrados', '::1'),
(582, '2026-10-03 13:25:01', 1, 'fichaje_aprobado', 'ID 58 - Validado por administración', '::1'),
(583, '2026-10-03 13:25:02', 1, 'fichaje_aprobado', 'ID 57 - Validado por administración', '::1'),
(584, '2026-10-03 13:25:03', 1, 'fichaje_aprobado', 'ID 56 - Validado por administración', '::1'),
(585, '2026-10-03 13:25:03', 1, 'fichaje_aprobado', 'ID 55 - Validado por administración', '::1'),
(586, '2026-10-03 13:25:48', 21, 'login', 'Ingreso correcto', '::1'),
(587, '2026-10-03 13:25:51', 21, 'fichaje_entrada', 'Hora: 13:25', '::1'),
(588, '2026-10-05 10:18:49', 1, 'login', 'Ingreso correcto', '::1'),
(589, '2026-10-05 10:22:19', 1, 'empleado_creado', 'ID 35 - Belene Betanso', '::1'),
(590, '2026-10-05 10:22:58', 1, 'empleado_creado', 'ID 36 - Fabiana Nesci', '::1'),
(591, '2026-10-05 10:23:33', 1, 'empleado_creado', 'ID 37 - Ana Begbeder', '::1'),
(592, '2026-10-05 10:24:56', 1, 'empleado_creado', 'ID 38 - Brenda Conforti', '::1'),
(593, '2026-10-05 10:25:27', 1, 'empleado_creado', 'ID 39 - Florencia Aranaga', '::1'),
(594, '2026-10-05 10:26:15', 1, 'empleado_creado', 'ID 40 - Agustina Seco', '::1'),
(595, '2026-10-05 10:26:44', 1, 'empleado_creado', 'ID 41 - Paula Santellan', '::1'),
(596, '2026-10-05 10:27:25', 1, 'empleado_creado', 'ID 42 - Lorena Viola', '::1'),
(597, '2026-10-05 10:27:57', 1, 'empleado_creado', 'ID 43 - test test', '::1'),
(598, '2026-10-05 10:28:47', 1, 'fichaje_entrada_corregida', 'ID 59', '::1'),
(599, '2026-10-05 10:28:58', 1, 'fichaje_rechazado', 'ID 59 - Validado por administración', '::1'),
(600, '2026-10-05 10:29:53', 43, 'login', 'Ingreso correcto', '::1'),
(601, '2026-10-05 10:30:00', 43, 'fichaje_entrada', 'Hora: 10:30', '::1'),
(602, '2026-10-05 10:32:16', 1, 'login', 'Ingreso correcto', '::1'),
(603, '2026-10-05 10:32:57', NULL, 'login_fallido', 'DNI: 11111222', '::1'),
(604, '2026-10-05 10:33:00', 43, 'login', 'Ingreso correcto', '::1'),
(605, '2026-10-05 10:37:42', 43, 'fichaje_salida', 'Hora: 10:37', '::1'),
(606, '2026-10-05 10:37:44', 43, 'fichaje_entrada', 'Hora: 10:37', '::1'),
(607, '2026-10-05 10:37:46', 43, 'fichaje_salida', 'Hora: 10:37', '::1'),
(608, '2026-10-05 10:37:48', 43, 'fichaje_entrada', 'Hora: 10:37', '::1'),
(609, '2026-10-05 10:37:49', 43, 'fichaje_salida', 'Hora: 10:37', '::1'),
(610, '2026-10-05 10:37:51', 43, 'fichaje_entrada', 'Hora: 10:37', '::1'),
(611, '2026-10-05 10:37:53', 43, 'fichaje_salida', 'Hora: 10:37', '::1'),
(612, '2026-10-05 10:37:54', 43, 'fichaje_entrada', 'Hora: 10:37', '::1'),
(613, '2026-10-05 10:37:56', 43, 'fichaje_salida', 'Hora: 10:37', '::1'),
(614, '2026-10-05 10:41:24', 43, 'fichaje_entrada', 'Hora: 10:41', '::1'),
(615, '2026-10-05 10:41:50', 43, 'fichaje_salida', 'Hora: 10:41', '::1'),
(616, '2026-10-05 10:41:52', 43, 'fichaje_entrada', 'Hora: 10:41', '::1'),
(617, '2026-10-05 10:41:53', 43, 'fichaje_salida', 'Hora: 10:41', '::1'),
(618, '2026-10-05 10:41:54', 43, 'fichaje_entrada', 'Hora: 10:41', '::1'),
(619, '2026-10-05 10:47:35', 43, 'fichaje_salida', 'Hora: 10:47', '::1'),
(620, '2026-10-05 10:47:37', 43, 'fichaje_entrada', 'Hora: 10:47', '::1'),
(621, '2026-10-05 10:47:38', 43, 'fichaje_salida', 'Hora: 10:47', '::1'),
(622, '2026-10-05 10:47:40', 43, 'fichaje_entrada', 'Hora: 10:47', '::1'),
(623, '2026-10-05 10:47:41', 43, 'fichaje_salida', 'Hora: 10:47', '::1'),
(624, '2026-10-05 10:47:42', 43, 'fichaje_entrada', 'Hora: 10:47', '::1'),
(625, '2026-10-05 10:47:43', 43, 'fichaje_salida', 'Hora: 10:47', '::1'),
(626, '2026-10-05 10:47:45', 43, 'fichaje_entrada', 'Hora: 10:47', '::1'),
(627, '2026-10-05 10:47:46', 43, 'fichaje_salida', 'Hora: 10:47', '::1'),
(628, '2026-10-05 10:47:47', 43, 'fichaje_entrada', 'Hora: 10:47', '::1'),
(629, '2026-10-05 10:47:49', 43, 'fichaje_salida', 'Hora: 10:47', '::1'),
(630, '2026-10-05 10:47:50', 43, 'fichaje_entrada', 'Hora: 10:47', '::1'),
(631, '2026-10-05 10:47:51', 43, 'fichaje_salida', 'Hora: 10:47', '::1'),
(632, '2026-10-05 10:47:53', 43, 'fichaje_entrada', 'Hora: 10:47', '::1'),
(633, '2026-10-05 10:47:54', 43, 'fichaje_salida', 'Hora: 10:47', '::1'),
(634, '2026-10-05 10:47:55', 43, 'fichaje_entrada', 'Hora: 10:47', '::1'),
(635, '2026-10-05 10:47:56', 43, 'fichaje_salida', 'Hora: 10:47', '::1'),
(636, '2026-10-05 10:48:03', 43, 'fichaje_entrada', 'Hora: 10:48', '::1'),
(637, '2026-10-05 10:48:04', 43, 'fichaje_salida', 'Hora: 10:48', '::1'),
(638, '2026-10-05 10:48:05', 43, 'fichaje_entrada', 'Hora: 10:48', '::1'),
(639, '2026-10-05 10:48:06', 43, 'fichaje_salida', 'Hora: 10:48', '::1'),
(640, '2026-10-05 10:48:08', 43, 'fichaje_entrada', 'Hora: 10:48', '::1'),
(641, '2026-10-05 10:48:09', 43, 'fichaje_salida', 'Hora: 10:48', '::1'),
(642, '2026-10-05 10:48:10', 43, 'fichaje_entrada', 'Hora: 10:48', '::1'),
(643, '2026-10-05 10:49:23', 43, 'fichaje_salida', 'Hora: 10:49', '::1'),
(644, '2026-10-05 11:02:03', 43, 'fichaje_entrada', 'Hora: 11:02', '::1'),
(645, '2026-10-05 11:02:04', 43, 'fichaje_salida', 'Hora: 11:02', '::1'),
(646, '2026-10-05 11:02:05', 43, 'fichaje_entrada', 'Hora: 11:02', '::1'),
(647, '2026-10-05 11:02:06', 43, 'fichaje_salida', 'Hora: 11:02', '::1'),
(648, '2026-10-05 11:02:07', 43, 'fichaje_entrada', 'Hora: 11:02', '::1'),
(649, '2026-10-05 11:02:09', 43, 'fichaje_salida', 'Hora: 11:02', '::1'),
(650, '2026-10-05 11:02:10', 43, 'fichaje_entrada', 'Hora: 11:02', '::1'),
(651, '2026-10-05 11:02:11', 43, 'fichaje_salida', 'Hora: 11:02', '::1'),
(652, '2026-10-05 11:02:12', 43, 'fichaje_entrada', 'Hora: 11:02', '::1'),
(653, '2026-10-05 11:02:13', 43, 'fichaje_salida', 'Hora: 11:02', '::1'),
(654, '2026-10-05 11:05:15', 43, 'fichaje_entrada', 'Hora: 11:05', '::1'),
(655, '2026-10-05 11:05:17', 43, 'fichaje_salida', 'Hora: 11:05', '::1'),
(656, '2026-10-05 11:05:18', 43, 'fichaje_entrada', 'Hora: 11:05', '::1'),
(657, '2026-10-05 11:05:19', 43, 'fichaje_salida', 'Hora: 11:05', '::1'),
(658, '2026-10-05 11:05:21', 43, 'fichaje_entrada', 'Hora: 11:05', '::1'),
(659, '2026-10-05 11:05:22', 43, 'fichaje_salida', 'Hora: 11:05', '::1'),
(660, '2026-10-05 11:05:23', 43, 'fichaje_entrada', 'Hora: 11:05', '::1'),
(661, '2026-10-05 11:05:25', 43, 'fichaje_salida', 'Hora: 11:05', '::1'),
(662, '2026-10-05 11:05:26', 43, 'fichaje_entrada', 'Hora: 11:05', '::1'),
(663, '2026-10-05 11:05:27', 43, 'fichaje_salida', 'Hora: 11:05', '::1'),
(664, '2026-10-05 11:10:53', 1, 'login', 'Ingreso correcto', '::1'),
(665, '2026-10-05 11:11:10', 1, 'novedad_creada', 'Vacaciones', '::1'),
(666, '2026-10-05 11:11:14', 1, 'novedad_creada', 'Licencia', '::1'),
(667, '2026-10-05 11:11:17', 1, 'novedad_creada', 'Capacitación', '::1'),
(668, '2026-10-05 11:11:19', 1, 'novedad_creada', 'Vacaciones', '::1'),
(669, '2026-10-05 11:11:20', 1, 'novedad_creada', 'ART', '::1'),
(670, '2026-10-05 11:11:22', 1, 'novedad_creada', 'Vacaciones', '::1'),
(671, '2026-10-05 11:22:54', 1, 'novedad_eliminada', 'ID 7', '::1'),
(672, '2026-10-05 11:22:57', 1, 'novedad_eliminada', 'ID 8', '::1'),
(673, '2026-10-05 11:22:58', 1, 'novedad_eliminada', 'ID 9', '::1'),
(674, '2026-10-05 11:23:00', 1, 'novedad_eliminada', 'ID 10', '::1'),
(675, '2026-10-05 11:23:02', 1, 'novedad_eliminada', 'ID 11', '::1'),
(676, '2026-10-05 11:23:03', 1, 'novedad_eliminada', 'ID 12', '::1'),
(677, '2026-10-05 11:23:05', 1, 'novedad_eliminada', 'ID 13', '::1'),
(678, '2026-10-05 11:23:07', 1, 'novedad_eliminada', 'ID 14', '::1'),
(679, '2026-10-05 11:23:08', 1, 'novedad_eliminada', 'ID 15', '::1'),
(680, '2026-10-05 11:23:18', 1, 'novedad_actualizada', 'ID 16', '::1'),
(681, '2026-10-05 11:23:26', 1, 'novedad_eliminada', 'ID 16', '::1'),
(682, '2026-10-05 11:23:27', 1, 'novedad_eliminada', 'ID 17', '::1'),
(683, '2026-10-05 11:23:37', 1, 'fichaje_aprobado', 'ID 170 - Validado por administración', '::1'),
(684, '2026-10-05 11:23:38', 1, 'fichaje_aprobado', 'ID 169 - Validado por administración', '::1'),
(685, '2026-10-05 11:23:39', 1, 'fichaje_aprobado', 'ID 168 - Validado por administración', '::1'),
(686, '2026-10-05 11:23:39', 1, 'fichaje_aprobado', 'ID 167 - Validado por administración', '::1'),
(687, '2026-10-05 11:23:40', 1, 'fichaje_aprobado', 'ID 166 - Validado por administración', '::1'),
(688, '2026-10-05 11:23:40', 1, 'fichaje_aprobado', 'ID 165 - Validado por administración', '::1'),
(689, '2026-10-05 11:23:40', 1, 'fichaje_aprobado', 'ID 164 - Validado por administración', '::1'),
(690, '2026-10-05 11:23:40', 1, 'fichaje_aprobado', 'ID 163 - Validado por administración', '::1'),
(691, '2026-10-05 11:23:41', 1, 'fichaje_aprobado', 'ID 162 - Validado por administración', '::1'),
(692, '2026-10-05 11:23:41', 1, 'fichaje_aprobado', 'ID 161 - Validado por administración', '::1'),
(693, '2026-10-05 11:23:41', 1, 'fichaje_aprobado', 'ID 79 - Validado por administración', '::1'),
(694, '2026-10-05 11:23:41', 1, 'fichaje_aprobado', 'ID 78 - Validado por administración', '::1'),
(695, '2026-10-05 11:23:42', 1, 'fichaje_aprobado', 'ID 77 - Validado por administración', '::1'),
(696, '2026-10-05 11:23:42', 1, 'fichaje_aprobado', 'ID 76 - Validado por administración', '::1'),
(697, '2026-10-05 11:23:44', 1, 'fichaje_aprobado', 'ID 75 - Validado por administración', '::1'),
(698, '2026-10-05 11:23:44', 1, 'fichaje_aprobado', 'ID 74 - Validado por administración', '::1'),
(699, '2026-10-05 11:23:44', 1, 'fichaje_aprobado', 'ID 73 - Validado por administración', '::1'),
(700, '2026-10-05 11:23:45', 1, 'fichaje_aprobado', 'ID 72 - Validado por administración', '::1'),
(701, '2026-10-05 11:23:45', 1, 'fichaje_aprobado', 'ID 71 - Validado por administración', '::1'),
(702, '2026-10-05 11:23:45', 1, 'fichaje_aprobado', 'ID 70 - Validado por administración', '::1'),
(703, '2026-10-05 11:23:45', 1, 'fichaje_aprobado', 'ID 69 - Validado por administración', '::1'),
(704, '2026-10-05 11:23:45', 1, 'fichaje_aprobado', 'ID 68 - Validado por administración', '::1'),
(705, '2026-10-05 11:23:46', 1, 'fichaje_aprobado', 'ID 67 - Validado por administración', '::1'),
(706, '2026-10-05 11:23:46', 1, 'fichaje_aprobado', 'ID 66 - Validado por administración', '::1'),
(707, '2026-10-05 11:23:46', 1, 'fichaje_aprobado', 'ID 65 - Validado por administración', '::1'),
(708, '2026-10-05 11:23:47', 1, 'fichaje_aprobado', 'ID 64 - Validado por administración', '::1'),
(709, '2026-10-05 11:23:47', 1, 'fichaje_aprobado', 'ID 63 - Validado por administración', '::1'),
(710, '2026-10-05 11:23:47', 1, 'fichaje_aprobado', 'ID 62 - Validado por administración', '::1'),
(711, '2026-10-05 11:23:49', 1, 'fichaje_aprobado', 'ID 61 - Validado por administración', '::1'),
(712, '2026-10-05 11:23:49', 1, 'fichaje_aprobado', 'ID 60 - Validado por administración', '::1'),
(713, '2026-10-05 11:24:13', 1, 'cuenta_editada', 'Configuración', '::1'),
(714, '2026-10-05 11:28:36', 1, 'cuenta_editada', 'Configuración', '::1'),
(715, '2026-10-05 11:28:57', 1, 'sesiones_invalidadas', 'Todas las sesiones - 0 fichajes cerrados', '::1'),
(716, '2026-10-05 11:48:01', 1, 'login', 'Ingreso correcto', '::1'),
(717, '2026-10-05 11:48:33', 1, 'novedad_creada', 'Vacaciones', '::1'),
(718, '2026-10-05 11:48:35', 1, 'novedad_creada', 'Licencia', '::1'),
(719, '2026-10-05 11:48:37', 1, 'novedad_creada', 'ART', '::1'),
(720, '2026-10-05 11:48:39', 1, 'novedad_creada', 'Capacitación', '::1'),
(721, '2026-10-05 11:53:51', 43, 'login', 'Ingreso correcto', '::1'),
(722, '2026-10-05 11:54:12', 43, 'fichaje_entrada', 'Hora: 11:54', '::1'),
(723, '2026-10-05 11:54:35', 43, 'fichaje_salida', 'Hora: 11:54', '::1'),
(724, '2026-10-05 11:56:24', 43, 'login', 'Ingreso correcto', '::1'),
(725, '2026-10-05 12:12:54', 43, 'login', 'Ingreso correcto', '::1'),
(726, '2026-10-05 12:14:40', 1, 'login', 'Ingreso correcto', '::1'),
(727, '2026-10-05 12:15:04', 1, 'fichaje_aprobado', 'ID 171 - Validado por administración', '::1'),
(728, '2026-10-05 12:22:22', 43, 'login', 'Ingreso correcto', '::1'),
(729, '2026-10-05 12:23:01', 1, 'login', 'Ingreso correcto', '::1'),
(730, '2026-10-06 10:06:01', 43, 'login', 'Ingreso correcto', '::1'),
(731, '2026-10-06 10:34:21', 43, 'login', 'Ingreso correcto', '::1'),
(732, '2026-10-06 10:34:29', 43, 'fichaje_entrada', 'Hora: 10:34', '::1'),
(733, '2026-10-06 10:41:16', 43, 'login', 'Ingreso correcto', '::1'),
(734, '2026-10-06 10:41:47', 43, 'fichaje_salida', 'Hora: 10:41', '::1'),
(735, '2026-10-06 10:46:46', 43, 'login', 'Ingreso correcto', '::1'),
(736, '2026-10-06 10:47:07', 43, 'fichaje_entrada', 'Hora: 10:47', '::1'),
(737, '2026-10-06 10:47:20', 43, 'fichaje_salida', 'Hora: 10:47', '::1'),
(738, '2026-10-06 10:47:32', 43, 'fichaje_entrada', 'Hora: 10:47', '::1'),
(739, '2026-10-06 10:47:41', 43, 'fichaje_salida', 'Hora: 10:47', '::1'),
(740, '2026-10-06 10:53:41', 43, 'login', 'Ingreso correcto', '::1'),
(741, '2026-10-06 10:53:46', 43, 'fichaje_entrada', 'Hora: 10:53', '::1'),
(742, '2026-10-06 10:56:52', 43, 'login', 'Ingreso correcto', '::1'),
(743, '2026-10-06 10:57:05', 43, 'fichaje_salida', 'Hora: 10:57', '::1'),
(744, '2026-10-06 10:57:35', 43, 'login', 'Ingreso correcto', '::1'),
(745, '2026-10-06 10:58:57', 1, 'login', 'Ingreso correcto', '::1'),
(746, '2026-10-06 11:00:08', 1, 'fichaje_aprobado', 'ID 175 - Validado por administración', '::1'),
(747, '2026-10-06 11:00:09', 1, 'fichaje_aprobado', 'ID 174 - Validado por administración', '::1'),
(748, '2026-10-06 11:00:10', 1, 'fichaje_aprobado', 'ID 173 - Validado por administración', '::1'),
(749, '2026-10-06 11:00:10', 1, 'fichaje_aprobado', 'ID 172 - Validado por administración', '::1'),
(750, '2026-10-06 11:00:51', 43, 'login', 'Ingreso correcto', '::1'),
(751, '2026-10-06 11:01:53', 1, 'login', 'Ingreso correcto', '::1'),
(752, '2026-10-06 11:03:17', 1, 'procedimiento_creado', 'Cirugia test', '::1'),
(753, '2026-10-06 11:03:32', 1, 'procedimiento_eliminado', 'ID 20', '::1');
INSERT INTO `auditoria` (`id`, `fecha`, `id_usuario`, `accion`, `detalle`, `ip`) VALUES
(754, '2026-10-06 11:12:09', 43, 'login', 'Ingreso correcto', '::1'),
(755, '2026-10-06 11:12:19', 43, 'fichaje_entrada', 'Hora: 11:12', '::1'),
(756, '2026-10-06 11:12:31', 43, 'fichaje_salida', 'Hora: 11:12', '::1');

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
(4, 2, 1, 'Cirujano'),
(21, 1, 1, 'Cirujano/a'),
(26, 3, 1, 'Cirujano/a');

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
(34, 21, '2026-10-01', '21:25:36', NULL, NULL, 1, 1, 'Validado por administración'),
(35, 21, '2026-10-02', '07:50:31', '07:52:05', 0.03, 1, 1, 'Validado por administración'),
(36, 21, '2026-10-02', '07:52:09', '07:52:11', 0.00, 2, 1, '.'),
(37, 21, '2026-10-02', '09:01:15', '09:11:39', 0.17, 1, 1, 'Validado por administración'),
(38, 21, '2026-10-02', '09:24:20', '09:24:22', 0.00, 1, 1, 'Validado por administración'),
(39, 21, '2026-10-02', '09:24:23', '09:24:24', 0.00, 1, 1, 'Validado por administración'),
(40, 21, '2026-10-02', '09:24:25', '09:24:27', 0.00, 1, 1, 'Validado por administración'),
(41, 21, '2026-10-02', '09:24:28', '09:24:29', 0.00, 1, 1, 'Validado por administración'),
(42, 21, '2026-10-02', '09:24:30', '09:35:50', 0.19, 1, 1, 'Validado por administración'),
(43, 21, '2026-10-02', '09:52:17', '09:52:19', 0.00, 2, 1, '.'),
(44, 21, '2026-10-02', '09:52:20', '09:52:21', 0.00, 1, 1, 'Validado por administración'),
(45, 21, '2026-10-02', '09:52:23', '09:52:24', 0.00, 1, 1, 'Validado por administración'),
(46, 21, '2026-10-02', '09:52:25', '09:52:26', 0.00, 1, 1, 'Validado por administración'),
(47, 21, '2026-10-02', '09:52:27', '09:52:28', 0.00, 1, 1, 'Validado por administración'),
(48, 21, '2026-10-02', '09:56:03', '09:56:06', 0.00, 1, 1, 'Validado por administración'),
(49, 21, '2026-10-02', '10:20:03', '10:20:21', 0.01, 1, 1, 'Validado por administración'),
(50, 21, '2026-10-02', '12:47:50', '12:47:51', 0.00, 1, 1, 'Validado por administración'),
(51, 21, '2026-10-02', '12:47:52', '12:47:53', 0.00, 1, 1, 'Validado por administración'),
(52, 21, '2026-10-02', '12:47:54', '12:47:55', 0.00, 1, 1, 'Validado por administración'),
(53, 21, '2026-10-02', '12:47:56', '12:47:57', 0.00, 1, 1, 'Validado por administración'),
(54, 21, '2026-10-02', '12:47:58', '12:47:59', 0.00, 1, 1, 'Validado por administración'),
(55, 21, '2026-10-03', '12:28:50', '12:28:52', 0.00, 1, 1, 'Validado por administración'),
(56, 21, '2026-10-03', '12:28:54', '12:28:57', 0.00, 1, 1, 'Validado por administración'),
(57, 21, '2026-10-03', '12:38:01', '12:38:03', 0.00, 1, 1, 'Validado por administración'),
(58, 21, '2026-10-03', '12:38:06', '12:38:08', 0.00, 1, 1, 'Validado por administración'),
(59, 21, '2026-10-03', '13:25:00', NULL, NULL, 2, 1, 'Validado por administración'),
(60, 43, '2026-10-05', '10:30:00', '10:37:42', 0.13, 1, 1, 'Validado por administración'),
(61, 43, '2026-10-05', '10:37:44', '10:37:46', 0.00, 1, 1, 'Validado por administración'),
(62, 43, '2026-10-05', '10:37:48', '10:37:49', 0.00, 1, 1, 'Validado por administración'),
(63, 43, '2026-10-05', '10:37:51', '10:37:53', 0.00, 1, 1, 'Validado por administración'),
(64, 43, '2026-10-05', '10:37:54', '10:37:56', 0.00, 1, 1, 'Validado por administración'),
(65, 43, '2026-10-05', '10:41:24', '10:41:50', 0.01, 1, 1, 'Validado por administración'),
(66, 43, '2026-10-05', '10:41:52', '10:41:53', 0.00, 1, 1, 'Validado por administración'),
(67, 43, '2026-10-05', '10:41:54', '10:47:35', 0.09, 1, 1, 'Validado por administración'),
(68, 43, '2026-10-05', '10:47:37', '10:47:38', 0.00, 1, 1, 'Validado por administración'),
(69, 43, '2026-10-05', '10:47:40', '10:47:41', 0.00, 1, 1, 'Validado por administración'),
(70, 43, '2026-10-05', '10:47:42', '10:47:43', 0.00, 1, 1, 'Validado por administración'),
(71, 43, '2026-10-05', '10:47:45', '10:47:46', 0.00, 1, 1, 'Validado por administración'),
(72, 43, '2026-10-05', '10:47:47', '10:47:49', 0.00, 1, 1, 'Validado por administración'),
(73, 43, '2026-10-05', '10:47:50', '10:47:51', 0.00, 1, 1, 'Validado por administración'),
(74, 43, '2026-10-05', '10:47:53', '10:47:54', 0.00, 1, 1, 'Validado por administración'),
(75, 43, '2026-10-05', '10:47:55', '10:47:56', 0.00, 1, 1, 'Validado por administración'),
(76, 43, '2026-10-05', '10:48:03', '10:48:04', 0.00, 1, 1, 'Validado por administración'),
(77, 43, '2026-10-05', '10:48:05', '10:48:06', 0.00, 1, 1, 'Validado por administración'),
(78, 43, '2026-10-05', '10:48:08', '10:48:09', 0.00, 1, 1, 'Validado por administración'),
(79, 43, '2026-10-05', '10:48:10', '10:49:23', 0.02, 1, 1, 'Validado por administración'),
(161, 43, '2026-10-05', '11:02:03', '11:02:04', 0.00, 1, 1, 'Validado por administración'),
(162, 43, '2026-10-05', '11:02:05', '11:02:06', 0.00, 1, 1, 'Validado por administración'),
(163, 43, '2026-10-05', '11:02:07', '11:02:09', 0.00, 1, 1, 'Validado por administración'),
(164, 43, '2026-10-05', '11:02:10', '11:02:11', 0.00, 1, 1, 'Validado por administración'),
(165, 43, '2026-10-05', '11:02:12', '11:02:12', 0.00, 1, 1, 'Validado por administración'),
(166, 43, '2026-10-05', '11:05:15', '11:05:17', 0.00, 1, 1, 'Validado por administración'),
(167, 43, '2026-10-05', '11:05:18', '11:05:19', 0.00, 1, 1, 'Validado por administración'),
(168, 43, '2026-10-05', '11:05:21', '11:05:22', 0.00, 1, 1, 'Validado por administración'),
(169, 43, '2026-10-05', '11:05:23', '11:05:25', 0.00, 1, 1, 'Validado por administración'),
(170, 43, '2026-10-05', '11:05:26', '11:05:27', 0.00, 1, 1, 'Validado por administración'),
(171, 43, '2026-10-05', '11:54:12', '11:54:35', 0.01, 1, 1, 'Validado por administración'),
(172, 43, '2026-10-06', '10:34:29', '10:41:47', 0.12, 1, 1, 'Validado por administración'),
(173, 43, '2026-10-06', '10:47:07', '10:47:20', 0.00, 1, 1, 'Validado por administración'),
(174, 43, '2026-10-06', '10:47:32', '10:47:41', 0.00, 1, 1, 'Validado por administración'),
(175, 43, '2026-10-06', '10:53:46', '10:57:05', 0.06, 1, 1, 'Validado por administración'),
(176, 43, '2026-10-06', '11:12:19', '11:12:31', 0.00, 0, NULL, NULL);

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
(18, 39, 'Vacaciones', '2026-10-05', '2026-10-07', '.', 1),
(19, 39, 'Licencia', '2026-10-05', '2026-10-07', '.', 1),
(20, 39, 'ART', '2026-10-05', '2026-10-07', '.', 1),
(21, 39, 'Capacitación', '2026-10-05', '2026-10-07', '.', 1);

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
('fichaje:usuario:21', 1, '2026-10-03 13:25:51', NULL),
('fichaje:usuario:43', 11, '2026-10-05 10:30:00', '2026-10-05 10:40:57');

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
(1, 'Instrumentador/a', 'Profesional encargado de realizar procedimientos quirúrgicos'),
(2, 'Tecnico/a radiologa', 'Profesional encargado de la anestesia durante las cirugías'),
(3, 'Administrativo/a', 'Personal de enfermería'),
(4, 'Limpieza', 'Personal encargado de tareas administrativas'),
(5, 'Enfermero/a', 'Personal encargado del mantenimiento de las instalaciones'),
(6, 'Anestesista', 'Personal que asiste al cirujano durante los procedimientos');

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
(2, 'Cirugía traumatológica', 1),
(4, 'Cirugía ambulatoria', 1),
(19, 'Cirugía general', 1);

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
  `password_hash` varchar(255) NOT NULL,
  `es_admin` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `sesion_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `dni`, `especialidad`, `id_tipo_personal`, `tipo_contrato`, `password_hash`, `es_admin`, `activo`, `sesion_token`, `created_at`) VALUES
(1, 'Natalia', 'Telechea', '30111222', 'Administrativo/a', 3, 'Admin', '$2y$10$/vaYdyxeQuk4yYOC0wp0beB0zbaa3H3S.HF3qq2MGK3yts/M6zZ.6', 1, 1, '97c4f7483201eabea4be4e5ef1d22fbb065e5d6f7fbc943019a675939b8f09be', '2026-09-09 21:21:39'),
(21, 'Morena', 'Morinigo', '30400500', 'Instrumentador/a', 1, 'Fijo', '$2y$10$8/alZSvLlUWi533S40kwQOY79/BfQeFNUp0cafn2E51QcxJmA18dy', 0, 1, NULL, '2026-10-02 00:23:48'),
(35, 'Belene', 'Betanso', '35700600', 'Tecnico/a radiologa', 2, 'Fijo', '$2y$10$gRQutIKmAxJrUWJAF.eZk.dq24TY1p6PL8LLtgS.j7EUzfNFphr/i', 0, 1, NULL, '2026-10-05 13:22:19'),
(36, 'Fabiana', 'Nesci', '34500600', 'Administrativo/a', 3, 'Fijo', '$2y$10$xP3gaau.nrBd1zYqMMSmtOYsmeSHEzr5V5tGWrL2jEFy8q1MemoDa', 0, 1, NULL, '2026-10-05 13:22:58'),
(37, 'Ana', 'Begbeder', '33600700', 'Limpieza', 4, 'Fijo', '$2y$10$sjLOGNE5F51GotgqTQLdE.eMlPhXYoYWXR9mOoo6Kgjvr8d26MKsq', 0, 1, NULL, '2026-10-05 13:23:33'),
(38, 'Brenda', 'Conforti', '32300400', 'Enfermero/a', 5, 'Por Hora', '$2y$10$0D8vGi/fO1lBJkPrHRRtTOGZPloK107MsS4BwfT5pZsj5X1YziYy2', 0, 1, NULL, '2026-10-05 13:24:56'),
(39, 'Florencia', 'Aranaga', '31200300', 'Instrumentador/a', 1, 'Por Cirugía', '$2y$10$ANmngnwaZBTc/e38/K5ATuPu6KAgSGvtv3ENfdb.gn.jt6ReQ7JQi', 0, 1, NULL, '2026-10-05 13:25:27'),
(40, 'Agustina', 'Seco', '30500600', 'Instrumentador/a', 1, 'Por Cirugía', '$2y$10$ExSCnmD2fxpQSbBaj3YoMOmdhGCKShw8ovfGgPUfUuyjjo6jgzkvi', 0, 1, NULL, '2026-10-05 13:26:15'),
(41, 'Paula', 'Santellan', '38500400', 'Anestesista', 6, 'Por Cirugía', '$2y$10$urlg25ki1rZpfmYYxfV2H.aL1G517kNX/0GNGWP/LKXt03eZQnXGK', 0, 1, NULL, '2026-10-05 13:26:44'),
(42, 'Lorena', 'Viola', '39100200', 'Anestesista', 6, 'Por Cirugía', '$2y$10$h7nc/1473qEhgNk7Rqkp..lv.SXQLl4cfcNmVekYcV9PfsbsyaFTu', 0, 1, NULL, '2026-10-05 13:27:25'),
(43, 'test', 'test', '11111222', 'Administrativo/a', 3, 'Fijo', '$2y$10$ad.Az/fEAljGZ4wC3BjsN.BR0.xUVpWC6cv0TcYhfEtQzdNULzgVe', 0, 1, '14c66f3e3f686f3f9e69ac1997220f9a7d4ca7d33ffef81870f02cffaf725f31', '2026-10-05 13:27:57');

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
  ADD KEY `fk_usuarios_tipo_personal` (`id_tipo_personal`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alquileres`
--
ALTER TABLE `alquileres`
  MODIFY `id_alquiler` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=757;

--
-- AUTO_INCREMENT de la tabla `cirugias`
--
ALTER TABLE `cirugias`
  MODIFY `id_cirugia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `cirugia_personal`
--
ALTER TABLE `cirugia_personal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `fichajes`
--
ALTER TABLE `fichajes`
  MODIFY `id_fichaje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=177;

--
-- AUTO_INCREMENT de la tabla `novedades`
--
ALTER TABLE `novedades`
  MODIFY `id_novedad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `tipos_personal`
--
ALTER TABLE `tipos_personal`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tipos_procedimiento`
--
ALTER TABLE `tipos_procedimiento`
  MODIFY `id_tipo_proc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

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
