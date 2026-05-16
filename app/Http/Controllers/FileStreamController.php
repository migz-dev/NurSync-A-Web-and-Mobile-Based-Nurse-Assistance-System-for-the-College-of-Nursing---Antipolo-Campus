<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileStreamController extends Controller
{
    public function procedure(Request $request, string $path): StreamedResponse
    {
        // allow only safe filenames like "abc123.pdf"
        $clean = preg_replace('/[^A-Za-z0-9._-]/', '', $path);
        if ($clean !== $path || $clean === '' || str_contains($clean, '..')) {
            abort(403);
        }

        // Try both expected locations (your files are in the second one right now)
        $candidates = [
            storage_path("app/public/procedures/{$clean}"),
            storage_path("app/private/public/procedures/{$clean}"),
        ];

        $full = null;
        foreach ($candidates as $p) {
            if (is_file($p)) { $full = $p; break; }
        }
        abort_unless($full && is_readable($full), 404);

        $download  = $request->boolean('download');
        $name      = $request->query('name', $clean);

        if ($download) {
            return response()->streamDownload(function () use ($full) {
                $in = fopen($full, 'rb');
                while (!feof($in)) { echo fread($in, 1048576); flush(); }
                fclose($in);
            }, $name, [
                'Content-Type'  => 'application/pdf',
                'Cache-Control' => 'private, max-age=0, must-revalidate',
            ]);
        }

        return response()->stream(function () use ($full) {
            $in = fopen($full, 'rb');
            while (!feof($in)) { echo fread($in, 1048576); flush(); }
            fclose($in);
        }, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$name}\"",
            'Cache-Control'       => 'private, max-age=0, must-revalidate',
        ]);
    }
}
