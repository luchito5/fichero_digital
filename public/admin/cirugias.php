<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';
$page_title = 'Cirugías';
$error = '';
$ok = '';

$cirujanos = employees_by_role(['cirujan']);
$instrumentadores = employees_by_role(['instrument']);
$anestesistas = employees_by_role(['anestes']);
$otros = employees_other(['cirujan', 'instrument', 'anestes']);
$tipos = procedure_types();

$mes = isset($_GET['mes']) && preg_match('/^\d{4}-\d{2}$/', $_GET['mes']) ? $_GET['mes'] : date('Y-m');
$editar = null;
$edit_id = 0;
$personal_post = [
    ['campo' => 'cirujano', 'rol' => 'Cirujano/a'],
    ['campo' => 'instrumentador', 'rol' => 'Instrumentador/a'],
    ['campo' => 'anestesia', 'rol' => 'Técnico/a en anestesia'],
    ['campo' => 'otro', 'rol' => 'Otro personal'],
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

$slots_rol = ['cirujano' => 'Cirujano/a', 'instrumentador' => 'Instrumentador/a', 'anestesia' => 'Técnico/a en anestesia', 'otro' => 'Otro personal'];
$slots_palabras = ['cirujano' => ['cirujan'], 'instrumentador' => ['instrument', 'enfermer', 'quirom'], 'anestesia' => ['anestes'], 'otro' => ['ayudante', 'otro']];
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
$listas = ['cirujano' => $cirujanos, 'instrumentador' => $instrumentadores, 'anestesia' => $anestesistas, 'otro' => $otros];
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
        <option value="">Seleccionar tipo de procedimiento</option>
        <?php foreach ($tipos as $tipo): ?>
        <option value="<?= (int)$tipo['id_tipo_proc'] ?>" <?= $val('tipo_procedimiento') === (string)$tipo['id_tipo_proc'] ? 'selected' : '' ?>><?= e($tipo['nombre']) ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="divider"></div>
<label style="font-weight:700;margin-bottom:14px;">Personal participante</label>
<div class="form-grid">
    <div><label>Cirujano/a</label><select name="cirujano"><option value="">Seleccionar empleado</option><?php if (isset($opciones_extra['cirujano'])): ?><option value="<?= (int)$opciones_extra['cirujano']['id'] ?>" selected><?= e($opciones_extra['cirujano']['nombre']) ?></option><?php endif; ?><?php foreach ($cirujanos as $emp): ?><option value="<?= (int)$emp['id_usuario'] ?>" <?= $val('cirujano', $personal_sel['cirujano']) === (string)$emp['id_usuario'] ? 'selected' : '' ?>><?= e($emp['apellido'] . ', ' . $emp['nombre']) ?><?= $emp['especialidad'] ? ' — ' . e($emp['especialidad']) : '' ?></option><?php endforeach; ?></select></div>
    <div><label>Instrumentador/a</label><select name="instrumentador"><option value="">Seleccionar empleado</option><?php if (isset($opciones_extra['instrumentador'])): ?><option value="<?= (int)$opciones_extra['instrumentador']['id'] ?>" selected><?= e($opciones_extra['instrumentador']['nombre']) ?></option><?php endif; ?><?php foreach ($instrumentadores as $emp): ?><option value="<?= (int)$emp['id_usuario'] ?>" <?= $val('instrumentador', $personal_sel['instrumentador']) === (string)$emp['id_usuario'] ? 'selected' : '' ?>><?= e($emp['apellido'] . ', ' . $emp['nombre']) ?><?= $emp['especialidad'] ? ' — ' . e($emp['especialidad']) : '' ?></option><?php endforeach; ?></select></div>
    <div><label>Técnico/a en anestesia</label><select name="anestesia"><option value="">Seleccionar empleado</option><?php if (isset($opciones_extra['anestesia'])): ?><option value="<?= (int)$opciones_extra['anestesia']['id'] ?>" selected><?= e($opciones_extra['anestesia']['nombre']) ?></option><?php endif; ?><?php foreach ($anestesistas as $emp): ?><option value="<?= (int)$emp['id_usuario'] ?>" <?= $val('anestesia', $personal_sel['anestesia']) === (string)$emp['id_usuario'] ? 'selected' : '' ?>><?= e($emp['apellido'] . ', ' . $emp['nombre']) ?><?= $emp['especialidad'] ? ' — ' . e($emp['especialidad']) : '' ?></option><?php endforeach; ?></select></div>
    <div><label>Otro personal (opcional)</label><select name="otro"><option value="">Seleccionar empleado</option><?php if (isset($opciones_extra['otro'])): ?><option value="<?= (int)$opciones_extra['otro']['id'] ?>" selected><?= e($opciones_extra['otro']['nombre']) ?></option><?php endif; ?><?php foreach ($otros as $emp): ?><option value="<?= (int)$emp['id_usuario'] ?>" <?= $val('otro', $personal_sel['otro']) === (string)$emp['id_usuario'] ? 'selected' : '' ?>><?= e($emp['apellido'] . ', ' . $emp['nombre']) ?><?= $emp['especialidad'] ? ' — ' . e($emp['especialidad']) : '' ?></option><?php endforeach; ?></select></div>
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
        <form class="inline-form" method="post" onsubmit="return confirm('¿Eliminar esta cirugía?');"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="eliminar"><input type="hidden" name="id" value="<?= (int)$cir['id_cirugia'] ?>"><button type="submit" class="act-del"><?= icon('trash') ?>Eliminar</button></form>
    </div></td>
</tr>
<?php endforeach; ?>
<?php if (!$cirugias): ?><tr><td colspan="6">No hay cirugías registradas en este mes.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php require __DIR__ . '/partials/footer.php'; ?>