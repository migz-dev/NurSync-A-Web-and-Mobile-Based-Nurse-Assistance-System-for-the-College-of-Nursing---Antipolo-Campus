<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Procedure;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class AdminProcedureController extends Controller
{
    /**
     * List procedures with search + status + ward + archived filters (AJAX-ready).
     * Default: show NON-archived only; sort by title ASC.
     */
    public function index(Request $req)
    {
        $q         = trim((string) $req->query('q', ''));
        $status    = trim((string) $req->query('status', ''));   // 'draft' | 'published' | '' (all)
        $ward      = trim((string) $req->query('ward', ''));     // optional exact match
        $archived  = $req->boolean('archived', false) || $status === 'archived';
        $perPage   = (int) $req->query('per_page', 10);
        $perPage   = ($perPage >= 1 && $perPage <= 100) ? $perPage : 10;

        $procedures = Procedure::query()
            ->with(['author', 'adminCreator'])

            // archived toggle
            ->when(!$archived, fn ($q) => $q->where('is_archived', 0))
            ->when($archived,  fn ($q) => $q->where('is_archived', 1))

            // search (use scope if available)
            ->when(
                method_exists(Procedure::class, 'scopeSearch'),
                fn ($q2) => $q2->search($q ?: null),
                fn ($q2) => $q !== '' ? $q2->where(function ($s) use ($q) {
                    $s->where('title', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%");
                }) : null
            )

            // status filter (ignore literal "archived")
            ->when(
                $status !== '' && $status !== 'archived',
                fn ($q2) => method_exists(Procedure::class, 'scopeStatus')
                    ? $q2->status($status)
                    : $q2->where('status', $status)
            )

            // ward filter
            ->when($ward !== '', fn ($q2) => $q2->where('clinical_wards', $ward))

            ->orderBy('title', 'asc')
            ->paginate($perPage)
            ->appends($req->only(['q', 'status', 'ward', 'per_page', 'archived']));

        // AJAX partials
        if ($req->ajax()) {
            $rows  = View::make('admin.procedures._rows',  compact('procedures', 'archived'))->render();
            $pager = View::make('admin.procedures._pager', compact('procedures', 'archived'))->render();

            return response()->json([
                'rows'     => $rows,
                'pager'    => $pager,
                'archived' => $archived,
            ]);
        }

        // Full page
$view = $archived
    ? 'admin.admin-resources-archived'   // <— matches your file path
    : 'admin.admin-resources';

return view($view, compact('procedures', 'archived'));
    }

    /**
     * Dedicated Archived page: /admin/procedures/archives
     * Reuses index() logic but forces archived=1.
     */
    public function archived(Request $req)
    {
        $req->merge(['archived' => 1]);
        return $this->index($req);
    }

    /** Create page (Admin) */
    public function create()
    {
        return view('admin.procedures.create');
    }

    /** Store – create procedure; redirect to Edit to add steps. */
    public function store(Request $req)
    {
        $data = $req->validate([
            'title'           => ['required','string','max:200'],
            'level'           => ['nullable','in:beginner,intermediate,advanced'],
            'clinical_wards'  => ['nullable','string','max:255'],
            'description'     => ['nullable','string','max:5000'],
            'ppe_csv'         => ['nullable','string','max:1000'],
            'tags_csv'        => ['nullable','string','max:1000'],
            'video_url'       => ['nullable','string','max:1000'],
            'video_file'      => ['nullable','file','mimetypes:video/mp4,video/webm,video/ogg','max:204800'],
            'hazards_text'    => ['nullable','string','max:5000'],
            'action'          => ['nullable','in:draft,publish'],
        ]);

        $p = new Procedure();
        $p->title          = $data['title'];
        $p->level          = $data['level'] ?? 'beginner';
        $p->clinical_wards = $data['clinical_wards'] ?? null;
        $p->description    = $data['description'] ?? null;
        $p->hazards_text   = $data['hazards_text'] ?? null;

        $p->ppe_json  = $this->csvToArray($data['ppe_csv']  ?? '');
        $p->tags_json = $this->csvToArray($data['tags_csv'] ?? '');

        if ($req->hasFile('video_file')) {
            $path = $req->file('video_file')->store('procedures/videos', 'public');
            $p->video_path = Storage::url($path);
            $p->video_url  = null;
        } else {
            $p->video_url  = $this->normalizeVideoUrl($data['video_url'] ?? null);
            $p->video_path = null;
        }

        $p->status       = ($data['action'] ?? 'draft') === 'publish' ? 'published' : 'draft';
        $p->published_at = $p->status === 'published' ? now() : null;

        $adminId = auth('admin')->id();
        $p->created_by_admin = $adminId;
        $p->updated_by_admin = $adminId;

        $p->ensureSlug();
        $p->save();

        return redirect()->route('admin.procedures.edit', $p)
            ->with('ok', 'Draft created. You can add steps now.');
        }

    /** Review page */
    public function show(Procedure $procedure)
    {
        $procedure->load([
            'steps',
            'attachments',
            'author',
            'adminCreator',
            'editorFaculty',
            'adminEditor',
        ]);

        return view('admin.procedures.show', compact('procedure'));
    }

    /** Edit page */
    public function edit(Procedure $procedure)
    {
        $procedure->load('steps');
        return view('admin.procedures.edit', compact('procedure'));
    }

    /** Update meta + steps */
    public function update(Request $req, Procedure $procedure)
    {
        $data = $req->validate([
            'title'                       => ['required','string','max:200'],
            'level'                       => ['nullable','in:beginner,intermediate,advanced'],
            'clinical_wards'              => ['nullable','string','max:255'],
            'description'                 => ['nullable','string','max:5000'],
            'ppe_csv'                     => ['nullable','string','max:1000'],
            'tags_csv'                    => ['nullable','string','max:1000'],
            'video_url'                   => ['nullable','string','max:1000'],
            'video_file'                  => ['nullable','file','mimetypes:video/mp4,video/webm,video/ogg','max:204800'],
            'remove_video'                => ['nullable','boolean'],
            'hazards_text'                => ['nullable','string','max:5000'],
            'action'                      => ['nullable','in:draft,publish'],
            'steps'                       => ['array'],
            'steps.*.step_no'             => ['nullable','integer','min:1'],
            'steps.*.title'               => ['nullable','string','max:255'],
            'steps.*.body'                => ['nullable','string','max:5000'],
            'steps.*.rationale'           => ['nullable','string','max:2000'],
            'steps.*.caution'             => ['nullable','string','max:2000'],
            'steps.*.duration_seconds'    => ['nullable','integer','min:0'],
            'steps.*.video_url'           => ['nullable','string','max:1000'],
            'steps.*.video_file'          => ['nullable','file','mimetypes:video/mp4,video/webm,video/ogg','max:204800'],
            'steps.*.remove_video'        => ['nullable','boolean'],
        ]);

        DB::transaction(function () use ($req, $procedure, $data) {
            // meta
            $procedure->title          = $data['title'];
            $procedure->level          = $data['level'] ?? $procedure->level;
            $procedure->clinical_wards = $data['clinical_wards'] ?? $procedure->clinical_wards;
            $procedure->description    = $data['description'] ?? null;
            $procedure->hazards_text   = $data['hazards_text'] ?? null;

            $procedure->ppe_json  = $this->csvToArray($data['ppe_csv']  ?? '');
            $procedure->tags_json = $this->csvToArray($data['tags_csv'] ?? '');

            if ($req->boolean('remove_video')) {
                $procedure->video_url  = null;
                $procedure->video_path = null;
            } elseif ($req->hasFile('video_file')) {
                $path = $req->file('video_file')->store('procedures/videos', 'public');
                $procedure->video_path = Storage::url($path);
                $procedure->video_url  = null;
            } else {
                $procedure->video_url = $this->normalizeVideoUrl($data['video_url'] ?? null);
            }

            if (!empty($data['action'])) {
                $procedure->status       = $data['action'] === 'publish' ? 'published' : 'draft';
                $procedure->published_at = $procedure->status === 'published' ? now() : null;
            }

            $procedure->updated_by_admin = auth('admin')->id();
            $procedure->ensureSlug();
            $procedure->save();

            // steps (replace-all if provided)
            $incoming = $req->input('steps', []);
            $clean = [];

            foreach ($incoming as $i => $step) {
                $body = trim((string) ($step['body'] ?? ''));
                if ($body === '') continue;

                $remove = $req->boolean("steps.$i.remove_video");
                $file   = $req->file("steps.$i.video_file");
                $url    = $this->normalizeVideoUrl($step['video_url'] ?? null);

                $videoUrl  = null;
                $videoPath = null;

                if ($remove) {
                    // leave both null
                } elseif ($file) {
                    $stored    = $file->store('procedures/step-videos', 'public');
                    $videoPath = Storage::url($stored);
                } else {
                    $videoUrl = $url ?: null;
                }

                $clean[] = [
                    'step_no'          => isset($step['step_no']) ? (int) $step['step_no'] : ($i + 1),
                    'title'            => trim((string) ($step['title'] ?? '')) ?: null,
                    'body'             => $body,
                    'rationale'        => trim((string) ($step['rationale'] ?? '')) ?: null,
                    'caution'          => trim((string) ($step['caution'] ?? '')) ?: null,
                    'duration_seconds' => ($step['duration_seconds'] !== null && $step['duration_seconds'] !== '')
                        ? max(0, (int) $step['duration_seconds'])
                        : null,
                    'video_url'        => $videoUrl,
                    'video_path'       => $videoPath,
                ];
            }

            if (!empty($clean)) {
                $procedure->steps()->delete();
                usort($clean, fn ($a, $b) => $a['step_no'] <=> $b['step_no']);
                foreach ($clean as $row) {
                    $procedure->steps()->create($row);
                }
            }
        });

        return redirect()->route('admin.procedures.show', $procedure)
            ->with('ok', 'Procedure updated.');
    }

    /**
     * ARCHIVE (soft): mark procedure (and steps) archived.
     */
    public function archive(Request $req, Procedure $procedure)
    {
        try {
            if ((int) $procedure->is_archived === 1) {
                return $req->ajax()
                    ? response()->json(['ok' => true, 'already' => true])
                    : back()->with('ok', 'Procedure is already archived.');
            }

            DB::transaction(function () use ($procedure) {
                $now     = Carbon::now();
                $adminId = auth('admin')->id();

                $procedure->forceFill([
                    'is_archived'       => 1,
                    'archived_at'       => $now,
                    'archived_by_admin' => $adminId,
                ])->save();

                // If your steps table has these fields, update them too:
                $procedure->steps()->update([
                    'is_archived'       => 1,
                    'archived_at'       => $now,
                    'archived_by_admin' => $adminId,
                    'updated_at'        => $now,
                ]);
            });

            if ($req->ajax()) {
                return response()->json(['ok' => true, 'archived' => true, 'procedure' => $procedure->slug]);
            }

            return redirect()->route('admin.procedures.index', ['archived' => 0])
                ->with('ok', 'Procedure archived.');
        } catch (\Throwable $e) {
            return $req->ajax()
                ? new JsonResponse(['ok' => false, 'error' => 'Unable to archive procedure.'], 422)
                : back()->withErrors('Unable to archive procedure.');
        }
    }

    /**
     * RESTORE from archive: unmark procedure (and steps).
     */
    public function restore(Request $req, Procedure $procedure)
    {
        try {
            if (! $procedure->is_archived) {
                return $req->ajax()
                    ? response()->json(['ok' => true, 'already' => true])
                    : back()->with('ok', 'Procedure is not archived.');
            }

            DB::transaction(function () use ($procedure) {
                $now = Carbon::now();

                $procedure->forceFill([
                    'is_archived'       => 0,
                    'archived_at'       => null,
                    'archived_by_admin' => null,
                ])->save();

                // If your steps table has these fields, clear them too:
                $procedure->steps()->update([
                    'is_archived'       => 0,
                    'archived_at'       => null,
                    'archived_by_admin' => null,
                    'updated_at'        => $now,
                ]);
            });

            if ($req->ajax()) {
                return response()->json(['ok' => true, 'restored' => true]);
            }

            return redirect()->route('admin.procedures.archived')
                ->with('ok', 'Procedure restored.');
        } catch (\Throwable $e) {
            return $req->ajax()
                ? new JsonResponse(['ok' => false, 'error' => 'Unable to restore procedure.'], 422)
                : back()->withErrors('Unable to restore procedure.');
        }
    }

    /**
     * HARD DELETE — allowed only if already archived.
     */
    public function destroy(Request $req, Procedure $procedure)
    {
        try {
            if (! $procedure->is_archived) {
                return $req->ajax()
                    ? new JsonResponse(['ok' => false, 'error' => 'Archive first before deleting.'], 422)
                    : back()->withErrors('Archive first before deleting.');
            }

            DB::transaction(function () use ($procedure) {
                // Optionally hard-delete children:
                // $procedure->steps()->delete();
                $procedure->delete();
            });

            return $req->ajax()
                ? response()->json(['ok' => true])
                : redirect()->route('admin.procedures.archived')->with('ok', 'Procedure deleted.');
        } catch (\Throwable $e) {
            return $req->ajax()
                ? new JsonResponse(['ok' => false, 'error' => 'Unable to delete procedure.'], 422)
                : back()->withErrors('Unable to delete procedure.');
        }
    }

    /** Publish / Unpublish */
    public function publish(Procedure $procedure)
    {
        $procedure->publish();
        $procedure->updated_by_admin = auth('admin')->id();
        $procedure->save();

        return back()->with('ok', 'Procedure published.');
    }

    public function unpublish(Procedure $procedure)
    {
        $procedure->unpublish();
        $procedure->updated_by_admin = auth('admin')->id();
        $procedure->save();

        return back()->with('ok', 'Procedure moved back to draft.');
    }

    /* ===========================
     * Helpers
     * =========================== */
    private function csvToArray(?string $csv): array
    {
        if (!$csv) return [];
        return array_values(array_filter(
            array_map('trim', explode(',', $csv)),
            fn ($s) => $s !== ''
        ));
    }

    private function normalizeVideoUrl(?string $url): ?string
    {
        if (!$url) return null;
        $u = trim($url);

        // YouTube
        if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([A-Za-z0-9_\-]{6,})~', $u, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        // Vimeo
        if (preg_match('~vimeo\.com/(\d+)~', $u, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return $u; // fallback/custom host
    }
}