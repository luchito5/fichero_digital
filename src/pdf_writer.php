<?php
declare(strict_types=1);

final class MiniPdf
{
    private const W = 595.28;
    private const H = 841.89;

    private float $margin = 36;
    private array $pages = [];
    private string $cur = '';
    private float $y = 0;
    private string $font = 'F1';
    private int $size = 10;
    private float $cr = 0.0;
    private float $cg = 0.0;
    private float $cb = 0.0;

    public function __construct(float $margin = 36.0)
    {
        $this->margin = $margin;
        $this->newPage();
    }

    public function left(): float
    {
        return $this->margin;
    }

    public function right(): float
    {
        return self::W - $this->margin;
    }

    public function bottom(): float
    {
        return self::H - $this->margin;
    }

    public function newPage(): void
    {
        if ($this->cur !== '') {
            $this->pages[] = $this->cur;
        }
        $this->cur = '';
        $this->y = $this->margin;
    }

    public function books(float $height): void
    {
        if ($this->y + $height > $this->bottom()) {
            $this->newPage();
        }
    }

    public function setFont(string $face, int $size): void
    {
        $this->font = $face;
        $this->size = $size;
    }

    public function setColor(int $r, int $g, int $b): void
    {
        $this->cr = $r / 255;
        $this->cg = $g / 255;
        $this->cb = $b / 255;
    }

    public function text(string $s, float $x, float $top, string $align = 'left'): void
    {
        $s = $this->encode($s);
        $w = $this->estWidth($s, $this->size);
        if ($align === 'right') {
            $x = $this->right() - $w;
        } elseif ($align === 'center') {
            $x = ($this->left() + $this->right() - $w) / 2;
        }
        $baseline = self::H - ($top + $this->size * 0.82);
        $f = fn (float $v): string => number_format($v, 2, '.', '');
        $this->cur .= 'BT /' . $this->font . ' ' . $this->size . ' Tf '
            . $f($this->cr) . ' ' . $f($this->cg) . ' ' . $f($this->cb) . ' rg '
            . $f($x) . ' ' . $f($baseline) . ' Td (' . $this->escape($s) . ") Tj ET\n";
    }

    public function line(float $x1, float $y1, float $x2, float $y2): void
    {
        $f = fn (float $v): string => number_format($v, 2, '.', '');
        $this->cur .= $f($x1) . ' ' . $f(self::H - $y1) . ' m ' . $f($x2) . ' ' . $f(self::H - $y2) . " l S\n";
    }

    public function rect(float $x, float $y, float $w, float $h, bool $fill, bool $stroke = false): void
    {
        $f = fn (float $v): string => number_format($v, 2, '.', '');
        $py = self::H - ($y + $h);
        $op = $fill && $stroke ? 'B' : ($fill ? 'f' : 'S');
        $this->cur .= $f($x) . ' ' . $f($py) . ' ' . $f($w) . ' ' . $f($h) . " re $op\n";
    }

    public function wrap(string $s, float $maxW, int $size): array
    {
        $s = trim($this->encode($s));
        if ($s === '') {
            return [''];
        }
        $lines = [];
        $line = '';
        foreach (preg_split('/\s+/', $s) ?: [] as $word) {
            $test = $line === '' ? $word : $line . ' ' . $word;
            if ($this->estWidth($test, $size) > $maxW && $line !== '') {
                $lines[] = $line;
                $line = $word;
            } else {
                $line = $test;
            }
        }
        if ($line !== '') {
            $lines[] = $line;
        }
        return $lines;
    }

    public function posY(): float
    {
        return $this->y;
    }

    public function advance(float $h): void
    {
        $this->y += $h;
    }

    private function estWidth(string $s, int $size): float
    {
        return 0.54 * $size * strlen($s);
    }

    private function encode(string $s): string
    {
        $c = @iconv('UTF-8', 'Windows-1252//IGNORE', $s);
        return $c === false ? $s : $c;
    }

    private function escape(string $s): string
    {
        $s = preg_replace('/[\x00-\x1f\x7f]/', '', $s) ?? $s;
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $s);
    }

    public function render(): string
    {
        if ($this->cur !== '' || $this->pages === []) {
            $this->pages[] = $this->cur;
        }
        $this->cur = '';

        $nPages = count($this->pages);
        $contentIds = [];
        $pageIds = [];
        $next = 5;
        for ($i = 0; $i < $nPages; $i++) {
            $contentIds[] = $next++;
            $pageIds[] = $next++;
        }

        $out = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];

        $add = function (int $num, string $text) use (&$out, &$offsets): void {
            $offsets[$num] = strlen($out);
            $out .= $num . " 0 obj\n" . $text . "\nendobj\n";
        };
        $addStream = function (int $num, string $data) use (&$out, &$offsets): void {
            $offsets[$num] = strlen($out);
            $out .= $num . " 0 obj\n<< /Length " . strlen($data) . " >>\nstream\n" . $data . "\nendstream\nendobj\n";
        };

        $add(1, '<< /Type /Catalog /Pages 2 0 R >>');
        $kids = implode(' ', array_map(static fn ($p) => $p . ' 0 R', $pageIds));
        $add(2, '<< /Type /Pages /Kids [ ' . $kids . ' ] /Count ' . $nPages . ' >>');
        $add(3, '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>');
        $add(4, '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>');

        foreach ($this->pages as $i => $data) {
            $addStream($contentIds[$i], $data);
            $pageBody = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ' . self::W . ' ' . self::H . ']'
                . ' /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >>'
                . ' /Contents ' . $contentIds[$i] . ' 0 R >>';
            $add($pageIds[$i], $pageBody);
        }

        $xrefOffset = strlen($out);
        $out .= "xref\n0 $next\n";
        $out .= "0000000000 65535 f \n";
        for ($id = 1; $id < $next; $id++) {
            $out .= sprintf('%010d 00000 n ' . "\n", $offsets[$id]);
        }
        $out .= "trailer\n<< /Size $next /Root 1 0 R >>\nstartxref\n$xrefOffset\n%%EOF\n";
        return $out;
    }
}