<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';
$page_title = 'Alquileres';
$error = '';
$ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'La sesión del formulario expiró. Intentá nuevamente.';
    } elseif (isset($_POST['action']) && $_POST['action'] === 'eliminar') {
        $result = delete_alquiler((int)($_POST['id'] ?? 0));
        if ($result['ok']) { $ok = 'Alquiler eliminado.'; } else { $error = $result['message']; }
    } else {
        $result = create_alquiler($_POST);
        if ($result['ok']) { $ok = 'Alquiler registrado.'; } else { $error = $result['message']; }
    }
}

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
    <td><?= e($alq['hora_salida']) ?></td>
    <td><?= e($alq['horas_uso'] !== null ? (float)$alq['horas_uso'] . ' h' : '') ?></td>
    <td><?= e($alq['dato_facturacion']) ?></td>
    <td class="actions-cell"><form class="inline-form" method="post" onsubmit="return confirm('¿Eliminar este alquiler?');"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="eliminar"><input type="hidden" name="id" value="<?= (int)$alq['id_alquiler'] ?>"><button type="submit" class="act-del"><?= icon('trash') ?>Eliminar</button></form></td>
</tr>
<?php endforeach; ?>
<?php if (!$alquileres): ?><tr><td colspan="8">No se registraron alquileres.</td></tr><?php endif; ?>
</tbody></table></div></section>

<div class="section-head" style="margin-top:34px;"><h2>Registrar nuevo alquiler</h2></div>
<section class="form-panel"><form method="post" autocomplete="off">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="form-grid">
    <div><label>Responsable externo · Nombre</label><input name="nombre_responsable" value="<?= e($_POST['nombre_responsable'] ?? '') ?>" required placeholder="Dr. Romero"></div>
    <div><label>Hora entrada</label><input type="time" name="hora_entrada" value="<?= e($_POST['hora_entrada'] ?? '08:00') ?>"></div>
    <div><label>Responsable externo · Institución</label><input name="institucion" value="<?= e($_POST['institucion'] ?? '') ?>" placeholder="Clínica Norte"></div>
    <div><label>Hora salida</label><input type="time" name="hora_salida" value="<?= e($_POST['hora_salida'] ?? '10:30') ?>"></div>
    <div style="grid-column:1"><label>Fecha</label><input type="date" name="fecha" value="<?= e($_POST['fecha'] ?? date('Y-m-d')) ?>" required></div>
</div>
<div><label>Datos de facturación (opcional)</label><textarea name="dato_facturacion" rows="2"><?= e($_POST['dato_facturacion'] ?? '') ?></textarea></div>
<button class="btn primary btn-add" type="submit"><?= icon('plus', 18) ?>Guardar alquiler</button>
</form></section>
<?php require __DIR__ . '/partials/footer.php'; ?>