<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';
$page_title = 'Vacaciones / ART';
$error = '';
$ok = '';

$empleados = active_employees();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'La sesión del formulario expiró. Intentá nuevamente.';
    } elseif (isset($_POST['action']) && $_POST['action'] === 'eliminar') {
        $result = delete_novedad((int)($_POST['id'] ?? 0));
        if ($result['ok']) { $ok = 'Novedad eliminada.'; } else { $error = $result['message']; }
    } else {
        $result = create_novedad($_POST);
        if ($result['ok']) { $ok = 'Novedad registrada.'; } else { $error = $result['message']; }
    }
}

$novedades = list_novedades();
require __DIR__ . '/partials/header.php';
?>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<?php if ($ok): ?><div class="alert success"><?= e($ok) ?></div><?php endif; ?>

<section class="section-head"><h2>Registrar novedad</h2></section>
<section class="form-panel"><form method="post" autocomplete="off">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="form-grid">
    <div><label>Empleado</label><select name="id_usuario" required><option value="">Seleccionar empleado</option><?php foreach ($empleados as $emp): ?><option value="<?= (int)$emp['id_usuario'] ?>" <?= (string)($_POST['id_usuario'] ?? '') === (string)$emp['id_usuario'] ? 'selected' : '' ?>><?= e($emp['apellido'] . ', ' . $emp['nombre']) ?></option><?php endforeach; ?></select></div>
    <div><label>Tipo de novedad</label><select name="tipo" required><option value="">Seleccionar tipo</option><?php foreach (['Vacaciones', 'Licencia', 'ART', 'Capacitación'] as $tipo): ?><option value="<?= e($tipo) ?>" <?= ($_POST['tipo'] ?? '') === $tipo ? 'selected' : '' ?>><?= e($tipo) ?></option><?php endforeach; ?></select></div>
    <div><label>Fecha desde</label><input type="date" name="fecha_desde" value="<?= e($_POST['fecha_desde'] ?? date('Y-m-d')) ?>" required></div>
    <div><label>Fecha hasta</label><input type="date" name="fecha_hasta" value="<?= e($_POST['fecha_hasta'] ?? '') ?>"></div>
</div>
<div><label>Observaciones (informativo)</label><textarea name="observaciones" rows="2"><?= e($_POST['observaciones'] ?? '') ?></textarea></div>
<button class="btn primary btn-add" type="submit"><?= icon('plus', 18) ?>Guardar novedad</button>
</form></section>

<div class="section-head" style="margin-top:34px;"><h2>Historial de novedades</h2></div>
<section class="panel"><div class="table-wrap"><table>
<thead><tr><th>Empleado</th><th>Tipo</th><th>Desde</th><th>Hasta</th><th>Observaciones</th><th></th></tr></thead>
<tbody>
<?php foreach ($novedades as $nov): ?>
<tr>
    <td><?= e($nov['nombre'] . ' ' . $nov['apellido']) ?></td>
    <td><span class="tag hour"><?= e($nov['tipo']) ?></span></td>
    <td><?= e($nov['fecha_desde']) ?></td>
    <td><?= e($nov['fecha_hasta']) ?></td>
    <td><?= e($nov['observaciones']) ?></td>
    <td class="actions-cell"><form class="inline-form" method="post" onsubmit="return confirm('¿Eliminar esta novedad?');"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="eliminar"><input type="hidden" name="id" value="<?= (int)$nov['id_novedad'] ?>"><button type="submit" class="act-del"><?= icon('trash') ?>Eliminar</button></form></td>
</tr>
<?php endforeach; ?>
<?php if (!$novedades): ?><tr><td colspan="6">No hay novedades registradas.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php require __DIR__ . '/partials/footer.php'; ?>