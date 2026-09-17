<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';
require_once __DIR__ . '/../../src/export_report.php';

$mes = isset($_GET['mes']) && preg_match('/^\d{4}-\d{2}$/', $_GET['mes']) ? $_GET['mes'] : date('Y-m');
$idEmp = (int)($_GET['empleado'] ?? 0);
$contrato = trim((string)($_GET['tipo'] ?? ''));
$export = $_GET['export'] ?? '';

if ($export === 'docx' || $export === 'word') {
    $d = reporte_datos($mes, $idEmp, $contrato);
    servir_docx('resumen_mensual_' . $mes . '.docx', generar_docx($d));
}
if ($export === 'pdf') {
    $d = reporte_datos($mes, $idEmp, $contrato);
    servir_pdf('resumen_mensual_' . $mes . '.pdf', generar_pdf($d));
}

$nombresMes = [
    1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
    'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre',
];
$nombreMes = ucfirst($nombresMes[(int)substr($mes, 5, 2)] . ' de ' . substr($mes, 0, 4));

$empleados = empleados_filtro_resumen($mes);
$porEmp = resumen_por_empleado($mes, $idEmp, $contrato);
$fichajes = resumen_fichajes($mes, $idEmp, $contrato);
$cirugias = resumen_cirugias($mes, $idEmp, $contrato);
$alquileres = resumen_alquileres($mes);
$novedades = resumen_novedades($mes, $idEmp, $contrato);

