<?php
require_once __DIR__ . '/../../../src/auth.php';
require_once __DIR__ . '/../../../src/security.php';
require_once __DIR__ . '/../../../src/admin_functions.php';
$user = require_admin();
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title ?? 'Administración') ?> | AMEMT</title>
<link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/bootstrap-compat.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app admin-app">
<aside class="sidebar">
    <div class="side-brand"><img class="mini-logo" src="../assets/img/amemt_logo.jpg" alt="AMEMT"><strong>Fichero Digital</strong></div>
    <h2>MENÚ</h2>
    <nav>
        <a class="<?= $current === 'panel.php' ? 'active' : '' ?>" href="panel.php">Panel</a>
        <a class="<?= in_array($current,['empleados.php','empleado_nuevo.php','empleado_editar.php'],true) ? 'active' : '' ?>" href="empleados.php">Empleados</a>
        <a href="cirugias.php">Cirugías</a>
        <a href="vacaciones.php">Vacaciones / ART</a>
        <a href="alquileres.php">Alquileres</a>
        <a class="<?= $current === 'fichajes_pendientes.php' ? 'active' : '' ?>" href="fichajes_pendientes.php">Fichajes pendientes</a>
        <a href="resumen_mensual.php">Resumen e informes</a>
        <a href="configuracion.php">Configuración</a>
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
