<?php

namespace App\Http\Controllers\Faculty\Instructor;

use App\Http\Controllers\Controller;
use App\Models\BoardExamQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BoardExamQuestionController extends Controller
{
    /**
     * Display a listing of the questions for the logged-in CI.
     */
    public function index(Request $request)
    {
        $facultyId = auth('faculty')->id();

        $filters = [
            'q'          => $request->input('q'),
            'category'   => $request->input('category'),
            'difficulty' => $request->input('difficulty'),
            'status'     => $request->input('status'),
        ];

        $query = BoardExamQuestion::query()
            ->where('faculty_id', $facultyId);

        // Search text (question + rationale + choices + exam title)
        if ($filters['q']) {
            $q = '%' . trim($filters['q']) . '%';
            $query->where(function ($sub) use ($q) {
                $sub->where('question_text', 'like', $q)
                    ->orWhere('rationale', 'like', $q)
                    ->orWhere('choice_a', 'like', $q)
                    ->orWhere('choice_b', 'like', $q)
                    ->orWhere('choice_c', 'like', $q)
                    ->orWhere('choice_d', 'like', $q)
                    ->orWhere('exam_title', 'like', $q);
            });
        }

        // These filters are mainly for future server-side filtering.
        // On the UI we do client-side filtering, so they may be "all" / null.
        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['difficulty']) && $filters['difficulty'] !== 'all') {
            $query->where('difficulty', $filters['difficulty']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        $questions = $query
            ->orderByDesc('created_at')
            ->paginate(200) // load a generous set for client-side paging
            ->withQueryString();

        return view('faculty.instructor.board_exam.index', [
            'questions' => $questions,
            'filters'   => $filters,
        ]);
    }

    /**
     * Show the form for creating a new exam + questions.
     */
    public function create()
    {
        return view('faculty.instructor.board_exam.create');
    }

    /**
     * Store a newly created exam + multiple questions in storage.
     */
    public function store(Request $request)
    {
        $facultyId = auth('faculty')->id();

        // Validate exam meta + questions array
        $data = $request->validate([
            'exam_title'        => ['required', 'string', 'max:255'],
            'status'            => ['nullable', 'in:draft,published,archived'],
            'questions'         => ['required', 'array', 'min:1'],

            'questions.*.question_text'  => ['required', 'string'],
            'questions.*.choice_a'       => ['required', 'string'],
            'questions.*.choice_b'       => ['required', 'string'],
            'questions.*.choice_c'       => ['required', 'string'],
            'questions.*.choice_d'       => ['required', 'string'],
            'questions.*.correct_answer' => ['required', 'in:A,B,C,D'],
            'questions.*.category'       => ['nullable', 'string', 'max:191'],
            'questions.*.difficulty'     => ['required', 'in:easy,moderate,difficult'],
            'questions.*.rationale'      => ['nullable', 'string'],
        ]);

        $defaultStatus = $data['status'] ?: 'draft';
        $examTitle     = $data['exam_title'];

        foreach ($data['questions'] as $q) {
            BoardExamQuestion::create([
                'faculty_id'      => $facultyId,
                'exam_title'      => $examTitle,
                'category'        => $q['category'] ?? null,
                'difficulty'      => $q['difficulty'],
                'question_text'   => $q['question_text'],
                'choice_a'        => $q['choice_a'],
                'choice_b'        => $q['choice_b'],
                'choice_c'        => $q['choice_c'],
                'choice_d'        => $q['choice_d'],
                'correct_answer'  => $q['correct_answer'],
                'rationale'       => $q['rationale'] ?? null,
                'status'          => $defaultStatus,
            ]);
        }

        return redirect()
            ->route('faculty.instructor.board_exam.index')
            ->with('success', 'Board exam set and questions created successfully.');
    }

    /**
     * Display an exam analytics page for a question's exam set.
     * (Exam title + all questions in that set + analytics placeholders.)
     */
    public function show(BoardExamQuestion $question)
    {
        $this->ensureOwnQuestion($question);

        $facultyId = auth('faculty')->id();

        // All questions under the same exam title for this CI
        $examQuestions = BoardExamQuestion::query()
            ->where('faculty_id', $facultyId)
            ->where('exam_title', $question->exam_title)
            ->orderBy('id')
            ->get();

        // --- Analytics placeholders (no attempts yet, but view won't break) ---

        // When you add an attempts table later, replace these with real values
        $totalAttempts   = 0;
        $avgScore        = 0;
        $medianScore     = 0;
        $highestScore    = 0;
        $lowestScore     = 0;
        $avgTimeMinutes  = 0;

        $summary = [
            'total_attempts'     => $totalAttempts,
            'avg_score'          => $avgScore,
            'median_score'       => $medianScore,
            'highest_score'      => $highestScore,
            'lowest_score'       => $lowestScore,
            'avg_time_minutes'   => $avgTimeMinutes,
            'avg_items_answered' => $examQuestions->count(),
        ];

        // Score distribution buckets – plug real data here once you track attempts
        // Example structure: [['label' => '0–10', 'value' => 3], ...]
        $scoreBuckets = [];

        // Performance by ward/area – will depend on how you log where the student is rotating
        // Example structure: [['label' => 'MS', 'value' => 75], ...] (avg score %)
        $wardPerformance = [];

        // Difficulty breakdown based on questions in the set
        $difficultyBreakdown = [
            'easy'      => $examQuestions->where('difficulty', 'easy')->count(),
            'moderate'  => $examQuestions->where('difficulty', 'moderate')->count(),
            'difficult' => $examQuestions->where('difficulty', 'difficult')->count(),
        ];

        // Item-level stats (per-question row in the table)
        $itemStats = $examQuestions->values()->map(function (BoardExamQuestion $q, int $idx) {
            return [
                'question_id'  => $q->id,
                'no'           => $idx + 1,
                'short_stem'   => Str::limit(trim($q->question_text ?? ''), 140),
                // These become real once you have per-question attempt data
                'correct_pct'  => null,
                'avg_time_sec' => null,
                'difficulty'   => $q->difficulty,
            ];
        })->toArray();

        return view('faculty.instructor.board_exam.show', [
            'question'            => $question,
            'examQuestions'       => $examQuestions,
            'summary'             => $summary,
            'scoreBuckets'        => $scoreBuckets,
            'wardPerformance'     => $wardPerformance,
            'difficultyBreakdown' => $difficultyBreakdown,
            'itemStats'           => $itemStats,
        ]);
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(BoardExamQuestion $question)
    {
        $this->ensureOwnQuestion($question);

        return view('faculty.instructor.board_exam.edit', [
            'question' => $question,
        ]);
    }

    /**
     * Update the specified question in storage.
     */
    public function update(Request $request, BoardExamQuestion $question)
    {
        $this->ensureOwnQuestion($question);

        $validated = $this->validateQuestion($request);

        if (empty($validated['status'])) {
            // Keep current status if none passed
            unset($validated['status']);
        }

        $question->update($validated);

        return redirect()
            ->route('faculty.instructor.board_exam.index')
            ->with('success', 'Board exam question updated successfully.');
    }

    /**
     * Archive the specified question (status = archived).
     */
    public function archive(BoardExamQuestion $question)
    {
        $this->ensureOwnQuestion($question);

        $question->status = 'archived';
        $question->save();

        return redirect()
            ->route('faculty.instructor.board_exam.index')
            ->with('success', 'Question archived successfully.');
    }

    /**
     * Remove the specified question from storage (hard delete).
     */
    public function destroy(BoardExamQuestion $question)
    {
        $this->ensureOwnQuestion($question);

        $question->delete();

        return redirect()
            ->route('faculty.instructor.board_exam.index')
            ->with('success', 'Question deleted permanently.');
    }

    /**
     * Validation rules for single-question create/update (and optional exam_title edit).
     */
    protected function validateQuestion(Request $request): array
    {
        return $request->validate([
            'exam_title'     => ['nullable', 'string', 'max:255'],
            'category'       => ['nullable', 'string', 'max:191'],
            'difficulty'     => ['required', 'in:easy,moderate,difficult'],
            'question_text'  => ['required', 'string'],
            'choice_a'       => ['required', 'string'],
            'choice_b'       => ['required', 'string'],
            'choice_c'       => ['required', 'string'],
            'choice_d'       => ['required', 'string'],
            'correct_answer' => ['required', 'in:A,B,C,D'],
            'rationale'      => ['nullable', 'string'],
            'status'         => ['nullable', 'in:draft,published,archived'],
        ]);
    }

    /**
     * Safety check – ensure logged-in CI owns this question.
     */
    protected function ensureOwnQuestion(BoardExamQuestion $question): void
    {
        $facultyId = auth('faculty')->id();

        if (!$facultyId || $question->faculty_id !== $facultyId) {
            abort(403, 'You are not allowed to manage this question.');
        }
    }
}
