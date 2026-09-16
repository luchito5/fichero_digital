<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';
$page_title='Empleados';
$search=trim((string)($_GET['buscar']??''));
$contract=trim((string)($_GET['tipo']??''));
$employees=list_employees($search,$contract);
require __DIR__ . '/partials/header.php';
?>
<?php if(isset($_GET['eliminado'])): ?><div class="alert success">Empleado eliminado definitivamente.</div>
<?php elseif(isset($_GET['error'])): ?><div class="alert error">No se pudo completar la operación.</div>
<?php elseif(isset($_GET['estado'])): ?><div class="alert success">Estado del empleado actualizado.</div>
<?php endif; ?>
<div class="section-head"><h2>Empleados</h2><a class="small-btn btn-add" href="empleado_nuevo.php"><?= icon('plus') ?>Nuevo empleado</a></div>
<form class="filters" method="get">
<input type="search" name="buscar" placeholder="Buscar empleado..." value="<?= e($search) ?>">
<select name="tipo"><option value="">Todos los tipos</option><?php foreach(['Fijo','Por Hora','Por Cirugía','Alquiler','Admin'] as $type): ?><option value="<?= e($type) ?>" <?= $contract===$type?'selected':'' ?>><?= e($type) ?></option><?php endforeach; ?></select>
<button class="small-btn" type="submit"><?= icon('search') ?>Buscar</button>
</form>
<section class="panel"><div class="table-wrap"><table>
<thead><tr><th>Nombre</th><th>Tipo</th><th>DNI</th><th>Tipo de personal</th><th>Estado</th><th>Acciones</th></tr></thead>
<tbody>
<?php foreach($employees as $employee): ?>
<tr>
<td><?= e($employee['nombre'].' '.$employee['apellido']) ?></td>
<td><span class="tag <?= e(contract_class($employee['tipo_contrato'])) ?>"><?= e($employee['tipo_contrato']) ?></span></td>
<td><?= e($employee['dni']) ?></td>
<td><?= e($employee['tipo_personal']) ?></td>
<td><form method="post" action="empleado_estado.php" class="inline-form" title="Cambiar estado"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$employee['id_usuario'] ?>"><button type="submit" class="status-switch <?= (int)$employee['activo']===1?'on':'off' ?>"><span class="track"></span><?= (int)$employee['activo']===1?'Activo':'Inactivo' ?></button></form></td>
<td class="actions-cell"><a class="act-edit" href="empleado_editar.php?id=<?= (int)$employee['id_usuario'] ?>" title="Editar"><?= icon('edit') ?>Editar</a> <form method="post" action="empleado_eliminar.php" class="inline-form" onsubmit="return confirm('¿Eliminar definitivamente a <?= e($employee['nombre'].' '.$employee['apellido']) ?>? Se borrarán todos sus registros. Esta acción no se puede deshacer.');"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$employee['id_usuario'] ?>"><button type="submit" class="act-del" title="Eliminar definitivamente"><?= icon('trash') ?>Eliminar</button></form></td>
</tr>
<?php endforeach; ?>
<?php if(!$employees): ?><tr><td colspan="6">No se encontraron empleados.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
