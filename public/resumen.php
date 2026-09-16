<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

$user = require_login();
$hours = month_hours($user['id']);
$fichajes = recent_fichajes($user['id']);

$page_title = 'Mi resumen';
$page_heading = 'Mi resumen';
$page_subtitle = $user['nombre'] . ' ' . $user['apellido'];
require __DIR__ . '/partials/header.php';
?>

<section class="cards">
    <article class="stat">
        <span>Horas del mes</span>
        <strong><?= number_format($hours, 2, ',', '.') ?> h</strong>
        <small>Horas trabajadas registradas</small>
    </article>
</section>

<section class="panel">
    <h2>Últimos fichajes</h2>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Entrada</th>
                <th>Salida</th>
                <th>Horas</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($fichajes as $f): ?>
            <tr>
                <td><?= e(date('d/m/Y', strtotime($f['fecha']))) ?></td>
                <td><?= e($f['hora_entrada'] ? substr($f['hora_entrada'], 0, 5) : '-') ?></td>
                <td><?= e($f['hora_salida'] ? substr($f['hora_salida'], 0, 5) : '-') ?></td>
                <td><?= e($f['horas_trabajadas'] !== null ? number_format((float)$f['horas_trabajadas'], 2, ',', '.') . ' h' : '-') ?></td>
                <td><?= (int)$f['validado_admin'] === 1 ? 'Validado' : 'Pendiente' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>