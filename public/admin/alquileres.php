<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';
$page_title = 'Alquileres';
$error = '';
$ok = '';

$editar = null;
$edit_id = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = (string)($_POST['action'] ?? '');
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'La sesión del formulario expiró. Intentá nuevamente.';
        if ($accion === 'editar') $edit_id = (int)($_POST['id'] ?? 0);
    } elseif ($accion === 'eliminar') {
        $result = delete_alquiler((int)($_POST['id'] ?? 0));
        if ($result['ok']) { $ok = 'Alquiler eliminado.'; } else { $error = $result['message']; }
    } elseif ($accion === 'confirmar') {
        $result = confirmar_salida_alquiler((int)($_POST['id'] ?? 0), (string)($_POST['hora_salida'] ?? ''));
        if ($result['ok']) { $ok = 'Hora de salida confirmada: ' . number_format((float)$result['horas'], 2, ',', '.') . ' h de uso.'; }
        else { $error = $result['message']; }
    } elseif ($accion === 'editar') {
        $edit_id = (int)($_POST['id'] ?? 0);
        $result = update_alquiler($edit_id, $_POST);
        if ($result['ok']) { $ok = 'Alquiler actualizado.'; $edit_id = 0; } else { $error = $result['message']; }
    } else {
        $result = create_alquiler($_POST);
        if ($result['ok']) { $ok = 'Alquiler registrado.'; } else { $error = $result['message']; }
    }
} elseif (isset($_GET['editar'])) {
    $edit_id = (int)$_GET['editar'];
}

if ($edit_id > 0) {
    $editar = get_alquiler($edit_id);
    if ($editar === null) { $error = 'El alquiler indicado no existe.'; $edit_id = 0; }
}

$val = function (string $campo, string $default = '') use ($editar): string {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST[$campo])) return (string)$_POST[$campo];
    if ($editar !== null && array_key_exists($campo, $editar)) return (string)$editar[$campo];
    return $default;
};

$stats = alquileres_stats();
$alquileres = list_alquileres();
require __DIR__ . '/partials/header.php';
?>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<?php if ($ok): ?><div class="alert success"><?= e($ok) ?></div><?php endif; ?>

<section class="cards admin-stats">
    <article class="stat"><span>Alquileres hoy</span><strong><?= $stats['hoy'] ?></strong></article>
    <article class="stat"><span>Alquileres este mes</span><strong><?= $stats['mes'] ?></strong></article>
    <article class="stat"><span>Total horas cedidas</span><strong><?= e((string)$stats['hs_mes']) ?> h</strong></article>
    <article class="stat"><span>Salidas sin confirmar</span><strong><?= (int)$stats['pendientes'] ?></strong></article>
</section>

<div class="section-head"><h2>Registro de alquileres</h2></div>
<section class="panel"><div class="table-wrap"><table>
<thead><tr><th>Responsable</th><th>Institución</th><th>Fecha</th><th>Entrada</th><th>Salida</th><th>Hs uso</th><th>Facturación</th><th></th></tr></thead>
<tbody>
<?php foreach ($alquileres as $alq): ?>
<tr>
    <td><?= e($alq['responsable']) ?></td>
    <td><?= e($alq['institucion']) ?></td>
    <td><?= e($alq['fecha']) ?></td>
    <td><?= e($alq['hora_entrada']) ?></td>
    <td>
        <?php if ((int)$alq['hora_salida_confirmada'] === 1): ?>
            <span class="salida-ok" title="Hora exacta confirmada"><?= e($alq['hora_salida']) ?><?= icon('check', 13) ?></span>
        <?php else: ?>
            <form class="inline-form salida-form" method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="confirmar">
                <input type="hidden" name="id" value="<?= (int)$alq['id_alquiler'] ?>">
                <input type="time" name="hora_salida" value="<?= e($alq['hora_salida'] !== null ? $alq['hora_salida'] : date('H:i')) ?>" required>
                <button type="submit" class="small-btn" title="Confirmar hora exacta de salida"><?= icon('check', 12) ?>Confirmar</button>
            </form>
            <span class="tag pendiente">aprox.</span>
        <?php endif; ?>
    </td>
    <td><?= e($alq['horas_uso'] !== null ? (float)$alq['horas_uso'] . ' h' : '') ?></td>
    <td><?= e($alq['dato_facturacion']) ?></td>
    <td class="actions-cell"><div class="row-actions">
        <a class="act-edit" href="alquileres.php?editar=<?= (int)$alq['id_alquiler'] ?>#form-alquiler"><?= icon('edit') ?>Editar</a>
        <form class="inline-form" method="post" onsubmit="return confirm('¿Eliminar este alquiler?');"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="eliminar"><input type="hidden" name="id" value="<?= (int)$alq['id_alquiler'] ?>"><button type="submit" class="act-del"><?= icon('trash') ?>Eliminar</button></form>
    </div></td>
</tr>
<?php endforeach; ?>
<?php if (!$alquileres): ?><tr><td colspan="8">No se registraron alquileres.</td></tr><?php endif; ?>
</tbody></table></div></section>

<div class="section-head" style="margin-top:34px;"><h2 id="form-alquiler"><?= $editar ? 'Editar alquiler' : 'Registrar nuevo alquiler' ?></h2></div>
<p class="help" style="text-align:left;margin:0 0 10px;">Cargá la hora de salida aunque sea aproximada. Cuando terminen de usar el quirófano, confirmá la hora exacta desde la fila de la tabla para que las horas de uso sean las reales.</p>
<section class="form-panel"><form method="post" autocomplete="off">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<?php if ($editar): ?>
<input type="hidden" name="action" value="editar">
<input type="hidden" name="id" value="<?= (int)$editar['id_alquiler'] ?>">
<?php endif; ?>
<div class="form-grid">
    <div><label>Responsable externo · Nombre</label><input name="nombre_responsable" value="<?= e($val('nombre_responsable')) ?>" required placeholder="Dr. Romero"></div>
    <div><label>Hora entrada</label><input type="time" name="hora_entrada" value="<?= e($val('hora_entrada', '08:00')) ?>"></div>
    <div><label>Responsable externo · Institución</label><input name="institucion" value="<?= e($val('institucion')) ?>" placeholder="Clínica Norte"></div>
    <div><label>Hora salida <small class="hint-inline">aproximada al registrar</small></label><input type="time" name="hora_salida" value="<?= e($val('hora_salida', '10:30')) ?>"></div>
    <div style="grid-column:1"><label>Fecha</label><input type="date" name="fecha" value="<?= e($val('fecha', date('Y-m-d'))) ?>" required></div>
</div>
<div><label>Datos de facturación (opcional)</label><textarea name="dato_facturacion" rows="2"><?= e($val('dato_facturacion')) ?></textarea></div>
<?php if ($editar): ?><div class="form-actions"><?php endif; ?>
<button class="btn primary btn-add" type="submit"><?= icon($editar ? 'save' : 'plus', 18) ?><?= $editar ? 'Guardar cambios' : 'Guardar alquiler' ?></button>
<?php if ($editar): ?><a class="small-btn" href="alquileres.php">Cancelar</a></div><?php endif; ?>
</form></section>
<?php require __DIR__ . '/partials/footer.php'; ?>