<?php
require_once __DIR__ . '/../../../src/auth.php';
require_once __DIR__ . '/../../../src/security.php';
require_once __DIR__ . '/../../../src/admin_functions.php';
$user = require_admin();
$current = basename($_SERVER['PHP_SELF']);
$brand_home = (int)$user['es_admin'] === 1 ? 'panel.php' : '../dashboard.php';
$brand_home_title = (int)$user['es_admin'] === 1 ? 'Ir al panel de administración' : 'Ir al fichaje';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title ?? 'Administración') ?> | AMEMT</title>
<link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/bootstrap-compat.css?v=<?= (int)@filemtime(__DIR__ . '/../../assets/css/bootstrap-compat.css') ?>">
<link rel="stylesheet" href="../assets/css/style.css?v=<?= (int)@filemtime(__DIR__ . '/../../assets/css/style.css') ?>">
<script src="../assets/js/confirm-modal.js?v=<?= (int)@filemtime(__DIR__ . '/../../assets/js/confirm-modal.js') ?>" defer></script>
<script src="../assets/js/dni-input.js?v=<?= (int)@filemtime(__DIR__ . '/../../assets/js/dni-input.js') ?>" defer></script>
</head>
<body>
<div class="app admin-app">
<aside class="sidebar">
    <div class="side-brand"><a class="brand-home" href="<?= e($brand_home) ?>" title="<?= e($brand_home_title) ?>"><img class="mini-logo" src="../assets/img/amemt_logo.jpg" alt="AMEMT"><strong>Fichero Digital</strong></a></div>
    <h2>MENÚ</h2>
    <nav>
        <a class="<?= $current === 'panel.php' ? 'active' : '' ?>" href="panel.php">Panel</a>
        <a class="<?= in_array($current,['empleados.php','empleado_nuevo.php','empleado_editar.php'],true) ? 'active' : '' ?>" href="empleados.php">Empleados</a>
        <a class="<?= $current === 'cirugias.php' ? 'active' : '' ?>" href="cirugias.php">Cirugías</a>
        <a class="<?= $current === 'vacaciones.php' ? 'active' : '' ?>" href="vacaciones.php">Vacaciones / ART</a>
        <a class="<?= $current === 'alquileres.php' ? 'active' : '' ?>" href="alquileres.php">Alquileres</a>
        <a class="<?= $current === 'fichajes_pendientes.php' ? 'active' : '' ?>" href="fichajes_pendientes.php">Pendientes de salida</a>
        <a class="<?= $current === 'validacion.php' ? 'active' : '' ?>" href="validacion.php">Validación</a>
        <a class="<?= $current === 'resumen_mensual.php' ? 'active' : '' ?>" href="resumen_mensual.php">Resumen e informes</a>
        <a class="<?= $current === 'configuracion.php' ? 'active' : '' ?>" href="configuracion.php">Configuración</a>
    </nav>
    <a class="logout-link" href="../logout.php"><?= icon('logout', 15) ?>Cerrar sesión</a>
</aside>
<main class="content admin-content">
<header class="topbar">
    <div>
        <h1><?= e($page_title ?? 'Panel de administración') ?></h1>
    </div>
    <div class="user-badge"><span><?= e($user['nombre'].' '.$user['apellido']) ?></span></div>
</header>
