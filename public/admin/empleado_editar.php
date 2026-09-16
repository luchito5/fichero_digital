<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';
$id=(int)($_GET['id']??$_POST['id']??0); $employee=find_employee($id); $types=personal_types(); $error=''; $page_title='Editar empleado';
if(!$employee){ header('Location: empleados.php'); exit; }
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verify_csrf($_POST['csrf_token']??'')) $error='La sesión del formulario expiró. Intentá nuevamente.';
    else { $result=update_employee($id,$_POST); if($result['ok']){ header('Location: empleados.php?actualizado=1'); exit; } $error=$result['message']; }
}
require __DIR__ . '/partials/header.php';
?>
<?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<section class="form-panel"><form method="post" autocomplete="off">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= $id ?>">
<div class="form-grid">
<div><label>Nombre</label><input name="nombre" value="<?= e($_POST['nombre']??$employee['nombre']) ?>" required></div>
<div><label>Apellido</label><input name="apellido" value="<?= e($_POST['apellido']??$employee['apellido']) ?>" required></div>
<div><label>Rol / Especialidad</label><select name="id_tipo_personal" required><?php foreach($types as $type): ?><option value="<?= (int)$type['id_tipo'] ?>" <?= (string)($_POST['id_tipo_personal']??$employee['id_tipo_personal'])===(string)$type['id_tipo']?'selected':'' ?>><?= e($type['nombre']) ?></option><?php endforeach; ?></select></div>
</div>
<div class="divider"></div>
<div>
<label>Tipo de contrato</label>
<div class="contract-options">
<label class="tag fixed"><input type="radio" name="tipo_contrato" value="Fijo" <?= ($_POST['tipo_contrato']??$employee['tipo_contrato'])==='Fijo'?'checked':'' ?>> Fijo</label>
<label class="tag hour"><input type="radio" name="tipo_contrato" value="Por Hora" <?= ($_POST['tipo_contrato']??$employee['tipo_contrato'])==='Por Hora'?'checked':'' ?>> Por Hora</label>
<label class="tag surgery"><input type="radio" name="tipo_contrato" value="Por Cirugía" <?= ($_POST['tipo_contrato']??$employee['tipo_contrato'])==='Por Cirugía'?'checked':'' ?>> Por Cirugía</label>
<label class="tag rental"><input type="radio" name="tipo_contrato" value="Alquiler" <?= ($_POST['tipo_contrato']??$employee['tipo_contrato'])==='Alquiler'?'checked':'' ?>> Alquiler</label>
<label class="tag admin"><input type="radio" name="tipo_contrato" value="Admin" <?= ($_POST['tipo_contrato']??$employee['tipo_contrato'])==='Admin'?'checked':'' ?>> Admin</label>
</div>
</div>
<div class="divider"></div>
<div class="form-grid3">
<div><label>DNI</label><input name="dni" value="<?= e($_POST['dni']??$employee['dni']) ?>" required></div>
<div><label>Nueva contraseña (opcional)</label><input type="password" name="password" minlength="8" placeholder="Dejar vacío para mantenerla"></div>
</div>
<button class="btn primary" type="submit"><?= icon('save', 18) ?>Guardar cambios</button>
</form></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
