<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';
$page_title = 'Cirugías';
$error = '';
$ok = '';

$instrumentadores = employees_by_role(['instrumentador']);
$radiologos = employees_by_role(['radiolog']);
$enfermeros = employees_by_role(['enfermero']);
$anestesistas = employees_by_role(['anestes']);
$administrativos = employees_by_role(['administrativo']);
$limpieza = employees_by_role(['limpieza']);
$tipos = procedure_types();

$mes = isset($_GET['mes']) && preg_match('/^\d{4}-\d{2}$/', $_GET['mes']) ? $_GET['mes'] : date('Y-m');
$editar = null;
$edit_id = 0;
// 6 roles disponibles: Instrumentador/a, Tecnico/a radiologa, Administrativo/a, Limpieza, Enfermero/a, Anestesista
// Mantener 4 selects: 2 roles importantes + resto agrupado
$personal_post = [
    ['campo' => 'instrumentador', 'rol' => 'Instrumentador/a'],
    ['campo' => 'anestesista', 'rol' => 'Anestesista'],
    ['campo' => 'enfermero', 'rol' => 'Enfermero/a'],
    ['campo' => 'otros_roles', 'rol' => 'Otros roles'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = (string)($_POST['action'] ?? '');
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'La sesión del formulario expiró. Intentá nuevamente.';
        if ($accion === 'editar') $edit_id = (int)($_POST['id'] ?? 0);
    } elseif ($accion === 'eliminar') {
        $result = delete_cirugia((int)($_POST['id'] ?? 0));
        if ($result['ok']) { $ok = 'Cirugía eliminada.'; } else { $error = $result['message']; }
    } else {
        $personal = array_map(fn($p) => ['id_usuario' => (int)($_POST[$p['campo']] ?? 0), 'rol' => $p['rol']], $personal_post);
        if ($accion === 'editar') {
            $edit_id = (int)($_POST['id'] ?? 0);
            $result = update_cirugia($edit_id, $_POST, $personal);
            if ($result['ok']) { $ok = 'Cirugía actualizada.'; $edit_id = 0; } else { $error = $result['message']; }
        } else {
            $result = create_cirugia($_POST, $personal);
            if ($result['ok']) { $ok = 'Cirugía registrada.'; } else { $error = $result['message']; }
        }
    }
} elseif (isset($_GET['editar'])) {
    $edit_id = (int)$_GET['editar'];
}

if ($edit_id > 0) {
    $editar = get_cirugia($edit_id);
    if ($editar === null) { $error = 'La cirugía indicada no existe.'; $edit_id = 0; }
    else { $editar['tipo_procedimiento'] = $editar['id_tipo_procedimiento']; }
}

$val = function (string $campo, string $default = '') use ($editar): string {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST[$campo])) return (string)$_POST[$campo];
    if ($editar !== null && array_key_exists($campo, $editar)) return (string)$editar[$campo];
    return $default;
};

