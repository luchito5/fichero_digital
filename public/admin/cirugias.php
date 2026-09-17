<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';
$page_title = 'Cirugías';
$error = '';
$ok = '';

$cirujanos = employees_by_role(['cirujan']);
$instrumentadores = employees_by_role(['instrument']);
$anestesistas = employees_by_role(['anestes']);
$otros = employees_other(['cirujan', 'instrument', 'anestes']);
$tipos = procedure_types();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'La sesión del formulario expiró. Intentá nuevamente.';
    } elseif (isset($_POST['action']) && $_POST['action'] === 'eliminar') {
        $result = delete_cirugia((int)($_POST['id'] ?? 0));
        if ($result['ok']) { $ok = 'Cirugía eliminada.'; } else { $error = $result['message']; }
    } else {
        $personal = [
            ['id_usuario' => (int)($_POST['cirujano'] ?? 0), 'rol' => 'Cirujano/a'],
            ['id_usuario' => (int)($_POST['instrumentador'] ?? 0), 'rol' => 'Instrumentador/a'],
            ['id_usuario' => (int)($_POST['anestesia'] ?? 0), 'rol' => 'Técnico/a en anestesia'],
            ['id_usuario' => (int)($_POST['otro'] ?? 0), 'rol' => 'Otro personal'],
        ];
        $result = create_cirugia($_POST, $personal);
        if ($result['ok']) { $ok = 'Cirugía registrada.'; } else { $error = $result['message']; }
    }
}

$mes = isset($_GET['mes']) && preg_match('/^\d{4}-\d{2}$/', $_GET['mes']) ? $_GET['mes'] : date('Y-m');
$cirugias = list_cirugias($mes);
require __DIR__ . '/partials/header.php';
?>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<?php if ($ok): ?><div class="alert success"><?= e($ok) ?></div><?php endif; ?>

<section class="section-head"><h2>Registrar cirugía</h2></section>
<section class="form-panel"><form method="post" autocomplete="off">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="form-grid">
    <div><label>Fecha</label><input type="date" name="fecha" value="<?= e($_POST['fecha'] ?? date('Y-m-d')) ?>" required></div>
    <div><label>Hora de inicio</label><input type="time" name="hora_inicio" value="<?= e($_POST['hora_inicio'] ?? '09:00') ?>" required></div>
</div>
<div>
    <label>Tipo de procedimiento</label>
    <select name="tipo_procedimiento" required>
        <option value="">Seleccionar tipo de procedimiento</option>
        <?php foreach ($tipos as $tipo): ?>
        <option value="<?= (int)$tipo['id_tipo_proc'] ?>" <?= (string)($_POST['tipo_procedimiento'] ?? '') === (string)$tipo['id_tipo_proc'] ? 'selected' : '' ?>><?= e($tipo['nombre']) ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="divider"></div>
<label style="font-weight:700;margin-bottom:14px;">Personal participante</label>
<div class="form-grid">
    <div><label>Cirujano/a</label><select name="cirujano"><option value="">Seleccionar empleado</option><?php foreach ($cirujanos as $emp): ?><option value="<?= (int)$emp['id_usuario'] ?>" <?= (string)($_POST['cirujano'] ?? '') === (string)$emp['id_usuario'] ? 'selected' : '' ?>><?= e($emp['apellido'] . ', ' . $emp['nombre']) ?><?= $emp['especialidad'] ? ' — ' . e($emp['especialidad']) : '' ?></option><?php endforeach; ?></select></div>
    <div><label>Instrumentador/a</label><select name="instrumentador"><option value="">Seleccionar empleado</option><?php foreach ($instrumentadores as $emp): ?><option value="<?= (int)$emp['id_usuario'] ?>" <?= (string)($_POST['instrumentador'] ?? '') === (string)$emp['id_usuario'] ? 'selected' : '' ?>><?= e($emp['apellido'] . ', ' . $emp['nombre']) ?><?= $emp['especialidad'] ? ' — ' . e($emp['especialidad']) : '' ?></option><?php endforeach; ?></select></div>
    <div><label>Técnico/a en anestesia</label><select name="anestesia"><option value="">Seleccionar empleado</option><?php foreach ($anestesistas as $emp): ?><option value="<?= (int)$emp['id_usuario'] ?>" <?= (string)($_POST['anestesia'] ?? '') === (string)$emp['id_usuario'] ? 'selected' : '' ?>><?= e($emp['apellido'] . ', ' . $emp['nombre']) ?><?= $emp['especialidad'] ? ' — ' . e($emp['especialidad']) : '' ?></option><?php endforeach; ?></select></div>
    <div><label>Otro personal (opcional)</label><select name="otro"><option value="">Seleccionar empleado</option><?php foreach ($otros as $emp): ?><option value="<?= (int)$emp['id_usuario'] ?>" <?= (string)($_POST['otro'] ?? '') === (string)$emp['id_usuario'] ? 'selected' : '' ?>><?= e($emp['apellido'] . ', ' . $emp['nombre']) ?><?= $emp['especialidad'] ? ' — ' . e($emp['especialidad']) : '' ?></option><?php endforeach; ?></select></div>
</div>
<div><label>Observaciones</label><textarea name="observaciones" rows="2"><?= e($_POST['observaciones'] ?? '') ?></textarea></div>
<button class="btn primary btn-add" type="submit"><?= icon('plus', 18) ?>Guardar cirugía</button>
</form></section>

<div class="section-head" style="margin-top:34px;"><h2>Cirugías registradas</h2>
<form class="filters" method="get" style="margin:0;"><input type="month" name="mes" value="<?= e($mes) ?>"><button class="small-btn" type="submit"><?= icon('calendar') ?>Ver mes</button></form>
</div>
<section class="panel"><div class="table-wrap"><table>
<thead><tr><th>Fecha</th><th>Hora</th><th>Procedimiento</th><th>Personal</th><th>Observaciones</th><th></th></tr></thead>
<tbody>
<?php foreach ($cirugias as $cir): ?>
<tr>
    <td><?= e($cir['fecha']) ?></td>
    <td><?= e($cir['hora_inicio']) ?></td>
    <td><?= e($cir['procedimiento']) ?></td>
    <td><?= e($cir['personal']) ?></td>
    <td><?= e($cir['observaciones']) ?></td>
    <td class="actions-cell"><form class="inline-form" method="post" onsubmit="return confirm('¿Eliminar esta cirugía?');"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="eliminar"><input type="hidden" name="id" value="<?= (int)$cir['id_cirugia'] ?>"><button type="submit" class="act-del"><?= icon('trash') ?>Eliminar</button></form></td>
</tr>
<?php endforeach; ?>
<?php if (!$cirugias): ?><tr><td colspan="6">No hay cirugías registradas en este mes.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php require __DIR__ . '/partials/footer.php'; ?>