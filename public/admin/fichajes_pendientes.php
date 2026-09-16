<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';
$page_title = 'Fichajes pendientes';
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
            $r = corregir_entrada($idFichaje, (string)($_POST['entrada'] ?? ''), (string)($_POST['obs'] ?? ''), (int)$user['id']);
            if ($r['ok']) { $ok = 'Entrada corregida y validada.'; } else { $error = $r['message']; }
        } elseif ($action === 'corregir') {
            $r = corregir_fichaje($idFichaje, (string)($_POST['entrada'] ?? ''), (string)($_POST['salida'] ?? ''), (string)($_POST['obs'] ?? ''), (int)$user['id']);
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

<div class="section-head"><h2>Fichajes pendientes de salida</h2>
<?php if ($demorados > 0): ?><span class="status delayed"><?= $demorados ?> con más de 12 h</span><?php endif; ?>
</div>
<section class="panel"><div class="table-wrap"><table>
<thead><tr><th>Empleado</th><th>Contrato</th><th>DNI</th><th>Fecha</th><th>Entrada</th><th>Tiempo sin salida</th><th></th></tr></thead>
<tbody>
<?php foreach ($pendientes as $f): $dem = (int)$f['demorado'] === 1; ?>
<tr<?= $dem ? ' class="fichaje-delayed"' : '' ?>>
    <td><?= e($f['nombre'] . ' ' . $f['apellido']) ?></td>
    <td><span class="tag <?= e(contract_class($f['tipo_contrato'])) ?>"><?= e($f['tipo_contrato']) ?></span></td>
    <td><?= e($f['dni']) ?></td>
    <td><?= e($f['fecha']) ?></td>
    <td><?= e($f['hora_entrada']) ?></td>
    <td><span class="status <?= $dem ? 'delayed' : 'active' ?>"><?= e(number_format((float)$f['horas_abiertas'], 1, ',', '.')) ?> h</span></td>
    <td class="actions-cell">
        <form class="inline-form" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="validar"><input type="hidden" name="id" value="<?= (int)$f['id_fichaje'] ?>"><button class="small-btn" type="submit" title="Validar entrada como correcta"><?= icon('check', 13) ?>Validar</button></form>
        <details class="inline-corregir"><summary><?= icon('edit', 13) ?>Corregir</summary>
        <form class="corregir-form" method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="id" value="<?= (int)$f['id_fichaje'] ?>">
            <div><label>Entrada</label><input type="time" name="entrada" value="<?= e($f['hora_entrada']) ?>" required></div>
            <div><label>Salida (vacío = mantener abierto)</label><input type="time" name="salida" value=""></div>
            <div><label>Observación</label><input name="obs" placeholder="Motivo de la corrección..." maxlength="255"></div>
            <div class="corregir-actions">
                <button type="submit" name="action" value="corregir_entrada" class="small-btn" title="Solo corregir entrada, fichaje queda abierto"><?= icon('save', 13) ?>Solo entrada</button>
                <button type="submit" name="action" value="corregir" class="btn primary" title="Establecer salida y cerrar fichaje"><?= icon('check', 14) ?>Cerrar fichaje</button>
            </div>
        </form>
        </details>
    </td>
</tr>
<?php endforeach; ?>
<?php if (!$pendientes): ?><tr><td colspan="7">No hay fichajes pendientes de salida.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php require __DIR__ . '/partials/footer.php'; ?>