$slots_rol = [
    'instrumentador' => 'Instrumentador/a',
    'anestesista' => 'Anestesista',
    'enfermero' => 'Enfermero/a',
    'otros_roles' => 'Otros roles',
];
$slots_palabras = [
    'instrumentador' => ['instrument'],
    'anestesista' => ['anestes'],
    'enfermero' => ['enfermer'],
    'otros_roles' => ['tecnico','radiolog','administrativo','limpieza'],
];
$personal_sel = array_fill_keys(array_keys($slots_rol), '');
$personal_nombres = [];
if ($editar !== null) {
    $normalizar = function (string $texto): string {
        $texto = strtr($texto, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u']);
        return mb_strtolower($texto);
    };
    foreach ($editar['personal'] as $fila) {
        $rol = $normalizar((string)$fila['rol']);
        $slot = null;
        foreach ($slots_rol as $campo => $etiqueta) if ($normalizar($etiqueta) === $rol) { $slot = $campo; break; }
        if ($slot === null) {
            foreach ($slots_palabras as $campo => $palabras) {
                foreach ($palabras as $palabra) if ($palabra !== '' && strpos($rol, $palabra) !== false) { $slot = $campo; break 2; }
            }
        }
        if ($slot === null) foreach ($personal_sel as $campo => $valor) if ($valor === '') { $slot = $campo; break; }
        if ($slot !== null && $personal_sel[$slot] === '') {
            $personal_sel[$slot] = (string)$fila['id_usuario'];
            $personal_nombres[$slot] = (string)($fila['nombre'] ?? '');
        }
    }
}
$otros_roles_list = array_merge($radiologos, $administrativos, $limpieza);
$listas = [
    'instrumentador' => $instrumentadores,
    'anestesista' => $anestesistas,
    'enfermero' => $enfermeros,
    'otros_roles' => $otros_roles_list,
];
$opciones_extra = [];
foreach ($personal_sel as $campo => $id) {
    if ($id === '') continue;
    $existe = false;
    foreach ($listas[$campo] as $emp) if ((string)$emp['id_usuario'] === $id) { $existe = true; break; }
    if (!$existe) $opciones_extra[$campo] = ['id' => $id, 'nombre' => $personal_nombres[$campo] !== '' ? $personal_nombres[$campo] : 'Empleado #' . $id];
}

$cirugias = list_cirugias($mes);
require __DIR__ . '/partials/header.php';
?>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<?php if ($ok): ?><div class="alert success"><?= e($ok) ?></div><?php endif; ?>

<section class="section-head"><h2><?= $editar ? 'Editar cirugía' : 'Registrar cirugía' ?></h2></section>
<section class="form-panel"><form method="post" autocomplete="off">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<?php if ($editar): ?>
<input type="hidden" name="action" value="editar">
<input type="hidden" name="id" value="<?= (int)$editar['id_cirugia'] ?>">
<?php endif; ?>
<div class="form-grid">
    <div><label>Fecha</label><input type="date" name="fecha" value="<?= e($val('fecha', date('Y-m-d'))) ?>" required></div>
    <div><label>Hora de inicio</label><input type="time" name="hora_inicio" value="<?= e($val('hora_inicio', '09:00')) ?>" required></div>
</div>
<div>
    <label>Tipo de procedimiento</label>
    <select name="tipo_procedimiento" required>
        <option value="" hidden>Seleccionar tipo de procedimiento</option>
        <?php foreach ($tipos as $tipo): ?>
        <option value="<?= (int)$tipo['id_tipo_proc'] ?>" <?= $val('tipo_procedimiento') === (string)$tipo['id_tipo_proc'] ? 'selected' : '' ?>><?= e($tipo['nombre']) ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="divider"></div>
<label style="font-weight:700;margin-bottom:14px;">Personal participante</label>
    <div class="form-grid">
        <div><label>Instrumentador/a</label><select name="instrumentador"><option value="" hidden>Seleccionar empleado</option><?php if (isset($opciones_extra['instrumentador'])): ?><option value="<?= (int)$opciones_extra['instrumentador']['id'] ?>" selected><?= e($opciones_extra['instrumentador']['nombre']) ?></option><?php endif; ?><?php foreach ($instrumentadores as $emp): ?><option value="<?= (int)$emp['id_usuario'] ?>" <?= $val('instrumentador', $personal_sel['instrumentador']) === (string)$emp['id_usuario'] ? 'selected' : '' ?>><?= e($emp['apellido'] . ', ' . $emp['nombre']) ?><?= $emp['especialidad'] ? ' — ' . e($emp['especialidad']) : '' ?></option><?php endforeach; ?></select></div>
        <div><label>Anestesista</label><select name="anestesista"><option value="" hidden>Seleccionar empleado</option><?php if (isset($opciones_extra['anestesista'])): ?><option value="<?= (int)$opciones_extra['anestesista']['id'] ?>" selected><?= e($opciones_extra['anestesista']['nombre']) ?></option><?php endif; ?><?php foreach ($anestesistas as $emp): ?><option value="<?= (int)$emp['id_usuario'] ?>" <?= $val('anestesista', $personal_sel['anestesista']) === (string)$emp['id_usuario'] ? 'selected' : '' ?>><?= e($emp['apellido'] . ', ' . $emp['nombre']) ?><?= $emp['especialidad'] ? ' — ' . e($emp['especialidad']) : '' ?></option><?php endforeach; ?></select></div>
        <div><label>Enfermero/a</label><select name="enfermero"><option value="" hidden>Seleccionar empleado</option><?php if (isset($opciones_extra['enfermero'])): ?><option value="<?= (int)$opciones_extra['enfermero']['id'] ?>" selected><?= e($opciones_extra['enfermero']['nombre']) ?></option><?php endif; ?><?php foreach ($enfermeros as $emp): ?><option value="<?= (int)$emp['id_usuario'] ?>" <?= $val('enfermero', $personal_sel['enfermero']) === (string)$emp['id_usuario'] ? 'selected' : '' ?>><?= e($emp['apellido'] . ', ' . $emp['nombre']) ?><?= $emp['especialidad'] ? ' — ' . e($emp['especialidad']) : '' ?></option><?php endforeach; ?></select></div>
        <div><label>Otros roles</label><select name="otros_roles"><option value="" hidden>Seleccionar empleado</option><?php if (isset($opciones_extra['otros_roles'])): ?><option value="<?= (int)$opciones_extra['otros_roles']['id'] ?>" selected><?= e($opciones_extra['otros_roles']['nombre']) ?></option><?php endif; ?><?php foreach ($otros_roles_list as $emp): ?><option value="<?= (int)$emp['id_usuario'] ?>" <?= $val('otros_roles', $personal_sel['otros_roles']) === (string)$emp['id_usuario'] ? 'selected' : '' ?>><?= e($emp['apellido'] . ', ' . $emp['nombre']) ?><?= $emp['especialidad'] ? ' — ' . e($emp['especialidad']) : '' ?></option><?php endforeach; ?></select></div>
    </div>
<div><label>Observaciones</label><textarea name="observaciones" rows="2"><?= e($val('observaciones')) ?></textarea></div>
<?php if ($editar): ?><div class="form-actions"><?php endif; ?>
<button class="btn primary btn-add" type="submit"><?= icon($editar ? 'save' : 'plus', 18) ?><?= $editar ? 'Guardar cambios' : 'Guardar cirugía' ?></button>
<?php if ($editar): ?><a class="small-btn" href="cirugias.php?mes=<?= e($mes) ?>">Cancelar</a></div><?php endif; ?>
</form></section>

<div class="section-head" style="margin-top:34px;"><h2>Cirugías registradas</h2>
<form class="filters" method="get" style="margin:0;"><input type="month" name="mes" value="<?= e($mes) ?>"><button class="small-btn" type="submit"><?= icon('calendar') ?>Ver mes</button></form>
</div>
<section class="panel"><div class="table-wrap"><table>
<thead><tr><th>Fecha</th><th>Hora</th><th>Procedimiento</th><th>Personal</th><th>Observaciones</th><th></th></tr></thead>
<tbody>
<?php foreach ($cirugias as $cir): ?>
<tr>
    <td><?= e($cir['fecha']) ?></td>
    <td><?= e($cir['hora_inicio']) ?></td>
    <td><?= e($cir['procedimiento']) ?></td>
    <td><?= e($cir['personal']) ?></td>
    <td><?= e($cir['observaciones']) ?></td>
    <td class="actions-cell"><div class="row-actions">
        <a class="act-edit" href="cirugias.php?mes=<?= e($mes) ?>&editar=<?= (int)$cir['id_cirugia'] ?>"><?= icon('edit') ?>Editar</a>
        <form id="del-cir-<?= (int)$cir['id_cirugia'] ?>" class="inline-form" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="eliminar"><input type="hidden" name="id" value="<?= (int)$cir['id_cirugia'] ?>"><button type="button" class="act-del" data-confirm-delete data-confirm-title="¿Eliminar esta cirugía?" data-confirm-message="Esta acción es <strong>irreversible</strong> y no se puede deshacer."><?= icon('trash') ?>Eliminar</button></form>
    </div></td>
</tr>
<?php endforeach; ?>
<?php if (!$cirugias): ?><tr><td colspan="6">No hay cirugías registradas en este mes.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php require __DIR__ . '/partials/footer.php'; ?>