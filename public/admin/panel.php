<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';
$page_title='Panel de administración';
$stats=admin_dashboard_stats();
$presentes=present_employees_today();
$demorados=fichajes_demorados_count();
require __DIR__ . '/partials/header.php';
?>
<?php if ($demorados > 0): ?>
<a class="pending-badge danger" href="fichajes_pendientes.php">
    <span class="pending-badge-dot"></span>
    <span><strong><?= $demorados ?></strong> empleado(s) con entrada registrada hace más de 12 horas sin registrar salida</span>
    <span class="pending-badge-go"><?= icon('edit', 13) ?>Validar / corregir</span>
</a>
<?php endif; ?>
<section class="cards admin-stats">
    <article class="stat"><span>Personal total</span><strong><?= $stats['personal_total'] ?></strong></article>
    <article class="stat"><span>Presentes hoy</span><strong><?= $stats['presentes_hoy'] ?></strong></article>
    <article class="stat"><span>Cirugías hoy</span><strong><?= $stats['cirugias_hoy'] ?></strong></article>
    <article class="stat"><span>Alquileres hoy</span><strong><?= $stats['alquileres_hoy'] ?></strong></article>
</section>
<section class="panel">
<h2>Personal presente ahora</h2>
<div class="table-wrap"><table>
<thead><tr><th>Nombre</th><th>Tipo</th><th>Entrada</th><th>Estado</th></tr></thead>
<tbody>
<?php foreach ($presentes as $p): ?>
<tr>
<td><?= e($p['nombre'].' '.$p['apellido']) ?></td>
<td><span class="tag <?= e(contract_class($p['tipo_contrato'])) ?>"><?= e($p['tipo_contrato']) ?></span></td>
<td><?= e(substr($p['hora_entrada'],0,5)) ?></td>
<td><span class="status active">Presente</span></td>
</tr>
<?php endforeach; ?>
<?php if (!$presentes): ?><tr><td colspan="4">No hay empleados presentes en este momento.</td></tr><?php endif; ?>
</tbody></table></div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
