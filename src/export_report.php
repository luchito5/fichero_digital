<?php
declare(strict_types=1);

require_once __DIR__ . '/admin_functions.php';
require_once __DIR__ . '/pdf_writer.php';
require_once __DIR__ . '/docx_writer.php';

function reporte_datos(string $mes, int $idUsuario = 0, string $contrato = ''): array
{
    $nombresMes = [
        1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre',
    ];
    $idx = (int)substr($mes, 5, 2);
    $nombreMes = ucfirst($nombresMes[$idx] . ' de ' . substr($mes, 0, 4));

    $porEmp = array_values(resumen_por_empleado($mes, $idUsuario, $contrato));
    $fichajes = resumen_fichajes($mes, $idUsuario, $contrato);
    $cirugias = resumen_cirugias($mes, $idUsuario, $contrato);
    $alquileres = resumen_alquileres($mes);
    $novedades = resumen_novedades($mes, $idUsuario, $contrato);

    $fmt = static fn ($v) => trim(rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.'));
    $dash = static fn ($v) => ($v === null || $v === '') ? '-' : (string)$v;

    $horas = array_reduce($porEmp, static fn ($carry, $r) => $carry + (float)$r['horas'], 0.0);

    $nombreEmp = 'Todos los empleados';
    if ($idUsuario > 0) {
        $f = find_employee($idUsuario);
        if ($f) {
            $nombreEmp = trim($f['nombre'] . ' ' . $f['apellido']);
        }
    }
    $nombreContrato = $contrato !== '' ? $contrato : 'Todos los tipos de contrato';

    $hsAlquiler = array_reduce($alquileres, static fn ($carry, $r) => $carry + (float)($r['horas_uso'] ?? 0), 0.0);

    $secciones = [];

    $secciones[] = [
        'titulo' => 'Resumen por empleado',
        'cols' => ['Empleado', 'Contrato', 'Fichajes', 'Horas', 'Cirugías', 'Novedades'],
        'widths' => [0.32, 0.16, 0.12, 0.14, 0.13, 0.13],
        'rows' => array_map(static fn ($r) => [
            trim($r['nombre'] . ' ' . $r['apellido']),
            $r['tipo_contrato'],
            (string)(int)$r['fichajes'],
            $fmt($r['horas']) . ' h',
            (string)(int)$r['cirugias'],
            (string)(int)$r['novedades'],
        ], $porEmp),
        'totales' => [
            'TOTAL (' . count($porEmp) . ')',
            '',
            (string)count($fichajes),
            $fmt($horas) . ' h',
            (string)count($cirugias),
            (string)count($novedades),
        ],
        'vacio' => 'No hay empleados para los filtros seleccionados.',
    ];

    $secciones[] = [
        'titulo' => 'Fichajes del mes',
        'cols' => ['Fecha', 'Empleado', 'Contrato', 'Entrada', 'Salida', 'Horas', 'Validado'],
        'widths' => [0.13, 0.26, 0.13, 0.10, 0.10, 0.12, 0.16],
        'rows' => array_map(static fn ($r) => [
            $r['fecha'], $r['empleado'], $r['tipo_contrato'],
            $dash($r['entrada']), $dash($r['salida']),
            $r['horas_trabajadas'] !== null ? $fmt((float)$r['horas_trabajadas']) . ' h' : '-',
            estado_validacion((int)$r['validado_admin'])['texto'],
        ], $fichajes),
        'totales' => [(string)count($fichajes) . ' registro(s)', '', '', '', '', $fmt(array_sum(array_map(static fn ($r) => (float)($r['horas_trabajadas'] ?? 0), $fichajes))) . ' h', ''],
        'vacio' => 'Sin fichajes para este período.',
    ];

    $secciones[] = [
        'titulo' => 'Cirugías del mes',
        'cols' => ['Fecha', 'Hora', 'Procedimiento', 'Personal', 'Observaciones'],
        'widths' => [0.12, 0.08, 0.20, 0.36, 0.24],
        'rows' => array_map(static fn ($r) => [
            $r['fecha'], $r['hora_inicio'], $r['procedimiento'], $dash($r['personal']), $dash($r['observaciones']),
        ], $cirugias),
        'totales' => [(string)count($cirugias) . ' procedimiento(s)', '', '', '', ''],
        'vacio' => 'Sin cirugías para este período.',
    ];

    $secciones[] = [
        'titulo' => 'Alquileres del mes',
        'cols' => ['Fecha', 'Responsable', 'Institución', 'Entrada', 'Salida', 'Hs uso', 'Facturación'],
        'widths' => [0.12, 0.20, 0.17, 0.09, 0.09, 0.09, 0.24],
        'rows' => array_map(static fn ($r) => [
            $r['fecha'], $dash($r['responsable']), $dash($r['institucion']),
            $dash($r['entrada']), $dash($r['salida']),
            $r['horas_uso'] !== null ? $fmt((float)$r['horas_uso']) . ' h' : '-',
            $dash($r['dato_facturacion']),
        ], $alquileres),
        'totales' => [(string)count($alquileres) . ' alquiler(es)', '', '', '', '', $fmt($hsAlquiler) . ' h', ''],
        'vacio' => 'Sin alquileres para este período.',
    ];

    $secciones[] = [
        'titulo' => 'Novedades del mes',
        'cols' => ['Empleado', 'Contrato', 'Tipo', 'Desde', 'Hasta', 'Observaciones'],
        'widths' => [0.22, 0.14, 0.14, 0.12, 0.12, 0.26],
        'rows' => array_map(static fn ($r) => [
            $r['empleado'], $r['tipo_contrato'], $r['tipo'],
            $dash($r['fecha_desde']), $dash($r['fecha_hasta']), $dash($r['observaciones']),
        ], $novedades),
        'totales' => [(string)count($novedades) . ' novedad(es)', '', '', '', '', ''],
        'vacio' => 'Sin novedades para este período.',
    ];

    return [
        'titulo' => 'FICHERO DIGITAL AMEMT',
        'subtitulo' => 'Resumen mensual · ' . $nombreMes,
        'mes' => $mes,
        'generado' => date('d/m/Y H:i'),
        'filtros' => [
            ['Empleado', $nombreEmp],
            ['Tipo de contrato', $nombreContrato],
        ],
        'secciones' => $secciones,
    ];
}

function generar_docx(array $d): string
{
    $body = '';
    $body .= docx_paragraph($d['titulo'], ['align' => 'center', 'bold' => true, 'size' => 32, 'color' => '079BB5', 'after' => 40]);
    $body .= docx_paragraph($d['subtitulo'], ['align' => 'center', 'size' => 22, 'color' => '333333', 'after' => 40]);
    foreach ($d['filtros'] as [$etiqueta, $valor]) {
        $body .= docx_paragraph($etiqueta . ': ' . $valor, ['align' => 'center', 'size' => 16, 'color' => '666666', 'after' => 20]);
    }
    $body .= docx_paragraph('Generado el ' . $d['generado'], ['align' => 'center', 'size' => 14, 'color' => '999999', 'after' => 200]);

    foreach ($d['secciones'] as $i => $sec) {
        $body .= docx_paragraph($sec['titulo'], ['bold' => true, 'size' => 22, 'color' => '079BB5', 'before' => $i === 0 ? 0 : 220, 'after' => 90]);
        $body .= docx_table(
            $sec['cols'],
            $sec['rows'],
            $sec['widths'],
            $sec['vacio'],
            9360,
            $sec['rows'] ? $sec['totales'] : []
        );
        $body .= docx_paragraph('', ['size' => 12, 'after' => 0]);
    }

    $doc = docx_document($body);
    $z = new DocxWriter();
    $z->add('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        . '<Default Extension="xml" ContentType="application/xml"/>'
        . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
        . '</Types>');
    $z->add('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
        . '</Relationships>');
    $z->add('word/document.xml', $doc);
    return $z->build();
}

function generar_pdf(array $d): string
{
    $pdf = new MiniPdf();

    $pw = $pdf->right() - $pdf->left();

    $pdf->setFont('F2', 17);
    $pdf->setColor(7, 155, 181);
    $pdf->text($d['titulo'], 0, $pdf->posY(), 'center');
    $pdf->advance(15);

    $pdf->setFont('F2', 12);
    $pdf->setColor(40, 40, 40);
    $pdf->text($d['subtitulo'], 0, $pdf->posY(), 'center');
    $pdf->advance(13);

    $pdf->setFont('F1', 9);
    $pdf->setColor(100, 100, 100);
    foreach ($d['filtros'] as [$etiqueta, $valor]) {
        $pdf->text($etiqueta . ': ' . $valor, 0, $pdf->posY(), 'center');
        $pdf->advance(11);
    }
    $pdf->setColor(150, 150, 150);
    $pdf->text('Generado el ' . $d['generado'], 0, $pdf->posY(), 'center');
    $pdf->advance(9);

    $pdf->line($pdf->left(), $pdf->posY(), $pdf->right(), $pdf->posY());
    $pdf->advance(14);

    $fs = 8;
    $pad = 5;

    foreach ($d['secciones'] as $sec) {
        $cols = $sec['cols'];
        $colW = array_map(static fn ($w) => $w * $pw, $sec['widths']);
        $rows = $sec['rows'];

        $rowH = $fs * 1.7;

        $drawHeader = function () use ($pdf, $cols, $colW, $pw, $rowH, $fs, $pad) {
            $pdf->books($rowH);
            $pdf->setFont('F2', $fs);
            $pdf->setColor(7, 155, 181);
            $pdf->rect($pdf->left(), $pdf->posY(), $pw, $rowH, true);
            $pdf->setColor(255, 255, 255);
            $cx = $pdf->left();
            foreach ($cols as $ci => $c) {
                $pdf->text($c, $cx + $pad, $pdf->posY() + 3);
                $cx += $colW[$ci];
            }
            $pdf->advance($rowH);
        };

        $drawRow = function (array $row, bool $alt, bool $tot, bool $sep = true) use ($pdf, $colW, $pw, $fs, $pad) {
            $lines = [];
            $maxLines = 1;
            foreach ($row as $ci => $val) {
                $ws = $pdf->wrap((string)$val, $colW[$ci] - 2 * $pad, $fs);
                $lines[$ci] = $ws;
                if (count($ws) > $maxLines) {
                    $maxLines = count($ws);
                }
            }
            $rh = $maxLines * $fs * 1.45 + 2 * $pad;

            $pdf->books($rh);
            $y = $pdf->posY();

            if ($tot) {
                $pdf->setColor(222, 238, 241);
                $pdf->rect($pdf->left(), $y, $pw, $rh, true);
            } elseif ($alt) {
                $pdf->setColor(240, 247, 248);
                $pdf->rect($pdf->left(), $y, $pw, $rh, true);
            }

            $pdf->setColor($tot ? 15 : 17, $tot ? 112 : 17, $tot ? 128 : 17);
            $cellY = $y;
            $cx = $pdf->left();
            foreach ($row as $ci => $val) {
                $pdf->setFont($tot ? 'F2' : 'F1', $fs);
                foreach ($lines[$ci] as $li => $ln) {
                    $pdf->text($ln, $cx + $pad, $cellY + $pad + $li * $fs * 1.45);
                }
                $cx += $colW[$ci];
            }
            if ($tot) {
                $pdf->setColor(7, 155, 181);
                $pdf->line($pdf->left(), $y, $pdf->right(), $y, 1.2);
            } elseif ($sep) {
                $pdf->setColor(224, 232, 235);
                $pdf->line($pdf->left(), $y + $rh, $pdf->right(), $y + $rh, 0.5);
            }
            $pdf->advance($rh);
        };

        $pdf->books(24);
        $pdf->setFont('F2', 11);
        $pdf->setColor(7, 155, 181);
        $pdf->text($sec['titulo'], $pdf->left(), $pdf->posY());
        $pdf->advance(14);

        $drawHeader();

        if (!$rows) {
            $pdf->books($rowH + 6);
            $pdf->setFont('F1', $fs);
            $pdf->setColor(120, 120, 120);
            $pdf->text($sec['vacio'], $pdf->left() + $pad, $pdf->posY() + 5);
            $pdf->advance($rowH);
        } else {
            foreach ($rows as $i => $row) {
                $drawRow($row, $i % 2 === 0, false, $i !== count($rows) - 1);
            }
            $pdf->advance(3);
            $drawRow($sec['totales'], false, true);
        }

        $pdf->advance(16);
    }

    return $pdf->render();
}

function servir_docx(string $filename, string $bytes): void
{
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($bytes));
    header('Cache-Control: no-cache');
    echo $bytes;
    exit;
}

function servir_pdf(string $filename, string $bytes): void
{
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($bytes));
    header('Cache-Control: no-cache');
    echo $bytes;
    exit;
}