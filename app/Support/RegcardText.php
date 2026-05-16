<?php

namespace App\Support;

class RegcardText
{
    public static function extract(string $absPath, string $ext): string
    {
        $ext = strtolower($ext);
        if ($ext === 'pdf') {
            $bin = strtoupper(substr(PHP_OS,0,3))==='WIN' ? 'pdftotext.exe' : 'pdftotext';
            @exec("$bin -v", $o, $ok);
            if ($ok===0) {
                $txt = $absPath.'.txt';
                @exec("$bin -layout -nopgbrk ".escapeshellarg($absPath).' '.escapeshellarg($txt));
                if (is_file($txt)) { $t = @file_get_contents($txt) ?: ''; @unlink($txt); return $t; }
            }
            $b = @file_get_contents($absPath) ?: '';
            return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]+/u',' ',$b) ?: '';
        }
        return ''; // images: fail-closed until OCR is added
    }

    public static function parseMeta(string $text): array
    {
        $meta = ['is_bsn'=>false,'year'=>null,'semester'=>null,'ay'=>null];
        $hay  = mb_strtolower($text);

        $meta['is_bsn'] = strpos($hay,'bachelor of science in nursing')!==false || preg_match('/\bbsn\b/i',$text);

        if (preg_match('/\b(First|Second)\s+Semester,\s*AY\s*(\d{4})[-–](\d{4})/i',$text,$m)) {
            $meta['semester'] = strtolower($m[1])==='first' ? 1 : 2;
            $meta['ay'] = [$m[2], $m[3]];
        }
        if (preg_match('/Year\s*Level\s*:\s*(First|Second|Third|Fourth)\s+Year/i',$text,$m)
            || preg_match('/\b(First|Second|Third|Fourth)\s+Year\b/i',$text,$m)) {
            $map = ['first'=>1,'second'=>2,'third'=>3,'fourth'=>4];
            $meta['year'] = $map[strtolower($m[1])] ?? null;
        }
        return $meta;
    }
}
