<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

$user = require_login();
if ((int)$user['es_admin'] === 1) { header('Location: admin/panel.php'); exit; }

$mesRaw = (string)($_GET['mes'] ?? date('Y-m'));
$mes = preg_match('/^\d{4}-\d{2}$/', $mesRaw) ? $mesRaw : date('Y-m');

$stats = my_month_stats($user['id'], $mes);
$fichajes = my_month_fichajes($user['id'], $mes);
$cirugias = my_month_cirugias($user['id'], $mes);
$novedades = my_month_novedades($user['id'], $mes);

$nombresMes = [
    1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
    'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre',
];
$nombreMes = ucfirst($nombresMes[(int)substr($mes, 5, 2)] . ' de ' . substr($mes, 0, 4));

$estadoFichaje = static fn (int $v): array => match ($v) {
    1 => ['texto' => 'Aprobado', 'clase' => 'active'],
    2 => ['texto' => 'Rechazado', 'clase' => 'delayed'],
    default => ['texto' => 'Pendiente', 'clase' => 'pending'],
};
$fmtHoras = static fn ($v) => rtrim(rtrim(number_format((float)$v, 2, ',', '.'), '0'), ',');

$page_title = 'Mi resumen';
$page_heading = 'Mi resumen';
$page_subtitle = $user['nombre'] . ' ' . $user['apellido'];
require __DIR__ . '/partials/header.php';
?>

<div class="section-head">
    <h2>Actividad de <?= e($nombreMes) ?></h2>
    <form class="resumen-filter" method="get">
        <input type="month" name="mes" value="<?= e($mes) ?>">
        <button class="small-btn" type="submit"><?= icon('calendar') ?>Ver mes</button>
    </form>
</div>

<section class="cards admin-stats">
    <article class="stat"><span>Horas del mes</span><strong><?= e($fmtHoras($stats['horas'])) ?> h</strong><small>trabajadas</small></article>
    <article class="stat"><span>Días trabajados</span><strong><?= (int)$stats['dias'] ?></strong><small><?= (int)$stats['fichajes'] ?> fichajes</small></article>
    <article class="stat"><span>Cirugías</span><strong><?= (int)$stats['cirugias'] ?></strong><small>participaciones</small></article>
    <article class="stat"><span>Novedades</span><strong><?= (int)$stats['novedades'] ?></strong><small>vacaciones / ART</small></article>
</section>

<div class="section-head" style="margin-top:34px;"><h2>Mis fichajes</h2></div>
<section class="panel"><div class="table-wrap"><table>
<thead><tr><th>Fecha</th><th>Entrada</th><th>Salida</th><th>Horas</th><th>Estado</th></tr></thead>
<tbody>
<?php foreach ($fichajes as $f): $st = $estadoFichaje((int)$f['validado_admin']); ?>
<tr>
    <td><?= e(date('d/m/Y', strtotime($f['fecha']))) ?></td>
    <td><?= e($f['entrada'] ?? '-') ?></td>
    <td><?= e($f['salida'] ?? '-') ?></td>
    <td><?= $f['horas_trabajadas'] !== null ? e($fmtHoras($f['horas_trabajadas'])) . ' h' : '-' ?></td>
    <td>
        <span class="status <?= e($st['clase']) ?>"><?= e($st['texto']) ?></span>
        <?php if (!empty($f['obs_validacion'])): ?><span class="val-obs"><?= e($f['obs_validacion']) ?></span><?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
<?php if (!$fichajes): ?><tr><td colspan="5">No hay fichajes en este período.</td></tr><?php endif; ?>
</tbody></table></div></section>

<div class="section-head" style="margin-top:34px;"><h2>Mis cirugías</h2></div>
<section class="panel"><div class="table-wrap"><table>
<thead><tr><th>Fecha</th><th>Hora</th><th>Procedimiento</th><th>Mi rol</th><th>Observaciones</th></tr></thead>
<tbody>
<?php foreach ($cirugias as $c): ?>
<tr>
    <td><?= e(date('d/m/Y', strtotime($c['fecha']))) ?></td>
    <td><?= e($c['hora_inicio']) ?></td>
    <td><?= e($c['procedimiento']) ?></td>
    <td><?= e($c['rol'] !== '' ? $c['rol'] : '-') ?></td>
    <td><?= e($c['observaciones'] ?? '-') ?></td>
</tr>
<?php endforeach; ?>
<?php if (!$cirugias): ?><tr><td colspan="5">No participaste de cirugías en este período.</td></tr><?php endif; ?>
</tbody></table></div></section>

<div class="section-head" style="margin-top:34px;"><h2>Mis novedades</h2></div>
<section class="panel"><div class="table-wrap"><table>
<thead><tr><th>Tipo</th><th>Desde</th><th>Hasta</th><th>Observaciones</th></tr></thead>
<tbody>
<?php foreach ($novedades as $n): ?>
<tr>
    <td><span class="tag hour"><?= e($n['tipo']) ?></span></td>
    <td><?= e($n['fecha_desde'] ?? '-') ?></td>
    <td><?= e($n['fecha_hasta'] ?? '-') ?></td>
    <td><?= e($n['observaciones'] ?: '-') ?></td>
</tr>
<?php endforeach; ?>
<?php if (!$novedades): ?><tr><td colspan="4">No hay novedades en este período.</td></tr><?php endif; ?>
</tbody></table></div></section>

<?php require __DIR__ . '/partials/footer.php'; ?>
