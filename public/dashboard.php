<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

$user = require_login();
if ((int)$user['es_admin'] === 1) { header('Location: admin/panel.php'); exit; }
$fichaje = today_fichaje($user['id']);
$hours = month_hours($user['id']);
$surgeries = today_cirurgies($user['id']);

$page_title = 'Fichaje';
$page_heading = 'Bienvenida/o, ' . $user['nombre'];
$page_subtitle = 'Panel de empleado';
require __DIR__ . '/partials/header.php';
?>

<section class="cards">
    <article class="stat">
        <span>Entrada hoy</span>
        <strong><?= e($fichaje['hora_entrada'] ?? '--:--') ?></strong>
        <small><?= date('d/m/Y') ?></small>
    </article>

    <article class="stat">
        <span>Horas este mes</span>
        <strong><?= number_format($hours, 2, ',', '.') ?> h</strong>
        <small>Horas registradas</small>
    </article>
</section>

<section class="actions">
    <button id="btnEntrada" class="btn entry" <?= ($fichaje && !$fichaje['hora_salida']) ? 'disabled' : '' ?>>
        <?= icon('login', 18) ?>Registrar entrada
    </button>
    <button id="btnSalida" class="btn exit" <?= (!$fichaje || !$fichaje['hora_entrada'] || $fichaje['hora_salida']) ? 'disabled' : '' ?>>
        <?= icon('logout', 18) ?>Registrar salida
    </button>
    <p id="message" class="alert hidden"></p>
</section>

<section class="panel">
    <h2>Actividades del día</h2>
    <table>
        <thead>
            <tr>
                <th>Evento</th>
                <th>Hora</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($fichaje && $fichaje['hora_entrada']): ?>
                <tr>
                    <td>Entrada</td>
                    <td><?= e(substr($fichaje['hora_entrada'], 0, 5)) ?></td>
                </tr>
            <?php endif; ?>

            <?php foreach ($surgeries as $surgery): ?>
                <tr>
                    <td>Cirugía participada - <?= e($surgery['rol_en_cirugia']) ?></td>
                    <td><?= e(substr($surgery['hora_inicio'], 0, 5)) ?></td>
                </tr>
            <?php endforeach; ?>

            <?php if (!$fichaje && !$surgeries): ?>
                <tr><td colspan="2">No hay actividades registradas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>