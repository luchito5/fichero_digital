<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';
$page_title = 'Configuración';
$error = '';
$ok = '';

$user = require_admin();
$tipos = db()->query('SELECT id_tipo_proc, nombre FROM tipos_procedimiento WHERE activo = 1 ORDER BY nombre')->fetchAll();

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'La sesión del formulario expiró. Intentá nuevamente.';
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'message' => $error]);
            exit;
        }
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'tipo_nuevo') {
            $result = create_procedure_type($_POST['nuevo_tipo'] ?? '');
            if ($result['ok']) {
                $ok = 'Tipo de procedimiento agregado.';
                if ($isAjax) {
                    $nuevo = db()->query('SELECT id_tipo_proc, nombre FROM tipos_procedimiento WHERE activo = 1 ORDER BY nombre')->fetchAll();
                    header('Content-Type: application/json');
                    echo json_encode(['ok' => true, 'message' => $ok, 'tipos' => $nuevo]);
                    exit;
                }
            } else {
                $error = $result['message'];
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['ok' => false, 'message' => $error]);
                    exit;
                }
            }
        } elseif ($action === 'tipo_eliminar') {
            $result = delete_procedure_type((int)($_POST['id'] ?? 0));
            if ($result['ok']) {
                $ok = 'Tipo de procedimiento eliminado.';
                if ($isAjax) {
                    $nuevo = db()->query('SELECT id_tipo_proc, nombre FROM tipos_procedimiento WHERE activo = 1 ORDER BY nombre')->fetchAll();
                    header('Content-Type: application/json');
                    echo json_encode(['ok' => true, 'message' => $ok, 'tipos' => $nuevo]);
                    exit;
                }
            } else {
                $error = $result['message'];
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['ok' => false, 'message' => $error]);
                    exit;
                }
            }
        } elseif ($action === 'password') {
            $nombreCompleto = trim((string)($_POST['nombre_completo'] ?? ''));
            $dniPost = (string)($_POST['dni'] ?? '');
            $dni = dni_solo_digitos($dniPost);
            $nueva = $_POST['nueva'] ?? '';
            $confirmar = $_POST['confirmar'] ?? '';

            $partes = preg_split('/\s+/', $nombreCompleto, 2);
            $nombre = $partes[0] ?? '';
            $apellido = $partes[1] ?? '';

            $err = '';
            $dniCheck = dni_validar($dniPost);
            if ($nombreCompleto === '') $err = 'Completá el nombre completo.';
            elseif (!$dniCheck['ok']) $err = $dniCheck['message'];
            elseif ($nueva !== '' && $confirmar === '') $err = 'Tenés que confirmar tu contraseña';
            elseif ($nueva !== '' && strlen($nueva) < 8) $err = 'La nueva contraseña debe tener al menos 8 caracteres.';
            elseif ($nueva !== $confirmar) $err = 'Las contraseñas no coinciden.';

            if ($err !== '') {
                $error = $err;
            } else {
                try {
                    $stmt = db()->prepare('SELECT COUNT(*) FROM usuarios WHERE dni = :d AND id_usuario <> :id');
                    $stmt->execute(['d' => $dni, 'id' => $user['id']]);
                    if ((int)$stmt->fetchColumn() > 0) {
                        $error = 'Ese DNI ya existe.';
                    } else {
                        $stmt = db()->prepare('UPDATE usuarios SET nombre = :n, apellido = :a, dni = :d WHERE id_usuario = :id');
                        $stmt->execute(['n' => $nombre, 'a' => $apellido, 'd' => $dni, 'id' => $user['id']]);
                        if ($nueva !== '') {
                            db()->prepare('UPDATE usuarios SET password_hash = :p WHERE id_usuario = :id')->execute(['p' => password_hash($nueva, PASSWORD_DEFAULT), 'id' => $user['id']]);
                        }
                        $_SESSION['user']['nombre'] = $nombre;
                        $_SESSION['user']['apellido'] = $apellido;
                        $_SESSION['user']['dni'] = $dni;
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
<div class="contract-preview" id="tipos-lista" style="margin-bottom:14px;">
<?php foreach ($tipos as $tipo): ?>
<span class="tag fixed"><?= e($tipo['nombre']) ?>
<form id="del-tipo-<?= (int)$tipo['id_tipo_proc'] ?>" class="inline-form" method="post" data-confirm-ajax><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="tipo_eliminar"><input type="hidden" name="id" value="<?= (int)$tipo['id_tipo_proc'] ?>"><button type="button" class="act-del" style="background:transparent;border:0;padding:0;cursor:pointer;color:#fff;margin-left:8px;" title="Eliminar tipo" aria-label="Eliminar tipo" data-confirm-delete data-confirm-title="¿Eliminar este tipo de procedimiento?" data-confirm-message="Esta acción es <strong>irreversible</strong> y no se puede deshacer."><?= icon('trash') ?></button></form>
</span>
<?php endforeach; ?>
</div>
<form method="post" autocomplete="off" class="filters" id="form-tipo-nuevo" style="grid-template-columns:1fr auto;">
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
    <div><label>DNI</label><input name="dni" type="text" inputmode="numeric" data-dni maxlength="8" pattern="[0-9]{8}" title="El DNI debe tener 8 dígitos numéricos." value="<?= e($_POST['dni'] ?? $user['dni']) ?>"></div>
    <div><label>Nueva contraseña (opcional)</label><input type="password" name="nueva" autocomplete="new-password"></div>
    <div><label>Confirmar contraseña</label><input type="password" name="confirmar" autocomplete="new-password"></div>
</div>
<button class="btn primary" type="submit"><?= icon('save', 18) ?>Guardar cambios</button>
</form></section>

<div class="section-head" style="margin-top:34px;"><h2>Sesiones</h2></div>
<section class="form-panel"><form id="cerrar-sesiones" method="post">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="cerrar_sesiones">
<button class="btn btn-del" type="button" data-confirm-delete data-confirm-action="Sí, cerrar" data-confirm-title="¿Cerrar la sesión de todos los usuarios?" data-confirm-message="Todos los usuarios serán <strong>desconectados</strong> inmediatamente. Esta acción no se puede deshacer."><?= icon('logout', 18) ?>Cerrar sesión de todos los usuarios</button>
</form></section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const lista = document.getElementById('tipos-lista');
    const formAgregar = document.getElementById('form-tipo-nuevo');
    if (!lista || !formAgregar) return;

    const csrf = formAgregar.querySelector('input[name="csrf_token"]').value;

    function renderTipos(tipos) {
        lista.innerHTML = tipos.map(function (t) {
            const id = parseInt(t.id_tipo_proc, 10);
            const nombre = t.nombre.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            return '<span class="tag fixed">' + nombre +
                '<form id="del-tipo-' + id + '" class="inline-form" method="post" data-confirm-ajax>' +
                '<input type="hidden" name="csrf_token" value="' + csrf + '">' +
                '<input type="hidden" name="action" value="tipo_eliminar">' +
                '<input type="hidden" name="id" value="' + id + '">' +
                '<button type="button" class="act-del" style="background:transparent;border:0;padding:0;cursor:pointer;color:#fff;margin-left:8px;" title="Eliminar tipo" aria-label="Eliminar tipo" data-confirm-delete data-confirm-title="¿Eliminar este tipo de procedimiento?" data-confirm-message="Esta acción es <strong>irreversible</strong> y no se puede deshacer.">' +
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>' +
                '</button></form></span>';
        }).join('');
    }

    function showAlert(msg, isError) {
        const div = document.createElement('div');
        div.className = 'alert ' + (isError ? 'error' : 'success');
        div.textContent = msg;
        const main = document.querySelector('.admin-content') || document.querySelector('.content');
        const firstSection = document.querySelector('.section-head, .form-panel');
        if (main && firstSection) {
            main.insertBefore(div, firstSection);
        } else if (main) {
            main.insertBefore(div, main.firstChild);
        } else {
            const panel = document.querySelector('.form-panel');
            panel.parentNode.insertBefore(div, panel.nextSibling);
        }
        setTimeout(function () { div.remove(); }, 3500);
    }

    formAgregar.addEventListener('submit', function (e) {
        e.preventDefault();
        const input = formAgregar.querySelector('input[name="nuevo_tipo"]');
        const nombre = input.value.trim();
        if (!nombre) return;

        const formData = new FormData();
        formData.append('csrf_token', csrf);
        formData.append('action', 'tipo_nuevo');
        formData.append('nuevo_tipo', nombre);

        fetch('configuracion.php', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.ok) {
                renderTipos(data.tipos);
                input.value = '';
                showAlert(data.message, false);
            } else {
                showAlert(data.message, true);
            }
        })
        .catch(function () {
            showAlert('Error de conexión. Intentá nuevamente.', true);
        });
    });

    // Baja en tiempo real: el modal de confirmación avisa al formulario con 'confirm-ajax'.
    lista.addEventListener('confirm-ajax', function (e) {
        const form = e.target;
        if (!form || form.tagName !== 'FORM') return;

        fetch('configuracion.php', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form)
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.ok) {
                renderTipos(data.tipos);
                showAlert(data.message, false);
            } else {
                showAlert(data.message, true);
            }
        })
        .catch(function () {
            showAlert('Error de conexión. Intentá nuevamente.', true);
        });
    });
});
</script>
<?php require __DIR__ . '/partials/footer.php'; ?>