<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';
$page_title = 'Configuración';
$error = '';
$ok = '';

$user = require_admin();
$tipos = db()->query('SELECT id_tipo_proc, nombre FROM tipos_procedimiento WHERE activo = 1 ORDER BY nombre')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'La sesión del formulario expiró. Intentá nuevamente.';
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'tipo_nuevo') {
            $result = create_procedure_type($_POST['nuevo_tipo'] ?? '');
            if ($result['ok']) { $ok = 'Tipo de procedimiento agregado.'; } else { $error = $result['message']; }
        } elseif ($action === 'tipo_eliminar') {
            $result = delete_procedure_type((int)($_POST['id'] ?? 0));
            if ($result['ok']) { $ok = 'Tipo de procedimiento eliminado.'; } else { $error = $result['message']; }
        } elseif ($action === 'password') {
            $nombreCompleto = trim((string)($_POST['nombre_completo'] ?? ''));
            $dni = trim((string)($_POST['dni'] ?? ''));
            $nueva = $_POST['nueva'] ?? '';
            $confirmar = $_POST['confirmar'] ?? '';

            $partes = preg_split('/\s+/', $nombreCompleto, 2);
            $nombre = $partes[0] ?? '';
            $apellido = $partes[1] ?? '';

            $err = '';
            if ($nombreCompleto === '') $err = 'Completá el nombre completo.';
            elseif ($dni === '') $err = 'Completá el DNI.';
            elseif ($nueva !== '' && $confirmar === '') $err = 'Tenés que confirmar tu contraseña';
            elseif ($nueva !== '' && strlen($nueva) < 8) $err = 'La nueva contraseña debe tener al menos 8 caracteres.';
            elseif ($nueva !== $confirmar) $err = 'Las contraseñas no coinciden.';

            if ($err !== '') {
                $error = $err;
            } else {
                try {
                    $stmt = db()->prepare('SELECT COUNT(*) FROM usuarios WHERE (dni = :d OR username = :u) AND id_usuario <> :id');
                    $stmt->execute(['d' => $dni, 'u' => $dni, 'id' => $user['id']]);
                    if ((int)$stmt->fetchColumn() > 0) {
                        $error = 'Ese DNI ya existe.';
                    } else {
                        $stmt = db()->prepare('UPDATE usuarios SET nombre = :n, apellido = :a, dni = :d, username = :d2 WHERE id_usuario = :id');
                        $stmt->execute(['n' => $nombre, 'a' => $apellido, 'd' => $dni, 'd2' => $dni, 'id' => $user['id']]);
                        if ($nueva !== '') {
                            db()->prepare('UPDATE usuarios SET password_hash = :p WHERE id_usuario = :id')->execute(['p' => password_hash($nueva, PASSWORD_DEFAULT), 'id' => $user['id']]);
                        }
                        $_SESSION['user']['nombre'] = $nombre;
                        $_SESSION['user']['apellido'] = $apellido;
                        $_SESSION['user']['dni'] = $dni;
                        $_SESSION['user']['username'] = $dni;
                        registrar_auditoria((int)$user['id'], 'cuenta_editada', 'Configuración');
                        $ok = 'Mi cuenta actualizada.';
                    }
                } catch (PDOException $e) {
                    error_log($e->getMessage());
                    $error = 'No se pudo actualizar la cuenta.';
                }
            }
        } elseif ($action === 'cerrar_sesiones') {
            invalidate_all_sessions((int)$user['id']);
            $ok = 'Se cerró la sesión de todos los usuarios y se fichó la salida de los empleados que estaban dentro. Tu sesión se mantuvo.';
        }
    }
}
require __DIR__ . '/partials/header.php';
?>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<?php if ($ok): ?><div class="alert success"><?= e($ok) ?></div><?php endif; ?>

<div class="section-head" style="margin-top:34px;"><h2>Tipos de procedimientos quirúrgicos</h2></div>
<section class="form-panel">
<div class="contract-preview" style="margin-bottom:14px;">
<?php foreach ($tipos as $tipo): ?>
<span class="tag fixed"><?= e($tipo['nombre']) ?>
<form class="inline-form" method="post" onsubmit="return confirm('¿Eliminar definitivamente este tipo de procedimiento? Esta acción no se puede deshacer.');"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="tipo_eliminar"><input type="hidden" name="id" value="<?= (int)$tipo['id_tipo_proc'] ?>"><button type="submit" class="act-del" style="background:transparent;border:0;padding:0;cursor:pointer;color:#fff;margin-left:8px;" title="Eliminar tipo" aria-label="Eliminar tipo"><?= icon('trash') ?></button></form>
</span>
<?php endforeach; ?>
</div>
<form method="post" autocomplete="off" class="filters" style="grid-template-columns:1fr auto;">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="tipo_nuevo">
<input name="nuevo_tipo" placeholder="Nuevo tipo de procedimiento..." required>
<button class="small-btn btn-add" type="submit"><?= icon('plus') ?>Agregar</button>
</form>
</section>

<div class="section-head" style="margin-top:34px;"><h2>Mi cuenta (administrador)</h2></div>
<section class="form-panel"><form method="post" autocomplete="off">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="password">
<div class="form-grid">
    <div><label>Nombre completo</label><input name="nombre_completo" value="<?= e($_POST['nombre_completo'] ?? $user['nombre'] . ' ' . $user['apellido']) ?>"></div>
    <div><label>DNI</label><input name="dni" value="<?= e($_POST['dni'] ?? $user['dni']) ?>"></div>
    <div><label>Nueva contraseña (opcional)</label><input type="password" name="nueva" autocomplete="new-password"></div>
    <div><label>Confirmar contraseña</label><input type="password" name="confirmar" autocomplete="new-password"></div>
</div>
<button class="btn primary" type="submit"><?= icon('save', 18) ?>Guardar cambios</button>
</form></section>

<div class="section-head" style="margin-top:34px;"><h2>Sesiones</h2></div>
<section class="form-panel"><form method="post" onsubmit="return confirm('¿Seguro que querés cerrar la sesión de todos los usuarios?');">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="cerrar_sesiones">
<button class="btn btn-del" type="submit"><?= icon('logout', 18) ?>Cerrar sesión de todos los usuarios</button>
</form></section>
<?php require __DIR__ . '/partials/footer.php'; ?>