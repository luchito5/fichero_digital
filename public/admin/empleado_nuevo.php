<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';
$page_title='Nuevo empleado';
$types=personal_types(); $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verify_csrf($_POST['csrf_token']??'')) $error='La sesión del formulario expiró. Intentá nuevamente.';
    else { $result=create_employee($_POST); if($result['ok']){ header('Location: empleados.php?creado=1'); exit; } $error=$result['message']; }
}
require __DIR__ . '/partials/header.php';
?>
<?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<section class="form-panel">
<form method="post" autocomplete="off">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="form-grid">
<div><label>Nombre</label><input name="nombre" value="<?= e($_POST['nombre']??'') ?>" required placeholder="Ej: María"></div>
<div><label>Apellido</label><input name="apellido" value="<?= e($_POST['apellido']??'') ?>" required placeholder="Ej: González"></div>
<div><label>Rol / Especialidad</label><select name="id_tipo_personal" required><option value="">Seleccionar rol</option><?php foreach($types as $type): ?><option value="<?= (int)$type['id_tipo'] ?>" <?= (string)($_POST['id_tipo_personal']??'')===(string)$type['id_tipo']?'selected':'' ?>><?= e($type['nombre']) ?></option><?php endforeach; ?></select></div>
</div>
<div class="divider"></div>
<div>
<label>Tipo de contrato</label>
<div class="contract-options">
<label class="tag fixed"><input type="radio" name="tipo_contrato" value="Fijo" <?= ($_POST['tipo_contrato']??'Fijo')==='Fijo'?'checked':'' ?>> Fijo</label>
<label class="tag hour"><input type="radio" name="tipo_contrato" value="Por Hora" <?= ($_POST['tipo_contrato']??'')==='Por Hora'?'checked':'' ?>> Por Hora</label>
<label class="tag surgery"><input type="radio" name="tipo_contrato" value="Por Cirugía" <?= ($_POST['tipo_contrato']??'')==='Por Cirugía'?'checked':'' ?>> Por Cirugía</label>
<label class="tag rental"><input type="radio" name="tipo_contrato" value="Alquiler" <?= ($_POST['tipo_contrato']??'')==='Alquiler'?'checked':'' ?>> Alquiler</label>
<label class="tag admin"><input type="radio" name="tipo_contrato" value="Admin" <?= ($_POST['tipo_contrato']??'')==='Admin'?'checked':'' ?>> Admin</label>
</div>
</div>
<div class="divider"></div>
<div class="form-grid">
<div><label>DNI (usuario de acceso)</label><input name="dni" inputmode="numeric" maxlength="20" value="<?= e($_POST['dni']??'') ?>" required placeholder="Ej: 30123456"></div>
<div><label>Contraseña temporal</label><input type="password" name="password" required minlength="8" placeholder="Mínimo 8 caracteres"></div>
</div>
<button class="btn primary btn-add" type="submit"><?= icon('plus', 18) ?>Crear empleado</button>
</form></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
