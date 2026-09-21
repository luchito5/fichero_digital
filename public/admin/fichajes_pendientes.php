<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';
$page_title = 'Fichajes pendientes de salida';
$error = '';
$ok = '';
$user = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'La sesión del formulario expiró. Intentá nuevamente.';
    } else {
        $action = $_POST['action'] ?? '';
        $idFichaje = (int)($_POST['id'] ?? 0);
        if ($action === 'validar') {
            $r = validar_fichaje($idFichaje, (int)$user['id']);
            if ($r['ok']) { $ok = 'Fichaje validado.'; } else { $error = $r['message']; }
        } elseif ($action === 'corregir_entrada') {
            $r = corregir_entrada($idFichaje, (string)($_POST['entrada'] ?? ''), (int)$user['id']);
            if ($r['ok']) { $ok = 'Entrada corregida y validada.'; } else { $error = $r['message']; }
        } elseif ($action === 'corregir') {
            $r = corregir_fichaje($idFichaje, (string)($_POST['entrada'] ?? ''), (string)($_POST['salida'] ?? ''), (int)$user['id']);
            if ($r['ok']) { $ok = 'Fichaje cerrado y validado.'; } else { $error = $r['message']; }
        }
    }
}

$pendientes = fichajes_sin_salida();
$demorados = fichajes_demorados_count();
require __DIR__ . '/partials/header.php';
?>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<?php if ($ok): ?><div class="alert success"><?= e($ok) ?></div><?php endif; ?>

<div class="section-head">
    <div>
        <h2>Fichajes pendientes de salida</h2>
        <p class="section-sub">Fichajes con entrada registrada y sin salida confirmada. Los que superan las 12 h se resaltan y son prioritarios. La validación de todos los fichajes (pendientes, aprobados y rechazados) se hace en el menú <strong>Validación</strong>.</p>
    </div>
    <?php if ($demorados > 0): ?><span class="status delayed"><?= $demorados ?> con más de 12 h</span><?php endif; ?>
</div>

<section class="cards">
    <article class="stat"><span>Pendientes de salida</span><strong><?= count($pendientes) ?></strong><small>fichajes sin salida</small></article>
    <article class="stat <?= $demorados > 0 ? 'stat-alert' : '' ?>"><span>Con más de 12 h</span><strong><?= $demorados ?></strong><small>requieren atención</small></article>
</section>

<section class="panel"><div class="table-wrap"><table>
<thead><tr><th>Empleado</th><th>Tipo de contrato</th><th>DNI</th><th>Fecha</th><th>Entrada</th><th>Tiempo sin salida</th><th>Acciones</th></tr></thead>
<tbody>
<?php foreach ($pendientes as $f): $dem = (int)$f['demorado'] === 1; $fid = (int)$f['id_fichaje']; ?>
<tr<?= $dem ? ' class="fichaje-delayed"' : '' ?>>
    <td><?= e($f['nombre'] . ' ' . $f['apellido']) ?></td>
    <td><span class="tag <?= e(contract_class($f['tipo_contrato'])) ?>"><?= e($f['tipo_contrato']) ?></span></td>
    <td><?= e($f['dni']) ?></td>
    <td><?= e($f['fecha']) ?></td>
    <td><?= e($f['hora_entrada']) ?></td>
    <td><span class="status <?= $dem ? 'delayed' : 'active' ?>"><?= e(number_format((float)$f['horas_abiertas'], 1, ',', '.')) ?> h</span></td>
    <td class="actions-cell">
        <div class="row-actions">
            <form class="inline-form" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="validar"><input type="hidden" name="id" value="<?= $fid ?>"><button class="small-btn" type="submit" title="Validar la entrada registrada como correcta"><?= icon('check', 13) ?>Validar</button></form>
            <button type="button" class="act corregir-toggle" data-row="corregir-<?= $fid ?>" aria-expanded="false" title="Corregir entrada y/o salida de este fichaje"><?= icon('edit', 13) ?>Corregir</button>
        </div>
    </td>
</tr>
<tr class="corregir-row" id="corregir-<?= $fid ?>">
    <td colspan="7">
        <div class="corregir-hint"><?= icon('info', 12) ?>Corregir solo la entrada mantiene el fichaje abierto. Cerrar fichaje registra la salida y valida el registro.</div>
        <form class="corregir-form" method="post" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="id" value="<?= $fid ?>">
            <div class="corregir-fields">
                <div class="corregir-field">
                    <label for="ent-<?= $fid ?>">Entrada</label>
                    <input type="time" id="ent-<?= $fid ?>" name="entrada" value="<?= e($f['hora_entrada']) ?>" required>
                </div>
                <div class="corregir-field">
                    <label for="sal-<?= $fid ?>">Salida <small>vacío = seguir abierto</small></label>
                    <input type="time" id="sal-<?= $fid ?>" name="salida" value="">
                </div>
            </div>
            <div class="corregir-actions">
                <button type="submit" name="action" value="corregir_entrada" class="small-btn" title="Corregir la entrada sin registrar salida"><?= icon('save', 13) ?>Solo entrada</button>
                <button type="submit" name="action" value="corregir" class="btn primary" title="Establecer la salida y validar el fichaje"><?= icon('check', 14) ?>Cerrar fichaje</button>
            </div>
        </form>
    </td>
</tr>
<?php endforeach; ?>
<?php if (!$pendientes): ?><tr><td colspan="7">No hay fichajes pendientes de salida. Todos registraron su salida.</td></tr><?php endif; ?>
</tbody></table></div></section>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggles = document.querySelectorAll('.corregir-toggle');
    if (!toggles.length) return;
    toggles.forEach((btn) => {
        btn.addEventListener('click', () => {
            const row = document.getElementById(btn.dataset.row);
            if (!row) return;
            const willOpen = !row.classList.contains('open');
            document.querySelectorAll('.corregir-row.open').forEach((r) => r.classList.remove('open'));
            document.querySelectorAll('.corregir-toggle.active').forEach((b) => { b.classList.remove('active'); b.setAttribute('aria-expanded', 'false'); });
            if (willOpen) {
                row.classList.add('open');
                btn.classList.add('active');
                btn.setAttribute('aria-expanded', 'true');
                const first = row.querySelector('.corregir-field input');
                if (first) first.focus();
            }
        });
    });
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        document.querySelectorAll('.corregir-row.open').forEach((r) => r.classList.remove('open'));
        document.querySelectorAll('.corregir-toggle.active').forEach((b) => { b.classList.remove('active'); b.setAttribute('aria-expanded', 'false'); });
    });
});
</script>
<?php require __DIR__ . '/partials/footer.php'; ?>