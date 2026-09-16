<?php
declare(strict_types=1);

final class DocxWriter
{
    private array $parts = [];

    public function add(string $name, string $content): void
    {
        $this->parts[$name] = $content;
    }

    public function build(): string
    {
        return $this->zipStore($this->parts);
    }

    private function zipStore(array $parts): string
    {
        $out = '';
        $central = '';
        $offset = 0;
        foreach ($parts as $name => $data) {
            $crc = strrev(pack('H*', hash('crc32b', $data)));
            $nameLen = strlen($name);
            $size = strlen($data);

            $out .= "\x50\x4b\x03\x04"
                . pack('v', 20)
                . pack('v', 0)
                . pack('v', 0)
                . pack('v', 0)
                . pack('v', 0)
                . $crc
                . pack('V', $size)
                . pack('V', $size)
                . pack('v', $nameLen)
                . pack('v', 0)
                . $name
                . $data;

            $central .= "\x50\x4b\x01\x02"
                . pack('v', 20)
                . pack('v', 20)
                . pack('v', 0)
                . pack('v', 0)
                . pack('v', 0)
                . pack('v', 0)
                . $crc
                . pack('V', $size)
                . pack('V', $size)
                . pack('v', $nameLen)
                . pack('v', 0)
                . pack('v', 0)
                . pack('v', 0)
                . pack('v', 0)
                . pack('V', 0)
                . pack('V', $offset)
                . $name;

            $offset = strlen($out);
        }

        $cdOffset = strlen($out);
        $out .= $central;
        $out .= "\x50\x4b\x05\x06"
            . pack('v', 0)
            . pack('v', 0)
            . pack('v', count($parts))
            . pack('v', count($parts))
            . pack('V', strlen($central))
            . pack('V', $cdOffset)
            . pack('v', 0);

        return $out;
    }
}

function docx_escape(string $s): string
{
    return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function docx_paragraph(string $text, array $opts = []): string
{
    $align = (string)($opts['align'] ?? '');
    $jc = $align !== '' ? '<w:jc w:val="' . $align . '"/>' : '';
    $b = !empty($opts['bold']) ? '<w:b/>' : '';
    $color = (string)($opts['color'] ?? '222222');
    $size = (int)($opts['size'] ?? 22);
    $before = (int)($opts['before'] ?? 0);
    $after = (int)($opts['after'] ?? 120);
    return '<w:p><w:pPr>' . $jc . '<w:spacing w:before="' . $before . '" w:after="' . $after . '"/></w:pPr>'
        . '<w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/>' . $b
        . '<w:color w:val="' . $color . '"/><w:sz w:val="' . $size . '"/></w:rPr>'
        . '<w:t xml:space="preserve">' . docx_escape($text) . '</w:t></w:r></w:p>';
}

function docx_cell(string $text, float $wt, bool $head, int $pw): string
{
    $shd = $head ? '<w:shd w:val="clear" w:color="auto" w:fill="079BB5"/>' : '';
    $b = $head ? '<w:b/>' : '';
    $color = $head ? 'FFFFFF' : '222222';
    return '<w:tc><w:tcPr><w:tcW w:w="' . (int)round($wt * $pw) . '" w:type="dxa"/>' . $shd . '</w:tcPr>'
        . '<w:p><w:pPr><w:spacing w:before="30" w:after="30"/></w:pPr>'
        . '<w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/>' . $b
        . '<w:color w:val="' . $color . '"/><w:sz w:val="18"/></w:rPr>'
        . '<w:t xml:space="preserve">' . docx_escape($text) . '</w:t></w:r></w:p></w:tc>';
}

function docx_table(array $cols, array $rows, array $widths, string $emptyMsg, int $pw = 9360): string
{
    $grid = implode('', array_map(
        static fn ($w) => '<w:gridCol w:w="' . (int)round($w * $pw) . '"/>',
        $widths
    ));

    $header = '<w:tr><w:trPr><w:tblHeader/></w:trPr>'
        . implode('', array_map(
            static fn ($c, $w) => docx_cell($c, $w, true, $pw),
            $cols,
            $widths
        ))
        . '</w:tr>';

    if (!$rows) {
        $rows = [[$emptyMsg]];
    }

    $body = '';
    foreach ($rows as $r) {
        $tds = '';
        foreach ($r as $i => $val) {
            $tds .= docx_cell((string)$val, $widths[$i] ?? 0.1, false, $pw);
        }
        $body .= '<w:tr>' . $tds . '</w:tr>';
    }

    return '<w:tbl><w:tblPr>'
        . '<w:tblW w:w="' . $pw . '" w:type="dxa"/>'
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
        . '<w:tblGrid>' . $grid . '</w:tblGrid>'
        . $header . $body
        . '</w:tbl>';
}

function docx_document(string $body): string
{
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
        . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" '
        . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        . '<w:body>'
        . $body
        . '<w:sectPr/>'
        . '</w:body>'
        . '</w:document>';
}