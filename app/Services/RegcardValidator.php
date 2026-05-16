<?php

// app/Services/RegcardValidator.php
namespace App\Services;

use Smalot\PdfParser\Parser as PdfParser;
use Illuminate\Support\Str;

class RegcardValidator
{
    /**
     * Validate a registration card file against the logged-in student and current term.
     *
     * @param  string $publicPath storage/public-relative path, e.g. "regcards/2026-2027_1/7/file.pdf"
     * @param  object $u          student-like object (must have name + student_number fields or equivalents)
     * @param  object $term       term-like object (code/name or ay_start/ay_end + semester)
     * @return array{ok:bool, reason?:string}
     */
    public function validate(string $publicPath, object $u, object $term): array
    {
        $abs  = storage_path('app/public/' . ltrim($publicPath, '/'));
        $ext  = strtolower(pathinfo($publicPath, PATHINFO_EXTENSION));
        $text = '';

        try {
            if ($ext === 'pdf') {
                $parser = new PdfParser();
                $pdf    = $parser->parseFile($abs);
                $text   = $pdf->getText();
            } else {
                // Optional OCR for images (JPG/PNG). Requires tesseract-ocr + thiagoalessio/tesseract_ocr.
                if (class_exists(\TesseractOCR::class)) {
                    $text = (new \TesseractOCR($abs))->run();
                } else {
                    return $this->fail('Please upload a PDF or enable OCR (tesseract) for images.');
                }
            }
        } catch (\Throwable $e) {
            return $this->fail('Could not read file content.');
        }

        $text = $this->normalize($text);

        // --- Fetch student fields with robust fallbacks ---
        $name = $this->firstValue($u, ['name', 'full_name', 'student_name'])
            ?: $this->fullNameFromParts($u);
        $studno = $this->firstValue($u, [
            'student_number', 'student_no', 'stud_no', 'studentid', 'student_id_no', 'student_id'
        ]);

        if (!$name)   return $this->fail('Student name unavailable for matching.');
        if (!$studno) return $this->fail('Student number unavailable for matching.');

        // --- Checks on extracted text ---
        if (!$this->hasName($text, $name))                return $this->fail('Name not found / mismatched.');
        if (!$this->hasStudentNo($text, (string)$studno)) return $this->fail('Student number not found / mismatched.');
        if (!$this->hasBSN($text))                        return $this->fail('Course/Major must be BSN.');
        if (!$this->hasAllowedYearLevel($text))           return $this->fail('Only 2nd, 3rd, or 4th Year are allowed.');
        if (!$this->hasFirstSemester($text))              return $this->fail('Must be First Semester.');

        // --- AY + current term matching ---
        $ayFromCard = $this->extractAY($text);          // e.g., "2026-2027"
        if (!$ayFromCard)                                 return $this->fail('Academic Year (AY) not detected.');
        if (!$this->isAYAllowed($ayFromCard))             return $this->fail('AY must be 2026-2027 or later.');

        $termCode = $this->termCodeString($term);       // from code/name or ay_start/ay_end
        if (!$this->matchesCurrentTermAY($ayFromCard, $termCode)) {
            return $this->fail('AY/term does not match current term.');
        }

        return ['ok' => true];
    }

    /* ============================= Helpers ============================= */

    /**
     * Normalize document text for searching: lower-case, collapse whitespace (incl. NBSP),
     * normalize dashes, and unify AY phrases.
     */
    private function normalize(string $s): string
    {
        // Replace NBSP & thin spaces with regular spaces
        $s = preg_replace('/[\x{00A0}\x{2000}-\x{200B}]+/u', ' ', $s) ?? $s;
        // Normalize dashes to hyphen
        $s = str_replace(['–', '—'], '-', $s);

        // Lower + collapse whitespace (unicode-aware)
        $s = Str::of($s)->lower()->replaceMatches('/\s+/u', ' ')->value();

        // Normalize AY variants
        $s = str_replace(['a.y.', 'academic year'], 'ay', $s);

        // Remove diacritics if intl is available (safer matching)
        if (function_exists('transliterator_transliterate')) {
            $s = transliterator_transliterate('NFD; [:Nonspacing Mark:] Remove; NFC', $s);
        }

        return trim($s);
    }

