<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/functions.php';
$user = require_login();
$current = basename($_SERVER['PHP_SELF']);
$page_title = $page_title ?? 'Fichaje';
$brand_home = (int)$user['es_admin'] === 1 ? 'admin/panel.php' : 'dashboard.php';
$brand_home_title = (int)$user['es_admin'] === 1 ? 'Ir al panel de administración' : 'Ir al fichaje';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<title><?= e($page_title) ?> | AMEMT</title>
<link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/bootstrap-compat.css?v=<?= (int)@filemtime(__DIR__ . '/../assets/css/bootstrap-compat.css') ?>">
<link rel="stylesheet" href="assets/css/style.css?v=<?= (int)@filemtime(__DIR__ . '/../assets/css/style.css') ?>">
<script src="assets/js/dni-input.js?v=<?= (int)@filemtime(__DIR__ . '/../assets/js/dni-input.js') ?>" defer></script>
</head>
<body>
<div class="app">
<aside class="sidebar">
    <div class="side-brand"><a class="brand-home" href="<?= e($brand_home) ?>" title="<?= e($brand_home_title) ?>"><img class="mini-logo" src="assets/img/amemt_logo.jpg" alt="AMEMT"><strong>Fichero Digital</strong></a></div>
    <h2>MENÚ</h2>
    <a class="<?= $current === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">Fichaje</a>
    <a class="<?= $current === 'resumen.php' ? 'active' : '' ?>" href="resumen.php">Mi resumen</a>
    <a class="logout-link" href="logout.php"><?= icon('logout', 15) ?>Cerrar sesión</a>
</aside>

<main class="content">
<header class="topbar">
    <div>
        <h1><?= e($page_heading ?? $page_title) ?></h1>
        <?php if (!empty($page_subtitle)): ?><p><?= e($page_subtitle) ?></p><?php endif; ?>
    </div>
    <div class="topbar-actions">
        <div class="sound-control" hidden>
            <button type="button" class="sound-toggle" id="soundToggle" aria-label="Silenciar sonidos" aria-pressed="false" title="Silenciar sonidos">
                <span class="sound-icon on"><?= icon('volume', 18) ?></span>
                <span class="sound-icon off"><?= icon('volume-mute', 18) ?></span>
            </button>
            <input type="range" class="sound-volume" id="soundVolume" min="0" max="100" step="5" value="100" aria-label="Volumen de los sonidos" title="Volumen de los sonidos">
        </div>
        <div class="user-badge">
            <?= e(strtoupper(substr($user['nombre'], 0, 1) . substr($user['apellido'], 0, 1))) ?>
        </div>
    </div>
</header>