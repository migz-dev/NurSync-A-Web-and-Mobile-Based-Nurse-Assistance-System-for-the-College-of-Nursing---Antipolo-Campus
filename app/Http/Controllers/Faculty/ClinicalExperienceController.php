<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\ClinicalExperience;
use App\Models\ClinicalExperienceAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClinicalExperienceController extends Controller
{
    /**
     * List all clinical experiences of the logged-in CI.
     */
    public function index(Request $request)
    {
        $facultyId = Auth::guard('faculty')->id();

        $search = trim($request->input('q', ''));
        $status = $request->input('status');
        $ward   = $request->input('ward');

        $query = ClinicalExperience::query()
            ->where('faculty_id', $facultyId)
            ->withCount('attachments')
            ->latest('created_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('story', 'like', "%{$search}%");
            });
        }

        if (!empty($status) && in_array($status, ['draft', 'published', 'archived'], true)) {
            $query->where('status', $status);
        }

        if (!empty($ward)) {
            $query->where('ward', $ward);
        }

        $experiences = $query->paginate(12)->withQueryString();

        return view('faculty.clinical_experiences.index', [
            'experiences' => $experiences,
            'search'      => $search,
            'status'      => $status,
            'ward'        => $ward,
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        // Policy: any logged-in faculty can create
        $this->authorize('create', ClinicalExperience::class);

        return view('faculty.clinical_experiences.create');
    }

    /**
     * Store a new clinical experience + attachments.
     */
    public function store(Request $request)
    {
        $this->authorize('create', ClinicalExperience::class);

        $facultyId = Auth::guard('faculty')->id();

        $data = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'ward'          => ['nullable', 'string', 'max:50'],
            'summary'       => ['nullable', 'string'],
            'story'         => ['required', 'string'],
            'key_takeaways' => ['nullable', 'string'],
            'status'        => ['nullable', 'in:draft,published,archived'],

            // multiple attachments[] input
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv', 'max:51200'],
            // optional captions for each file (same index order)
            'attachment_captions.*' => ['nullable', 'string', 'max:255'],
        ]);

        $status = $data['status'] ?? 'draft';

        DB::beginTransaction();

        try {
            $experience = new ClinicalExperience();
            $experience->faculty_id    = $facultyId;
            $experience->title         = $data['title'];
            $experience->slug          = $this->generateUniqueSlug($data['title']);
            $experience->ward          = $data['ward'] ?? null;
            $experience->summary       = $data['summary'] ?? null;
            $experience->story         = $data['story'];
            $experience->key_takeaways = $data['key_takeaways'] ?? null;
            $experience->status        = $status;
            $experience->save();

            // Handle attachments if uploaded
            if ($request->hasFile('attachments')) {
                $captions = $request->input('attachment_captions', []);

                $this->storeAttachments(
                    $experience,
                    $request->file('attachments'),
                    $captions
                );
            }

            DB::commit();

            return redirect()
                ->route('faculty.instructor.experiences.index')
                ->with('success', 'Clinical experience created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while saving the clinical experience.');
        }
    }

    /**
     * Display a single clinical experience.
     */
    public function show(ClinicalExperience $experience)
    {
        // Policy check: owner only
        $this->authorize('view', $experience);

        $experience->load([
            'attachments' => function ($q) {
                $q->orderBy('is_primary', 'desc')
                  ->orderBy('sort_order')
                  ->orderBy('id');
            },
        ]);

        return view('faculty.clinical_experiences.show', [
            'experience' => $experience,
        ]);
    }

    /**
     * Show edit form.
     */
    public function edit(ClinicalExperience $experience)
    {
        $this->authorize('update', $experience);

        $experience->load([
            'attachments' => function ($q) {
                $q->orderBy('is_primary', 'desc')
                  ->orderBy('sort_order')
                  ->orderBy('id');
            },
        ]);

        return view('faculty.clinical_experiences.edit', [
            'experience' => $experience,
        ]);
    }

    /**
     * Update an existing experience (and optionally add attachments).
     */
    public function update(Request $request, ClinicalExperience $experience)
    {
        $this->authorize('update', $experience);

        $data = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'ward'          => ['nullable', 'string', 'max:50'],
            'summary'       => ['nullable', 'string'],
            'story'         => ['required', 'string'],
            'key_takeaways' => ['nullable', 'string'],
            'status'        => ['nullable', 'in:draft,published,archived'],

            'attachments.*'         => ['file', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv', 'max:51200'],
            'attachment_captions.*' => ['nullable', 'string', 'max:255'],

            // optional: which attachment id should be primary
            'primary_attachment_id' => ['nullable', 'integer'],
        ]);

        DB::beginTransaction();

        try {
            $experience->title         = $data['title'];
            $experience->ward          = $data['ward'] ?? null;
            $experience->summary       = $data['summary'] ?? null;
            $experience->story         = $data['story'];
            $experience->key_takeaways = $data['key_takeaways'] ?? null;
            $experience->status        = $data['status'] ?? $experience->status;

            // Regenerate slug if title changed (optional)
            if ($experience->isDirty('title')) {
                $experience->slug = $this->generateUniqueSlug($experience->title, $experience->id);
            }

            $experience->save();

            // Upload any NEW attachments
            if ($request->hasFile('attachments')) {
                $captions = $request->input('attachment_captions', []);

                $this->storeAttachments(
                    $experience,
                    $request->file('attachments'),
                    $captions
                );
            }

            // Handle primary attachment selection
            if (!empty($data['primary_attachment_id'])) {
                $this->setPrimaryAttachment($experience, (int) $data['primary_attachment_id']);
            }

            DB::commit();

            return redirect()
                ->route('faculty.instructor.experiences.edit', $experience)
                ->with('success', 'Clinical experience updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the clinical experience.');
        }
    }

    /**
     * Soft-archive an experience.
     */
    public function archive(ClinicalExperience $experience)
    {
        // Treat archive as an "update"-type action
        $this->authorize('update', $experience);

        $experience->status = 'archived';
        $experience->save();

        return redirect()
            ->route('faculty.instructor.experiences.index')
            ->with('success', 'Clinical experience archived.');
    }

    /**
     * Permanently delete an experience (and its attachments).
     */
    public function destroy(ClinicalExperience $experience)
    {
        $this->authorize('delete', $experience);

        // delete files from storage too
        $experience->load('attachments');
        foreach ($experience->attachments as $att) {
            if ($att->storage_path && Storage::disk('public')->exists($att->storage_path)) {
                Storage::disk('public')->delete($att->storage_path);
            }
        }

        $experience->delete(); // attachments removed via FK cascade

        return redirect()
            ->route('faculty.instructor.experiences.index')
            ->with('success', 'Clinical experience deleted permanently.');
    }

    /**
     * Delete a single attachment from an experience.
     */
    public function destroyAttachment(ClinicalExperienceAttachment $attachment)
    {
        $experience = $attachment->experience;

        // Update permissions – if you can update the experience, you can remove its files
        $this->authorize('update', $experience);

        if ($attachment->storage_path && Storage::disk('public')->exists($attachment->storage_path)) {
            Storage::disk('public')->delete($attachment->storage_path);
        }

        $attachment->delete();

        return back()->with('success', 'Attachment removed.');
    }

    /* ============================================================
     |  Helpers
     |============================================================ */

    /**
     * Store uploaded attachments for a given experience.
     *
     * @param  \App\Models\ClinicalExperience  $experience
     * @param  \Illuminate\Http\UploadedFile[] $files
     * @param  array                            $captions
     */
    protected function storeAttachments(ClinicalExperience $experience, array $files, array $captions = []): void
    {
        $disk = 'public';
        $basePath = 'clinical_experiences/' . $experience->id;

        $currentMaxSort = (int) $experience->attachments()->max('sort_order');
        $sort = $currentMaxSort;

        foreach ($files as $idx => $file) {
            if (!$file->isValid()) {
                continue;
            }

            $sort++;

            $originalName = $file->getClientOriginalName();
            $mime         = $file->getClientMimeType();
            $size         = $file->getSize();

            $ext      = $file->getClientOriginalExtension();
            $filename = uniqid('att_') . '.' . $ext;

            $storedPath = $file->storeAs($basePath, $filename, $disk);

            $fileType = str_starts_with($mime, 'video/')
                ? 'video'
                : 'image';

            ClinicalExperienceAttachment::create([
                'clinical_experience_id' => $experience->id,
                'file_type'              => $fileType,
                'storage_path'           => $storedPath,
                'original_name'          => $originalName,
                'mime_type'              => $mime,
                'file_size'              => $size,
                'caption'                => $captions[$idx] ?? null,
                'is_primary'             => 0,
                'sort_order'             => $sort,
            ]);
        }
    }

    /**
     * Mark one attachment as primary for an experience.
     */
    protected function setPrimaryAttachment(ClinicalExperience $experience, int $attachmentId): void
    {
        // clear existing primary
        ClinicalExperienceAttachment::where('clinical_experience_id', $experience->id)
            ->update(['is_primary' => 0]);

        ClinicalExperienceAttachment::where('clinical_experience_id', $experience->id)
            ->where('id', $attachmentId)
            ->update(['is_primary' => 1]);
    }

    /**
     * Generate a unique slug for the experience title.
     */
    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);

        if ($base === '') {
            $base = 'experience';
        }

        $slug = $base;
        $i = 1;

        while (
            ClinicalExperience::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
