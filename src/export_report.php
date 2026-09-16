<?php
declare(strict_types=1);

require_once __DIR__ . '/admin_functions.php';
require_once __DIR__ . '/pdf_writer.php';
require_once __DIR__ . '/docx_writer.php';

function reporte_datos(string $mes, int $idUsuario = 0, string $contrato = ''): array
{
    $cfg = system_config();
    $nombresMes = [
        1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre',
    ];
    $idx = (int)substr($mes, 5, 2);
    $nombreMes = ucfirst($nombresMes[$idx] . ' de ' . substr($mes, 0, 4));

    $porEmp = resumen_por_empleado($mes, $idUsuario, $contrato);
    $fichajes = resumen_fichajes($mes, $idUsuario, $contrato);
    $cirugias = resumen_cirugias($mes, $idUsuario, $contrato);
    $alquileres = resumen_alquileres($mes);
    $novedades = resumen_novedades($mes, $idUsuario, $contrato);

    $horas = array_reduce($porEmp, static fn ($carry, $r) => $carry + (float)$r['horas'], 0.0);
    $hsAlquiler = array_reduce($alquileres, static fn ($carry, $r) => $carry + (float)($r['horas_uso'] ?? 0), 0.0);

    $nombreEmp = 'Todos los empleados';
    if ($idUsuario > 0) {
        $f = find_employee($idUsuario);
        if ($f) {
            $nombreEmp = trim($f['nombre'] . ' ' . $f['apellido']);
        }
    }
    $nombreContrato = $contrato !== '' ? $contrato : 'Todos';
    $institucion = trim((string)($cfg['nombre_institucion'] ?? 'AMEMT'));

    $fmt = static fn ($v) => trim(rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.'));

    $stats = [
        ['Empleados', (string)count($porEmp), 'activos'],
        ['Horas trabajadas', $fmt($horas) . ' h', 'según fichajes'],
        ['Fichajes', (string)count($fichajes), 'registros'],
        ['Cirugías', (string)count($cirugias), 'procedimientos'],
        ['Alquileres', (string)count($alquileres), $fmt($hsAlquiler) . ' h de uso'],
        ['Novedades', (string)count($novedades), 'ausencias'],
    ];

    $secciones = [];

    $secciones[] = ['h' => 'Resumen por empleado'];
    $rowsEmp = [];
    foreach ($porEmp as $r) {
        $rowsEmp[] = [
            trim($r['nombre'] . ' ' . $r['apellido']),
            $r['tipo_contrato'],
            (string)(int)$r['fichajes'],
            $fmt($r['horas']) . ' h',
            (string)(int)$r['cirugias'],
            (string)(int)$r['novedades'],
        ];
    }
    $secciones[] = [
        't' => [
            'cols' => ['Empleado', 'Contrato', 'Fichajes', 'Horas', 'Cirugías', 'Novedades'],
            'rows' => $rowsEmp,
            'widths' => [0.30, 0.16, 0.12, 0.14, 0.13, 0.15],
        ],
        'vacio' => 'No hay empleados para los filtros seleccionados.',
    ];

    $secciones[] = ['h' => 'Fichajes del mes'];
    $rowsF = [];
    foreach ($fichajes as $r) {
        $rowsF[] = [
            $r['fecha'],
            $r['empleado'],
            $r['tipo_contrato'],
            $r['entrada'] ?? '-',
            $r['salida'] ?? '-',
            $r['horas_trabajadas'] !== null ? $fmt((float)$r['horas_trabajadas']) . ' h' : '-',
            (int)$r['validado_admin'] ? 'Sí' : 'No',
        ];
    }
    $secciones[] = [
        't' => [
            'cols' => ['Fecha', 'Empleado', 'Contrato', 'Entrada', 'Salida', 'Horas', 'Validado'],
            'rows' => $rowsF,
            'widths' => [0.15, 0.24, 0.14, 0.11, 0.11, 0.11, 0.14],
        ],
        'vacio' => 'No hay fichajes registrados en este período.',
    ];

    $secciones[] = ['h' => 'Cirugías del mes'];
    $rowsC = [];
    foreach ($cirugias as $r) {
        $rowsC[] = [
            $r['fecha'],
            $r['hora_inicio'],
            $r['procedimiento'],
            $r['personal'],
            $r['observaciones'] ?? '-',
        ];
    }
    $secciones[] = [
        't' => [
            'cols' => ['Fecha', 'Hora', 'Procedimiento', 'Personal', 'Observaciones'],
            'rows' => $rowsC,
            'widths' => [0.13, 0.09, 0.17, 0.38, 0.23],
        ],
        'vacio' => 'No hay cirugías registradas en este período.',
    ];

    $secciones[] = ['h' => 'Alquileres del mes'];
    $rowsA = [];
    foreach ($alquileres as $r) {
        $rowsA[] = [
            $r['fecha'],
            $r['responsable'],
            $r['institucion'],
            $r['entrada'] ?? '-',
            $r['salida'] ?? '-',
            $r['horas_uso'] !== null ? $fmt((float)$r['horas_uso']) . ' h' : '-',
            $r['dato_facturacion'] ?: '-',
        ];
    }
    $secciones[] = [
        't' => [
            'cols' => ['Fecha', 'Responsable', 'Institución', 'Entrada', 'Salida', 'Hs', 'Facturación'],
            'rows' => $rowsA,
            'widths' => [0.12, 0.19, 0.16, 0.10, 0.10, 0.08, 0.25],
        ],
        'vacio' => 'No hay alquileres registrados en este período.',
    ];

    $secciones[] = ['h' => 'Novedades del mes'];
    $rowsN = [];
    foreach ($novedades as $r) {
        $rowsN[] = [
            $r['empleado'],
            $r['tipo_contrato'],
            $r['tipo'],
            $r['fecha_desde'] ?? '-',
            $r['fecha_hasta'] ?? '-',
            $r['observaciones'] ?: '-',
        ];
    }
    $secciones[] = [
        't' => [
            'cols' => ['Empleado', 'Contrato', 'Tipo', 'Desde', 'Hasta', 'Observaciones'],
            'rows' => $rowsN,
            'widths' => [0.22, 0.14, 0.14, 0.12, 0.12, 0.26],
        ],
        'vacio' => 'No hay novedades registradas en este período.',
    ];

    return [
        'institucion' => $institucion,
        'titulo' => 'Resumen mensual',
        'periodo' => $nombreMes,
        'mes' => $mes,
        'filtros' => [
            'Periodo: ' . $nombreMes,
            'Empleado: ' . $nombreEmp,
            'Contrato: ' . $nombreContrato,
        ],
        'stats' => $stats,
        'secciones' => $secciones,
    ];
}

function generar_docx(array $d): string
{
    $body = '';
    $body .= docx_paragraph(trim($d['institucion']), ['align' => 'center', 'bold' => true, 'size' => 28, 'color' => '079BB5', 'after' => 0]);
    $body .= docx_paragraph($d['titulo'] . ' — ' . $d['periodo'], ['align' => 'center', 'bold' => true, 'size' => 30, 'after' => 60]);
    $body .= docx_paragraph(implode('  ·  ', $d['filtros']), ['align' => 'center', 'size' => 16, 'color' => '555555', 'after' => 200]);

    $body .= '<w:tbl><w:tblPr>'
        . '<w:tblW w:w="9360" w:type="dxa"/>'
        . '<w:tblBorders>'
        . '<w:top w:val="single" w:sz="4" w:space="0" w:color="98B3BA"/>'
        . '<w:left w:val="single" w:sz="4" w:space="0" w:color="98B3BA"/>'
        . '<w:bottom w:val="single" w:sz="4" w:space="0" w:color="98B3BA"/>'
        . '<w:right w:val="single" w:sz="4" w:space="0" w:color="98B3BA"/>'
        . '<w:insideH w:val="single" w:sz="4" w:space="0" w:color="98B3BA"/>'
        . '<w:insideV w:val="single" w:sz="4" w:space="0" w:color="98B3BA"/>'
        . '</w:tblBorders>'
        . '<w:tblLayout w:type="fixed"/>'
        . '</w:tblPr>'
        . '<w:tblGrid>'
        . '<w:gridCol w:w="3120"/>'
        . '<w:gridCol w:w="3120"/>'
        . '<w:gridCol w:w="3120"/>'
        . '</w:tblGrid>';

    foreach (array_chunk($d['stats'], 3) as $row) {
        $body .= '<w:tr>';
        foreach ($row as $cell) {
            $body .= '<w:tc><w:tcPr><w:tcW w:w="3120" w:type="dxa"/>'
                . '<w:shd w:val="clear" w:color="auto" w:fill="0D8A9C"/></w:tcPr>'
                . '<w:p><w:pPr><w:spacing w:before="40" w:after="20"/></w:pPr>'
                . '<w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/>'
                . '<w:color w:val="FFFFFF"/><w:sz w:val="16"/></w:rPr>'
                . '<w:t xml:space="preserve">' . docx_escape($cell[0]) . '</w:t></w:r></w:p>'
                . '<w:p><w:pPr><w:spacing w:before="0" w:after="20"/></w:pPr>'
                . '<w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/>'
                . '<w:b/><w:color w:val="FFFFFF"/><w:sz w:val="28"/></w:rPr>'
                . '<w:t xml:space="preserve">' . docx_escape($cell[1]) . '</w:t></w:r></w:p>'
                . '<w:p><w:pPr><w:spacing w:before="0" w:after="40"/></w:pPr>'
                . '<w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/>'
                . '<w:color w:val="D0E8EC"/><w:sz w:val="14"/></w:rPr>'
                . '<w:t xml:space="preserve">' . docx_escape($cell[2]) . '</w:t></w:r></w:p>'
                . '</w:tc>';
        }
        $body .= '</w:tr>';
    }
    $body .= '</w:tbl>';

    foreach ($d['secciones'] as $sec) {
        if (isset($sec['h'])) {
            $body .= docx_paragraph($sec['h'], ['bold' => true, 'size' => 24, 'color' => '0F7080', 'after' => 80, 'before' => 160]);
            continue;
        }
        $t = $sec['t'];
        $body .= docx_table($t['cols'], $t['rows'], $t['widths'], $sec['vacio'] ?? '');
        $body .= docx_paragraph('', ['size' => 4, 'after' => 80]);
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

    $pdf->setFont('F2', 18);
    $pdf->setColor(7, 155, 181);
    $pdf->text(trim($d['institucion']), 0, $pdf->posY(), 'center');
    $pdf->advance(22);

    $pdf->setFont('F2', 14);
    $pdf->setColor(17, 17, 17);
    $pdf->text($d['titulo'] . ' — ' . $d['periodo'], 0, $pdf->posY(), 'center');
    $pdf->advance(18);

    $pdf->setFont('F1', 8);
    $pdf->setColor(90, 90, 90);
    $pdf->text(implode('  |  ', $d['filtros']), 0, $pdf->posY(), 'center');
    $pdf->advance(16);

    $pdf->line($pdf->left(), $pdf->posY(), $pdf->right(), $pdf->posY());
    $pdf->advance(10);

    $pw = $pdf->right() - $pdf->left();
    $boxW = ($pw - 10) / 3;
    $boxH = 38;
    foreach ($d['stats'] as $i => $s) {
        $col = $i % 3;
        if ($col === 0) {
            $pdf->books($boxH + 6);
            $startY = $pdf->posY();
        }
        $x = $pdf->left() + $col * ($boxW + 5);
        $pdf->setColor(7, 155, 181);
        $pdf->rect($x, $startY, $boxW, $boxH, true);
        $pdf->setColor(255, 255, 255);
        $pdf->setFont('F1', 8);
        $pdf->text($s[0], $x + 8, $startY + 5);
        $pdf->setFont('F2', 14);
        $pdf->text($s[1], $x + 8, $startY + 15);
        $pdf->setFont('F1', 7);
        $pdf->text($s[2], $x + 8, $startY + 28);
        if ($col === 2 || $i === count($d['stats']) - 1) {
            $pdf->advance($boxH + 6);
        }
    }

    $pdf->advance(8);
    $pdf->setColor(17, 17, 17);

    foreach ($d['secciones'] as $sec) {
        if (isset($sec['h'])) {
            $pdf->books(30);
            $pdf->setFont('F2', 12);
            $pdf->setColor(15, 112, 128);
            $pdf->text($sec['h'], $pdf->left(), $pdf->posY());
            $pdf->advance(16);
            $pdf->setColor(7, 155, 181);
            $pdf->line($pdf->left(), $pdf->posY(), $pdf->right(), $pdf->posY());
            $pdf->advance(12);
            $pdf->setColor(17, 17, 17);
            continue;
        }

        $t = $sec['t'];
        $cols = $t['cols'];
        $rows = $t['rows'];
        $widths = $t['widths'];
        $colW = array_map(static fn ($w) => $w * $pw, $widths);
        $fs = 8;
        $rowH = $fs * 1.7;
        $pad = 4;

        $pdf->books(30);

        $pdf->setColor(7, 155, 181);
        $pdf->rect($pdf->left(), $pdf->posY(), $pw, $rowH, true);
        $pdf->setColor(255, 255, 255);
        $pdf->setFont('F2', $fs + 1);
        $cx = $pdf->left();
        foreach ($cols as $ci => $c) {
            $pdf->text($c, $cx + $pad, $pdf->posY() + 3);
            $cx += $colW[$ci];
        }
        $pdf->advance($rowH);
        $pdf->setColor(17, 17, 17);

        if (!$rows) {
            $pdf->setFont('F1', $fs);
            $pdf->text($sec['vacio'] ?? 'Sin registros', $pdf->left() + $pad, $pdf->posY() + 4);
            $pdf->advance($rowH + 4);
        } else {
            foreach ($rows as $ri => $row) {
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

                if ($ri % 2 === 0) {
                    $pdf->setColor(228, 240, 243);
                    $pdf->rect($pdf->left(), $pdf->posY(), $pw, $rh, true);
                    $pdf->setColor(17, 17, 17);
                }

                $cellY = $pdf->posY();
                $cx = $pdf->left();
                foreach ($row as $ci => $val) {
                    $pdf->setFont('F1', $fs);
                    foreach ($lines[$ci] as $li => $ln) {
                        $pdf->text($ln, $cx + $pad, $cellY + $pad + $li * $fs * 1.45);
                    }
                    $cx += $colW[$ci];
                }
                $pdf->line($pdf->left(), $cellY + $rh, $pdf->right(), $cellY + $rh);
                $pdf->advance($rh);
            }
        }
        $pdf->advance(14);
    }

    return $pdf->render();
}

function servir_docx(string $filename, string $bytes): void
{
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache');
    echo $bytes;
    exit;
}

function servir_pdf(string $filename, string $bytes): void
{
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache');
    echo $bytes;
    exit;
}