    /**
     * Normalize a human name for tokenization (keeps hyphens and periods for initials).
     */
    private function normalizeForName(string $s): string
    {
        $s = preg_replace('/[\x{00A0}\x{2000}-\x{200B}]+/u', ' ', $s) ?? $s;
        $s = Str::of($s)->lower()->replaceMatches('/\s+/u', ' ')->value();

        if (function_exists('transliterator_transliterate')) {
            $s = transliterator_transliterate('NFD; [:Nonspacing Mark:] Remove; NFC', $s);
        }

        return trim($s);
    }

    /**
     * Split a full name into [first, middle, last] tokens (loose, unicode-aware).
     */
    private function splitNameTokens(string $full): array
    {
        $n = $this->normalizeForName($full);
        // Keep letters, spaces, hyphens, apostrophes and periods (for initials)
        $n = preg_replace("/[^\\p{L}\\s\\.\\-']+/u", ' ', $n) ?? $n;
        $n = preg_replace('/\s+/u', ' ', $n) ?? $n;

        $parts = preg_split('/\s+/u', $n, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $first = $parts[0] ?? '';
        $last  = $parts[count($parts) - 1] ?? '';
        $middle = count($parts) > 2 ? implode(' ', array_slice($parts, 1, -1)) : '';

        return [$first, $middle, $last];
    }

    /**
     * Robust name matcher:
     * - tolerates LAST, FIRST
     * - allows optional middles/initials (e.g., FIRST M. LAST)
     * - ignores NBSPs/extra spacing/punctuation variations
     */
    private function hasName(string $text, string $full): bool
    {
        // Text already normalized by validate()
        [$first, $middle, $last] = $this->splitNameTokens($full);

        if ($first === '' || $last === '') {
            return false;
        }

        // FIRST … LAST   (allow up to ~4 tokens between, e.g., multiple middles)
        $between = '(?:[\p{Z}\s,.\'-]*\b[\p{L}\-\.]+\b){0,4}';
        $reFirstLast = '/\b' . preg_quote($first, '/') . '\b' . $between . '[\p{Z}\s,.\'-]*\b' . preg_quote($last, '/') . '\b/u';
        if (preg_match($reFirstLast, $text)) {
            return true;
        }

        // LAST, FIRST
        $reLastFirst = '/\b' . preg_quote($last, '/') . '\b[\p{Z}\s,.\'-]*\b' . preg_quote($first, '/') . '\b/u';
        if (preg_match($reLastFirst, $text)) {
            return true;
        }

        // FIRST M. LAST (middle as initial)
        if ($middle !== '') {
            $mi = mb_substr($middle, 0, 1);
            $reMi = '/\b' . preg_quote($first, '/') . '\b[\p{Z}\s,.\'-]*' .
                    preg_quote($mi, '/') . '\.?[\p{Z}\s,.\'-]*\b' .
                    preg_quote($last, '/') . '\b/u';
            if (preg_match($reMi, $text)) {
                return true;
            }
        }

        // Fallbacks: contiguous “first last” or full string (both space-squished & lowercased)
        $noMid = trim($first . ' ' . $last);
        if ($noMid !== '' && str_contains($text, $noMid)) {
            return true;
        }
        $squishedFull = $this->normalizeForName($full);
        if ($squishedFull !== '' && str_contains($text, $squishedFull)) {
            return true;
        }

        return false;
    }

    private function hasStudentNo(string $text, string $studno): bool
    {
        $studno = strtolower($studno);
        if ($studno !== '' && str_contains($text, $studno)) return true;

        // Allow numeric-only matching ignoring hyphens/spaces
        $plainDoc  = preg_replace('/\D+/', '', $text);
        $plainStud = preg_replace('/\D+/', '', $studno);
        return $plainStud !== '' && str_contains($plainDoc, $plainStud);
    }

    private function hasBSN(string $text): bool
    {
        if (str_contains($text, 'bachelor of science in nursing')) return true;
        if (str_contains($text, 'bs in nursing'))                  return true;
        if (preg_match('/\bbsn\b/', $text))                        return true;

        // If the card prints "Course / Major : ____", check that segment
        if (preg_match('/course\s*\/?\s*major\s*:\s*(.+?)\b(?:year|term|semester|type)\b/u', $text, $m)) {
            return str_contains($m[1], 'nursing');
        }
        return false;
    }

    private function hasAllowedYearLevel(string $text): bool
    {
        return preg_match('/\b(2nd|second|3rd|third|4th|fourth)\s+year\b/u', $text) === 1;
    }

    private function hasFirstSemester(string $text): bool
    {
        return preg_match('/\b(1st|first)\s+semester\b/u', $text) === 1;
    }

    private function extractAY(string $text): ?string
    {
        // matches: "ay 2026-2027", "ay: 2026-2027", etc. (dashes normalized earlier)
        if (preg_match('/ay[^0-9]*([0-9]{4})\s*-\s*([0-9]{4})/u', $text, $m)) {
            return $m[1] . '-' . $m[2];
        }
        return null;
    }

    private function isAYAllowed(string $ay): bool
    {
        if (!preg_match('/^(\d{4})-(\d{4})$/', $ay, $m)) return false;
        $start = (int)$m[1]; $end = (int)$m[2];
        return $start >= 2026 && $end === $start + 1;
    }

    private function matchesCurrentTermAY(string $ay, string $termCode): bool
    {
        $t = strtolower($termCode);
        if ($t === '') return false;

        // Straight substring or regex-matched AY
        if (str_contains($t, strtolower($ay))) return true;

        $ayEsc = preg_quote($ay, '/');
        if (preg_match("/{$ayEsc}\b/u", $t)) return true;

        // Allow codes like "2026-2027-1" (our numeric semester code)
        $code = substr($ay, 0, 4) . '-' . substr($ay, 5, 4) . '-1';
        return str_contains($t, $code);
    }

    private function termCodeString(object $term): string
    {
        // Prefer provided code/name
        if (!empty($term->code)) return (string)$term->code;
        if (!empty($term->name)) return (string)$term->name;

        // Else try to synthesize from AY start/end + semester
        $start = $term->ay_start ?? $term->year_start ?? null;
        $end   = $term->ay_end   ?? $term->year_end   ?? null;
        $sem   = $term->semester ?? $term->term       ?? null;

        if ($start && $end) {
            $semPart = '';
            if ($sem !== null) {
                // accept 1|2|3 or text
                if (is_numeric($sem)) {
                    $semPart = '-' . (int)$sem;
                } else {
                    $semPart = ' ' . strtolower((string)$sem);
                }
            }
            return "AY {$start}-{$end}{$semPart}";
        }

        // Last resort
        return 'current term';
    }

    private function firstValue(object $o, array $fields): ?string
    {
        foreach ($fields as $f) {
            if (isset($o->{$f}) && $o->{$f} !== '') return (string)$o->{$f};
        }
        return null;
    }

    private function fullNameFromParts(object $o): ?string
    {
        $first = $this->firstValue($o, ['first_name', 'fname', 'given_name']) ?? '';
        $mid   = $this->firstValue($o, ['middle_name', 'mname', 'mi']) ?? '';
        $last  = $this->firstValue($o, ['last_name', 'lname', 'surname', 'family_name']) ?? '';
        $full  = trim($first . ' ' . trim($mid) . ' ' . $last);
        return $full !== '' ? $full : null;
    }

    private function fail(string $reason): array
    {
        return ['ok' => false, 'reason' => $reason];
    }
}