$fmtH = static fn ($v) => trim(rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.'));
$horas = array_reduce($porEmp, static fn ($carry, $r) => $carry + (float)$r['horas'], 0.0);
$hsAlquiler = array_reduce($alquileres, static fn ($carry, $r) => $carry + (float)($r['horas_uso'] ?? 0), 0.0);

$page_title = 'Resumen e informes';
require __DIR__ . '/partials/header.php';

function detail_table(array $cols, array $rows, array $widths, int $colsSpan): string
{
    if (!$rows) {
        return '<p style="color:var(--muted);font-size:12px;margin:10px 0 0;">Sin registros para este período.</p>';
    }
    $h = '<thead><tr>';
    foreach ($cols as $i => $c) {
        $w = (int)round($widths[$i] * 100) . '%';
        $h .= '<th style="width:' . $w . '">' . e($c) . '</th>';
    }
    $h .= '</tr></thead><tbody>';
    foreach ($rows as $r) {
        $h .= '<tr>';
        foreach ($r as $i => $val) {
            $h .= '<td>' . e((string)$val) . '</td>';
        }
        $h .= '</tr>';
    }
    $h .= '</tbody>';
    return '<div class="table-wrap"><table>' . $h . '</table></div>';
}

$baseQ = http_build_query(array_filter(['mes' => $mes, 'empleado' => $idEmp ?: null, 'tipo' => $contrato ?: null]));
?>

<div class="section-head">
    <h2>Resumen general</h2>
    <div style="display:flex;gap:10px;align-items:center;">
        <a class="act act-view" href="resumen_mensual.php?<?= e($baseQ) ?>&export=docx"><?= icon('download') ?>Word (.DOCX)</a>
        <a class="act act-view" href="resumen_mensual.php?<?= e($baseQ) ?>&export=pdf"><?= icon('printer') ?>PDF (.PDF)</a>
    </div>
</div>

<form class="filters report-filters" method="get">
    <div>
        <label style="margin:0 0 4px;font-size:12px;color:var(--muted);">Mes</label>
        <input type="month" name="mes" value="<?= e($mes) ?>">
    </div>
    <div>
        <label style="margin:0 0 4px;font-size:12px;color:var(--muted);">Empleado</label>
        <select name="empleado">
            <option value="">Todos los empleados</option>
            <?php foreach ($empleados as $emp): ?>
            <option value="<?= (int)$emp['id_usuario'] ?>" <?= $idEmp === (int)$emp['id_usuario'] ? 'selected' : '' ?>><?= e($emp['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label style="margin:0 0 4px;font-size:12px;color:var(--muted);">Tipo de contrato</label>
        <select name="tipo">
            <option value="">Todos</option>
            <?php foreach (['Fijo', 'Por Hora', 'Por Cirugía', 'Alquiler', 'Admin'] as $c): ?>
            <option value="<?= e($c) ?>" <?= $contrato === $c ? 'selected' : '' ?>><?= e($c) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div style="display:flex;align-items:flex-end;">
        <button class="small-btn" type="submit"><?= icon('calendar') ?>Aplicar filtros</button>
    </div>
</form>

<section class="cards admin-stats report-stats">
    <article class="stat"><span>Empleados</span><strong><?= count($porEmp) ?></strong><small>activos</small></article>
    <article class="stat"><span>Horas trabajadas</span><strong><?= e($fmtH($horas)) ?> h</strong><small>según fichajes</small></article>
    <article class="stat"><span>Fichajes</span><strong><?= count($fichajes) ?></strong><small>registros</small></article>
    <article class="stat"><span>Cirugías</span><strong><?= count($cirugias) ?></strong><small>procedimientos</small></article>
    <article class="stat"><span>Alquileres</span><strong><?= count($alquileres) ?></strong><small><?= e($fmtH($hsAlquiler)) ?> h de uso</small></article>
    <article class="stat"><span>Novedades</span><strong><?= count($novedades) ?></strong><small>ausencias</small></article>
</section>

<div class="section-head"><h2>Resumen por empleado</h2></div>
<section class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:30%">Empleado</th>
                    <th style="width:14%">Contrato</th>
                    <th style="width:10%">Fichajes</th>
                    <th style="width:12%">Horas</th>
                    <th style="width:10%">Cirugías</th>
                    <th style="width:10%">Novedades</th>
                    <th style="width:14%"></th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$porEmp): ?>
                <tr><td colspan="7">No hay empleados para los filtros seleccionados.</td></tr>
            <?php endif; ?>
            <?php foreach ($porEmp as $emp): ?>
                <tr>
                    <td><strong><?= e($emp['nombre'] . ' ' . $emp['apellido']) ?></strong></td>
                    <td><span class="tag <?= e(contract_class($emp['tipo_contrato'])) ?>"><?= e($emp['tipo_contrato']) ?></span></td>
                    <td><?= (int)$emp['fichajes'] ?></td>
                    <td><?= e($fmtH($emp['horas'])) ?> h</td>
                    <td><?= (int)$emp['cirugias'] ?></td>
                    <td><?= (int)$emp['novedades'] ?></td>
                    <td class="actions-cell">
                        <details class="emp-detail">
                            <summary class="act act-view" style="cursor:pointer;"><?= icon('eye') ?>Detalle</summary>
                            <div class="detail-body">
                                <?php
                                $df = resumen_fichajes($mes, (int)$emp['id_usuario']);
                                $dc = resumen_cirugias($mes, (int)$emp['id_usuario']);
                                $dn = resumen_novedades($mes, (int)$emp['id_usuario']);
                                ?>
                                <?php if ($df): ?>
                                <h4>Fichajes</h4>
                                <?= detail_table(
                                    ['Fecha', 'Entrada', 'Salida', 'Horas', 'Validado'],
                                    array_map(fn ($r) => [
                                        $r['fecha'], $r['entrada'] ?? '-', $r['salida'] ?? '-',
                                        $r['horas_trabajadas'] !== null ? $fmtH((float)$r['horas_trabajadas']) . ' h' : '-',
                                        estado_validacion((int)$r['validado_admin'])['texto'],
                                    ], $df),
                                    [0.28, 0.17, 0.17, 0.20, 0.18],
                                    5
                                ) ?>
                                <?php endif; ?>
                                <?php if ($dc): ?>
                                <h4>Cirugías</h4>
                                <?= detail_table(
                                    ['Fecha', 'Hora', 'Procedimiento', 'Personal', 'Observaciones'],
                                    array_map(fn ($r) => [$r['fecha'], $r['hora_inicio'], $r['procedimiento'], $r['personal'], $r['observaciones'] ?? '-'], $dc),
                                    [0.13, 0.09, 0.17, 0.38, 0.23],
                                    5
                                ) ?>
                                <?php endif; ?>
                                <?php if ($dn): ?>
                                <h4>Novedades</h4>
                                <?= detail_table(
                                    ['Tipo', 'Desde', 'Hasta', 'Observaciones'],
                                    array_map(fn ($r) => [$r['tipo'], $r['fecha_desde'] ?? '-', $r['fecha_hasta'] ?? '-', $r['observaciones'] ?: '-'], $dn),
                                    [0.20, 0.18, 0.18, 0.44],
                                    4
                                ) ?>
                                <?php endif; ?>
                                <?php if (!$df && !$dc && !$dn): ?>
                                <p style="color:var(--muted);font-size:12px;margin:10px 0 0;">Sin registros para este período.</p>
                                <?php endif; ?>
                            </div>
                        </details>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<div class="section-head" style="margin-top:34px;"><h2>Fichajes del mes</h2></div>
<section class="panel">
    <?= detail_table(
        ['Fecha', 'Empleado', 'Contrato', 'Entrada', 'Salida', 'Horas', 'Validado'],
        array_map(fn ($r) => [
            $r['fecha'], $r['empleado'], $r['tipo_contrato'],
            $r['entrada'] ?? '-', $r['salida'] ?? '-',
            $r['horas_trabajadas'] !== null ? $fmtH((float)$r['horas_trabajadas']) . ' h' : '-',
            estado_validacion((int)$r['validado_admin'])['texto'],
        ], $fichajes),
        [0.15, 0.24, 0.14, 0.11, 0.11, 0.11, 0.14],
        7
    ) ?>
</section>

<div class="section-head" style="margin-top:34px;"><h2>Cirugías del mes</h2></div>
<section class="panel">
    <?= detail_table(
        ['Fecha', 'Hora', 'Procedimiento', 'Personal', 'Observaciones'],
        array_map(fn ($r) => [$r['fecha'], $r['hora_inicio'], $r['procedimiento'], $r['personal'], $r['observaciones'] ?? '-'], $cirugias),
        [0.13, 0.09, 0.17, 0.38, 0.23],
        5
    ) ?>
</section>

<div class="section-head" style="margin-top:34px;"><h2>Alquileres del mes</h2></div>
<section class="panel">
    <?= detail_table(
        ['Fecha', 'Responsable', 'Institución', 'Entrada', 'Salida', 'Hs', 'Facturación'],
        array_map(fn ($r) => [
            $r['fecha'], $r['responsable'], $r['institucion'],
            $r['entrada'] ?? '-', $r['salida'] ?? '-',
            $r['horas_uso'] !== null ? $fmtH((float)$r['horas_uso']) . ' h' : '-',
            $r['dato_facturacion'] ?: '-',
        ], $alquileres),
        [0.12, 0.19, 0.16, 0.10, 0.10, 0.08, 0.25],
        7
    ) ?>
</section>

<div class="section-head" style="margin-top:34px;"><h2>Novedades del mes</h2></div>
<section class="panel">
    <?= detail_table(
        ['Empleado', 'Contrato', 'Tipo', 'Desde', 'Hasta', 'Observaciones'],
        array_map(fn ($r) => [$r['empleado'], $r['tipo_contrato'], $r['tipo'], $r['fecha_desde'] ?? '-', $r['fecha_hasta'] ?? '-', $r['observaciones'] ?: '-'], $novedades),
        [0.22, 0.14, 0.14, 0.12, 0.12, 0.26],
        6
    ) ?>
</section>

<style>
.emp-detail { display:inline; }
.emp-detail > summary { list-style:none; }
.emp-detail > summary::-webkit-details-marker { display:none; }
.detail-body { margin-top:10px; padding:12px; background:rgba(0,0,0,.15); border-radius:6px; }
.detail-body h4 { margin:16px 0 6px; font-size:13px; color:var(--cyan); border-bottom:1px solid rgba(255,255,255,.15); padding-bottom:4px; }
.detail-body h4:first-child { margin-top:0; }
.detail-body .table-wrap { margin:0; }
.detail-body table { font-size:12px; }
.detail-body th { padding:6px; font-size:11px; }
.detail-body td { padding:6px; }
</style>

<?php require __DIR__ . '/partials/footer.php'; ?>