<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';

$user = require_admin();
$page_title = 'Validación de entradas y salidas';
$error = '';
$ok = '';

$mesRaw = (string)($_POST['mes'] ?? $_GET['mes'] ?? date('Y-m'));
$mes = preg_match('/^\d{4}-\d{2}$/', $mesRaw) ? $mesRaw : date('Y-m');

$estadoRaw = (string)($_POST['estado'] ?? $_GET['estado'] ?? 'pendiente');
$estado = in_array($estadoRaw, ['pendiente', 'aprobado', 'rechazado', 'todos'], true) ? $estadoRaw : 'pendiente';

$idEmp = (int)($_POST['empleado'] ?? $_GET['empleado'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'La sesión del formulario expiró. Intentá nuevamente.';
    } else {
        $r = validar_fichaje_estado((int)($_POST['id'] ?? 0), (int)($_POST['valor'] ?? 0), (string)($_POST['obs'] ?? ''), (int)$user['id']);
        if ($r['ok']) {
            $ok = match ((int)$r['estado']) {
                0 => 'Fichaje devuelto a pendiente.',
                2 => 'Entrada/salida rechazada.',
                default => 'Entrada/salida aprobada.',
            };
        } else {
            $error = $r['message'];
        }
    }
}

$stats = fichajes_validacion_stats($mes, $idEmp);
$fichajes = fichajes_validacion($mes, $estado, $idEmp);
$empleados = active_employees();

$estados = [
    'pendiente' => 'Pendientes',
    'aprobado'  => 'Aprobados',
    'rechazado' => 'Rechazados',
    'todos'     => 'Todos',
];

require __DIR__ . '/partials/header.php';
?>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<?php if ($ok): ?><div class="alert success"><?= e($ok) ?></div><?php endif; ?>

<div class="section-head">
    <h2>Validar entradas y salidas</h2>
    <span class="status <?= (int)$stats['pendiente'] > 0 ? 'pending' : 'active' ?>"><?= (int)$stats['pendiente'] ?> pendiente(s)</span>
</div>

<section class="cards admin-stats">
    <article class="stat"><span>Pendientes</span><strong><?= (int)$stats['pendiente'] ?></strong><small>sin revisar</small></article>
    <article class="stat"><span>Aprobados</span><strong><?= (int)$stats['aprobado'] ?></strong><small>validados</small></article>
    <article class="stat"><span>Rechazados</span><strong><?= (int)$stats['rechazado'] ?></strong><small>con observación</small></article>
    <article class="stat"><span>Total del mes</span><strong><?= (int)$stats['total'] ?></strong><small>fichajes</small></article>
</section>

<form class="filters report-filters" method="get" style="margin-bottom:22px;">
    <div>
        <label style="margin:0 0 4px;font-size:12px;color:var(--muted);">Mes</label>
        <input type="month" name="mes" value="<?= e($mes) ?>">
    </div>
    <div>
        <label style="margin:0 0 4px;font-size:12px;color:var(--muted);">Empleado</label>
        <select name="empleado">
            <option value="">Todos los empleados</option>
            <?php foreach ($empleados as $emp): ?>
            <option value="<?= (int)$emp['id_usuario'] ?>" <?= $idEmp === (int)$emp['id_usuario'] ? 'selected' : '' ?>><?= e($emp['apellido'] . ', ' . $emp['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label style="margin:0 0 4px;font-size:12px;color:var(--muted);">Estado</label>
        <select name="estado">
            <?php foreach ($estados as $key => $label): ?>
            <option value="<?= e($key) ?>" <?= $estado === $key ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div style="display:flex;align-items:flex-end;">
        <button class="small-btn" type="submit"><?= icon('search') ?>Filtrar</button>
    </div>
</form>

<section class="panel"><div class="table-wrap"><table>
<thead><tr>
    <th>Empleado</th><th>Contrato</th><th>Fecha</th><th>Entrada</th><th>Salida</th><th>Horas</th><th>Estado</th><th style="min-width:320px;">Observación y validación</th>
</tr></thead>
<tbody>
<?php foreach ($fichajes as $f): $st = estado_validacion((int)$f['validado_admin']); ?>
<tr>
    <td><?= e($f['empleado']) ?><br><small style="color:var(--muted);">DNI <?= e($f['dni']) ?></small></td>
    <td><span class="tag <?= e(contract_class($f['tipo_contrato'])) ?>"><?= e($f['tipo_contrato']) ?></span></td>
    <td><?= e($f['fecha']) ?></td>
    <td><?= e($f['entrada'] ?? '-') ?></td>
    <td><?= $f['salida'] !== null ? e($f['salida']) : '<span class="status pending">Sin salida</span>' ?></td>
    <td><?= $f['horas_trabajadas'] !== null ? e(number_format((float)$f['horas_trabajadas'], 2, ',', '.')) . ' h' : '-' ?></td>
    <td>
        <span class="status <?= e($st['clase']) ?>"><?= e($st['texto']) ?></span>
        <?php if (!empty($f['obs_validacion'])): ?><span class="val-obs"><?= e($f['obs_validacion']) ?></span><?php endif; ?>
        <?php if (!empty($f['validador'])): ?><span class="val-obs">por <?= e($f['validador']) ?></span><?php endif; ?>
    </td>
    <td>
        <form class="val-form" method="post" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="id" value="<?= (int)$f['id_fichaje'] ?>">
            <input type="hidden" name="mes" value="<?= e($mes) ?>">
            <input type="hidden" name="estado" value="<?= e($estado) ?>">
            <input type="hidden" name="empleado" value="<?= (int)$idEmp ?>">
            <input type="text" class="obs-input" name="obs" maxlength="255" placeholder="Observación (obligatoria al rechazar)..." value="<?= e($f['obs_validacion'] ?? '') ?>">
            <button type="submit" name="valor" value="1" class="val-btn ok" title="Aprobar entrada/salida"><?= icon('check', 16) ?></button>
            <button type="submit" name="valor" value="2" class="val-btn no" title="Rechazar entrada/salida"><?= icon('x', 16) ?></button>
            <?php if ((int)$f['validado_admin'] !== 0): ?>
            <button type="submit" name="valor" value="0" class="val-btn pend" title="Volver a pendiente"><?= icon('undo', 14) ?>Volver a pendiente</button>
            <?php endif; ?>
        </form>
    </td>
</tr>
<?php endforeach; ?>
<?php if (!$fichajes): ?><tr><td colspan="8">No hay fichajes para los filtros seleccionados.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
