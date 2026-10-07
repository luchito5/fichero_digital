<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';
$page_title = 'Vacaciones / ART';
$error = '';
$ok = '';

$empleados = active_employees();
$editar = null;
$edit_id = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = (string)($_POST['action'] ?? '');
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'La sesión del formulario expiró. Intentá nuevamente.';
        if ($accion === 'editar') $edit_id = (int)($_POST['id'] ?? 0);
    } elseif ($accion === 'eliminar') {
        $result = delete_novedad((int)($_POST['id'] ?? 0));
        if ($result['ok']) { $ok = 'Novedad eliminada.'; } else { $error = $result['message']; }
    } elseif ($accion === 'editar') {
        $edit_id = (int)($_POST['id'] ?? 0);
        $result = update_novedad($edit_id, $_POST);
        if ($result['ok']) { $ok = 'Novedad actualizada.'; $edit_id = 0; } else { $error = $result['message']; }
    } else {
        $result = create_novedad($_POST);
        if ($result['ok']) { $ok = 'Novedad registrada.'; } else { $error = $result['message']; }
    }
} elseif (isset($_GET['editar'])) {
    $edit_id = (int)$_GET['editar'];
}

if ($edit_id > 0) {
    $editar = get_novedad($edit_id);
    if ($editar === null) { $error = 'La novedad indicada no existe.'; $edit_id = 0; }
}

$val = function (string $campo, string $default = '') use ($editar): string {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST[$campo])) return (string)$_POST[$campo];
    if ($editar !== null && array_key_exists($campo, $editar)) return (string)$editar[$campo];
    return $default;
};

$desde_ts = strtotime($val('fecha_desde', date('Y-m-d')));
$min_hasta = $desde_ts ? date('Y-m-d', $desde_ts + 86400) : date('Y-m-d');
$novedades = list_novedades();
require __DIR__ . '/partials/header.php';
?>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<?php if ($ok): ?><div class="alert success"><?= e($ok) ?></div><?php endif; ?>

<section class="section-head"><h2><?= $editar ? 'Editar novedad' : 'Registrar novedad' ?></h2></section>
<section class="form-panel"><form method="post" autocomplete="off">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<?php if ($editar): ?>
<input type="hidden" name="action" value="editar">
<input type="hidden" name="id" value="<?= (int)$editar['id_novedad'] ?>">
<?php endif; ?>
<div class="form-grid">
    <div><label>Empleado</label><select name="id_usuario" required><option value="" hidden>Seleccionar empleado</option><?php foreach ($empleados as $emp): ?><option value="<?= (int)$emp['id_usuario'] ?>" <?= $val('id_usuario') === (string)$emp['id_usuario'] ? 'selected' : '' ?>><?= e($emp['apellido'] . ', ' . $emp['nombre']) ?></option><?php endforeach; ?></select></div>
    <div><label>Tipo de novedad</label><select name="tipo" required><option value="" hidden>Seleccionar tipo</option><?php foreach (['Vacaciones', 'Licencia', 'ART', 'Capacitación'] as $tipo): ?><option value="<?= e($tipo) ?>" <?= $val('tipo') === $tipo ? 'selected' : '' ?>><?= e($tipo) ?></option><?php endforeach; ?></select></div>
    <div><label>Fecha desde</label><input type="date" id="fecha_desde" name="fecha_desde" value="<?= e($val('fecha_desde', date('Y-m-d'))) ?>" required></div>
    <div><label>Fecha hasta</label><input type="date" id="fecha_hasta" name="fecha_hasta" min="<?= e($min_hasta) ?>" value="<?= e($val('fecha_hasta')) ?>"></div>
</div>
<div><label>Observaciones (informativo)</label><textarea name="observaciones" rows="2"><?= e($val('observaciones')) ?></textarea></div>
<?php if ($editar): ?><div class="form-actions"><?php endif; ?>
<button class="btn primary btn-add" type="submit"><?= icon($editar ? 'save' : 'plus', 18) ?><?= $editar ? 'Guardar cambios' : 'Guardar novedad' ?></button>
<?php if ($editar): ?><a class="small-btn" href="vacaciones.php">Cancelar</a></div><?php endif; ?>
</form></section>

<div class="section-head" style="margin-top:34px;"><h2>Historial de novedades</h2></div>
<section class="panel"><div class="table-wrap"><table>
<thead><tr><th>Empleado</th><th>Tipo</th><th aria-sort="ascending">Desde</th><th>Hasta</th><th>Observaciones</th><th></th></tr></thead>
<tbody>
<?php foreach ($novedades as $nov): ?>
<tr>
    <td><?= e($nov['nombre'] . ' ' . $nov['apellido']) ?></td>
    <td>
        <?php
            $tipo = (string)$nov['tipo'];
            $tipo_clases = ['Vacaciones' => 'vacaciones', 'Licencia' => 'licencia', 'ART' => 'art', 'Capacitación' => 'capacitacion'];
            $cls = $tipo_clases[$tipo] ?? 'otros';
        ?>
        <span class="tag bubble <?= $cls ?>"><?= e($tipo) ?></span>
    </td>
    <td><?= e($nov['fecha_desde'] ? date('d/m/Y', strtotime($nov['fecha_desde'])) : '—') ?></td>
    <td><?= e($nov['fecha_hasta'] ? date('d/m/Y', strtotime($nov['fecha_hasta'])) : '—') ?></td>
    <td><?= e($nov['observaciones']) ?></td>
    <td class="actions-cell"><div class="row-actions">
        <a class="act-edit" href="vacaciones.php?editar=<?= (int)$nov['id_novedad'] ?>"><?= icon('edit') ?>Editar</a>
        <form id="del-nov-<?= (int)$nov['id_novedad'] ?>" class="inline-form" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="eliminar"><input type="hidden" name="id" value="<?= (int)$nov['id_novedad'] ?>"><button type="button" class="act-del" data-confirm-delete data-confirm-title="¿Eliminar esta novedad?" data-confirm-message="Esta acción es <strong>irreversible</strong> y no se puede deshacer."><?= icon('trash') ?>Eliminar</button></form>
    </div></td>
</tr>
<?php endforeach; ?>
<?php if (!$novedades): ?><tr><td colspan="6">No hay novedades registradas.</td></tr><?php endif; ?>
</tbody></table></div></section>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const desde = document.getElementById('fecha_desde');
    const hasta = document.getElementById('fecha_hasta');
    if (!desde || !hasta) return;
    const limite = () => {
        if (!desde.value) return '';
        const [a, m, d] = desde.value.split('-').map(Number);
        const dia = new Date(Date.UTC(a, m - 1, d + 1));
        return dia.toISOString().slice(0, 10);
    };
    const aplicar = () => {
        const min = limite();
        if (!min) { hasta.removeAttribute('min'); return; }
        hasta.min = min;
        if (hasta.value && hasta.value < min) hasta.value = min;
    };
    desde.addEventListener('change', aplicar);
    hasta.addEventListener('change', () => { if (hasta.min && hasta.value && hasta.value < hasta.min) hasta.value = hasta.min; });
    aplicar();
});
</script>
<?php require __DIR__ . '/partials/footer.php'; ?>
