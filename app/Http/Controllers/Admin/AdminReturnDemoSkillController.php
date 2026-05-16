<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Procedure;
use App\Models\ProcedureStep;
use App\Models\ReturnDemoSkill;
use App\Models\ReturnDemoStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class AdminReturnDemoSkillController extends Controller
{
    /* =========================
       INDEX (Active Skills)
    ========================== */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $skills = $this->filterSkills($request)
                ->where('is_archived', 0)
                ->orderByDesc('created_at')
                ->paginate(10)
                ->appends($request->query());

            $rows  = View::exists('admin.return_demo_skills._table_rows')
                ? View::make('admin.return_demo_skills._table_rows', compact('skills'))->render()
                : $this->fallbackRows($skills);

            $pager = View::exists('admin.return_demo_skills._table_pager')
                ? View::make('admin.return_demo_skills._table_pager', compact('skills'))->render()
                : '';

            return response()->json(['rows' => $rows, 'pager' => $pager]);
        }

        $skills = $this->filterSkills($request)
            ->where('is_archived', 0)
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.admin-return-demo-skills', compact('skills'));
    }

    /* =========================
       ARCHIVED LIST
    ========================== */
    public function archived(Request $request)
    {
        $skills = $this->filterSkills($request)
            ->where('is_archived', 1)
            ->orderByDesc('archived_at')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.admin-return-demo-skills-archived', compact('skills'));
    }

    /* =========================
       CREATE / STORE (manual)
    ========================== */
    public function create()
    {
        return view('admin.return_demo_skills.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'level'           => 'nullable|string|max:50',
            'clinical_wards'  => 'nullable|string|max:255',
            'procedure_id'    => 'nullable|integer|exists:procedures,id',
        ]);

        $data['slug']             = $this->uniqueSlug($this->slugBase($data['title']));
        $data['created_by_admin'] = Auth::guard('admin')->id();
        $data['status']           = 'draft';

        ReturnDemoSkill::create($data);

        return redirect()
            ->route('admin.return_demo.skills.index')
            ->with('ok', 'Return demo skill created successfully.');
    }

    /* =========================
       SHOW / EDIT / UPDATE
    ========================== */
    public function show(ReturnDemoSkill $skill)
    {
        $skill->load(['steps', 'attachments', 'procedure']);
        return view('admin.return_demo_skills.show', compact('skill'));
    }

    public function edit(ReturnDemoSkill $skill)
    {
        return view('admin.return_demo_skills.edit', compact('skill'));
    }

    public function update(Request $request, ReturnDemoSkill $skill)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'level'           => 'nullable|string|max:50',
            'clinical_wards'  => 'nullable|string|max:255',
            'procedure_id'    => 'nullable|integer|exists:procedures,id',
            'status'          => 'nullable|in:draft,published',
        ]);

        $data['updated_by_admin'] = Auth::guard('admin')->id();
        $skill->update($data);

        return redirect()
            ->route('admin.return_demo.skills.index')
            ->with('ok', 'Return demo skill updated.');
    }

    /* =========================
       PUBLISH / UNPUBLISH
    ========================== */
    public function publish(ReturnDemoSkill $skill)
    {
        $skill->publish();
        return back()->with('ok', 'Skill published.');
    }

    public function unpublish(ReturnDemoSkill $skill)
    {
        $skill->unpublish();
        return back()->with('ok', 'Skill reverted to draft.');
    }

    /* =========================
       ARCHIVE / RESTORE
    ========================== */
    public function archive(ReturnDemoSkill $skill, Request $request)
    {
        $skill->archive(Auth::guard('admin')->id());

        if ($request->ajax()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('ok', 'Skill archived successfully.');
    }

    public function restore(ReturnDemoSkill $skill, Request $request)
    {
        $skill->restore();

        if ($request->ajax()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('ok', 'Skill restored.');
    }

    /* =========================
       DESTROY (Hard Delete)
    ========================== */
    public function destroy(ReturnDemoSkill $skill)
    {
        if (!$skill->is_archived) {
            return back()->withErrors(['Skill must be archived before deletion.']);
        }

        $skill->delete();
        return back()->with('ok', 'Skill permanently deleted.');
    }

    /* =========================
       IMPORT FROM PROCEDURES
    ========================== */
    public function importFromProcedures(Request $request)
    {
        $data = $request->validate([
            'procedure_ids'   => 'required|array',
            'procedure_ids.*' => 'integer|exists:procedures,id',
        ]);

        $adminId = Auth::guard('admin')->id();
        $created = 0;
        $skipped = 0;

        DB::transaction(function () use ($data, $adminId, &$created, &$skipped) {
            $procedures = Procedure::whereIn('id', $data['procedure_ids'])->get();

            foreach ($procedures as $p) {
                if (ReturnDemoSkill::where('procedure_id', $p->id)->exists()) {
                    $skipped++;
                    continue;
                }

                $slug = $this->uniqueSlug($this->slugBase($p->slug ?: $p->title ?: 'skill'));

                $skill = ReturnDemoSkill::create([
                    'slug'             => $slug,
                    'title'            => $p->title,
                    'description'      => $p->description,
                    'level'            => $p->level,
                    'status'           => 'draft',
                    'clinical_wards'   => $p->clinical_wards,
                    'hazards_text'     => $p->hazards_text,
                    'ppe_json'         => $p->ppe_json ?? null,
                    'tags_json'        => $p->tags_json ?? null,
                    'video_url'        => $p->video_url,
                    'video_path'       => $p->video_path,
                    'pdf_path'         => $p->pdf_path,
                    'procedure_id'     => $p->id,
                    'created_by_admin' => $adminId,
                ]);

                $steps = ProcedureStep::where('procedure_id', $p->id)
                    ->where('is_archived', 0)
                    ->orderBy('step_no')
                    ->get();

                foreach ($steps as $s) {
                    ReturnDemoStep::create([
                        'return_demo_id'    => $skill->id,
                        'step_no'           => $s->step_no,
                        'title'             => $s->title,
                        'body'              => $s->body,
                        'rationale'         => $s->rationale,
                        'caution'           => $s->caution,
                        'duration_seconds'  => $s->duration_seconds,
                        'video_url'         => $s->video_url,
                        'video_path'        => $s->video_path,
                    ]);
                }

                $created++;
            }
        });

        return response()->json([
            'ok'      => true,
            'created' => $created,
            'skipped' => $skipped,
        ]);
    }

    /* =========================
       PROCEDURES JSON for Picker (matches modal expectation)
    ========================== */
    public function procedures(Request $request)
    {
        $q      = trim((string) $request->input('q'));
        $status = trim((string) $request->input('status')); // 'draft' | 'published' | ''
        $ward   = trim((string) $request->input('ward'));   // substring of clinical_wards

        $query = Procedure::query()
            ->where('is_archived', 0)
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($s) use ($q) {
                    $s->where('title', 'like', "%{$q}%")
                      ->orWhere('slug', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->when($status !== '', fn($qq) => $qq->where('status', $status))
            ->when($ward !== '',   fn($qq) => $qq->where('clinical_wards', 'like', "%{$ward}%"))
            ->orderBy('title');

        // (Optional) add pagination later; for now keep payload light:
        $rows = $query->limit(150)->get(['id','title','clinical_wards','status','created_at']);

        return response()->json([
            'data'  => $rows->map(fn($p) => [
                'id'             => (int) $p->id,
                'title'          => (string) ($p->title ?? '—'),
                'clinical_wards' => (string) ($p->clinical_wards ?? '—'),
                'status'         => (string) ($p->status ?? 'draft'),
                'created_at'     => optional($p->created_at)->format('M d, Y') ?? '—',
            ]),
            'pager' => '', // modal doesn’t require yet
        ]);
    }

    /* Back-compat alias if you referenced this name elsewhere */
public function proceduresForPicker(Request $request)
{
    $q      = trim((string) $request->input('q'));
    $status = trim((string) $request->input('status'));   // 'draft' | 'published' | ''
    $ward   = trim((string) $request->input('ward'));     // text contained in clinical_wards

    $rows = \App\Models\Procedure::query()
        // treat NULL as active too
        ->where(function ($w) {
            $w->whereNull('is_archived')->orWhere('is_archived', 0);
        })
        ->when($q !== '', function ($qq) use ($q) {
            $qq->where(function ($s) use ($q) {
                $s->where('title', 'like', "%{$q}%")
                  ->orWhere('slug', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%");
            });
        })
        ->when($status !== '', fn($qq) => $qq->where('status', $status))
        ->when($ward !== '', fn($qq) => $qq->where('clinical_wards', 'like', "%{$ward}%"))
        ->orderBy('title')
        ->limit(150)
        ->get(['id','title','clinical_wards','status','created_at']);

    return response()->json([
        'items' => $rows->map(function ($p) {
            return [
                'id'             => (int) $p->id,
                'title'          => (string) ($p->title ?? '—'),
                'clinical_wards' => (string) ($p->clinical_wards ?? '—'),
                'status'         => (string) ($p->status ?? 'draft'),
                'created'        => optional($p->created_at)->format('M d, Y') ?? '—',
            ];
        }),
    ]);
}
    /* =========================
       PRIVATE HELPERS
    ========================== */
    private function filterSkills(Request $request)
    {
        return ReturnDemoSkill::query()
            ->when($request->filled('q'), fn($q) => $q->search($request->input('q')))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('ward'), fn($q) => $q->ward($request->input('ward')));
    }

    private function fallbackRows($skills): string
    {
        if (!$skills->count()) {
            return '<tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No skills found.</td></tr>';
        }

        $html = '';
        foreach ($skills as $s) {
            $statusBadge = $s->status === 'published'
                ? '<span class="inline-flex items-center gap-1.5 rounded-lg bg-green-50 text-green-700 px-2 py-1 text-[12px] font-medium"><i data-lucide="check-circle" class="h-3.5 w-3.5"></i> Published</span>'
                : '<span class="inline-flex items-center gap-1.5 rounded-lg bg-yellow-50 text-yellow-700 px-2 py-1 text-[12px] font-medium"><i data-lucide="clock" class="h-3.5 w-3.5"></i> Draft</span>';

            $author = $s->created_by_admin
                ? (optional($s->adminCreator)->full_name ?? '—')
                : ($s->created_by ? (optional($s->author)->full_name ?? '—') : '—');

            $html .= <<<HTML
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3 font-medium text-slate-900">{$this->e($s->title ?: '—')}</td>
              <td class="px-4 py-3 text-slate-700">{$this->e($s->clinical_wards ?: '—')}</td>
              <td class="px-4 py-3">{$statusBadge}</td>
              <td class="px-4 py-3 text-slate-700">{$this->e(optional($s->created_at)->format('M d, Y') ?: '—')}</td>
              <td class="px-4 py-3 text-slate-700">{$this->e($author)}</td>
              <td class="px-4 py-3">
                <div class="flex items-center justify-end gap-1.5">
                  <a href="{$this->urlTo('admin.return_demo.skills.show', $s)}"
                     class="inline-flex items-center justify-center rounded-lg bg-blue-600 text-white p-2 hover:bg-blue-700"
                     title="View"><i data-lucide="eye" class="h-4 w-4"></i></a>

                  <a href="{$this->urlTo('admin.return_demo.skills.edit', $s)}"
                     class="inline-flex items-center justify-center rounded-lg bg-yellow-400 text-slate-900 p-2 hover:brightness-95"
                     title="Edit"><i data-lucide="pencil" class="h-4 w-4"></i></a>

                  <button type="button"
                          class="inline-flex items-center justify-center rounded-lg bg-orange-500 text-white p-2 hover:bg-orange-600"
                          data-action="archive" data-skill-id="{$s->id}" data-skill-title="{$this->e($s->title)}"
                          data-url="{$this->urlTo('admin.return_demo.skills.archive', $s)}">
                    <i data-lucide="archive" class="h-4 w-4"></i>
                  </button>
                </div>
              </td>
            </tr>
            HTML;
        }
        return $html;
    }

    private function e($value): string
    {
        return e($value ?? '');
    }

    private function urlTo(string $name, ReturnDemoSkill $skill): string
    {
        try {
            return route($name, $skill);
        } catch (\Throwable $e) {
            return '#';
        }
    }

    private function slugBase(?string $text): string
    {
        $text = trim((string) $text);
        return Str::slug($text !== '' ? $text : 'skill');
    }

    private function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = $base;
        $i = 1;

        while (
            ReturnDemoSkill::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . (++$i);
        }
        return $slug;
    }
}
