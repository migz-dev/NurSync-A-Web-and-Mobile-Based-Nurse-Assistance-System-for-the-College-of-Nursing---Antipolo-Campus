<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $run->procedure->title }} · Practice Run · NurSync</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
      <link rel="icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('CON_LOGO.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50">
    <main class="min-h-screen flex">
        @php($active = 'procedures')
        @include('partials.sidebar')

        <section class="flex-1">
            <div class="container mx-auto px-8 py-10 space-y-6">

                <header class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span
                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                            <i data-lucide="{{ $run->procedure->icon ?? 'book-open' }}" class="h-5 w-5"></i>
                        </span>
                        <h1 class="text-[24px] font-extrabold text-slate-900">
                            {{ $run->procedure->title }} — Practice
                        </h1>
                    </div>
                    <form method="POST" action="{{ route('student.practice.finish', $run) }}">
                        @csrf
                        <button
                            class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:opacity-95">
                            Finish
                        </button>
                    </form>
                </header>
{{-- Build safe JSON for Alpine (raw PHP, no map/arrow fns) --}}
<?php
    $payload = [];
    foreach (($steps ?? []) as $s) {
        $payload[] = [
            'rs_id'   => $s->id,
            'no'      => $s->step->step_no ?? null,
            'title'   => $s->step->title  ?? '',
            'body'    => $s->step->body   ?? '',
            'is_done' => (bool) ($s->is_done ?? false),
            'notes'   => (string) ($s->notes ?? ''),
        ];
    }
    // pre-encode once so Blade doesn't re-touch it
    $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
?>
<div x-data='simRun({{ $run->id }}, {!! $payloadJson !!})' class="grid gap-6 lg:grid-cols-12">




                    {{-- Step list --}}
                    <aside class="lg:col-span-3 space-y-1">
                        <template x-for="s in steps" :key="s.rs_id">
                            <button @click="active = s.rs_id"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl border"
                                :class="active===s.rs_id ? 'border-slate-900 bg-slate-50' : 'border-slate-200'">
                                <span class="truncate text-sm" x-text="`Step ${s.no}: ${s.title}`"></span>
                                <span x-show="s.is_done">✅</span>
                            </button>
                        </template>
                    </aside>

                    {{-- Active step --}}
                    <section class="lg:col-span-6 space-y-4">
                        <template x-if="current()">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900"
                                    x-text="`Step ${current().no}: ${current().title}`"></h2>

                                <div class="rounded-xl bg-white border border-slate-200 p-4 text-sm text-slate-700"
                                    x-html="nl2br(escapeHtml(current().body))"></div>

                                <div class="flex items-center gap-3">
                                    <button @click="toggle()"
                                        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:opacity-95"
                                        x-text="current().is_done ? 'Undo' : 'Mark Done'">
                                    </button>
                                    <button @click="prev()"
                                        class="rounded-lg border px-3 py-2 text-sm hover:bg-slate-50">Prev</button>
                                    <button @click="next()"
                                        class="rounded-lg border px-3 py-2 text-sm hover:bg-slate-50">Next</button>
                                </div>

                                <div class="mt-3">
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Notes
                                        (optional)</label>
                                    <textarea x-model="notes" @change="saveNotes()"
                                        class="w-full rounded-xl border border-slate-200 p-3 text-sm"
                                        rows="3"></textarea>
                                </div>
                            </div>
                        </template>
                    </section>

                    {{-- Right: reflection + progress --}}
                    <aside class="lg:col-span-3 space-y-4">
                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                            <div class="text-[13px] font-semibold text-slate-800 mb-2">Reflection</div>
                            <textarea x-model="reflection" @change="saveReflection()" rows="5"
                                class="w-full rounded-lg border p-2 text-sm"></textarea>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                            <div class="text-[13px] font-semibold text-slate-800">Progress</div>
                            <div class="mt-2 h-2 bg-slate-200 rounded">
                                <div class="h-2 bg-slate-900 rounded" :style="`width:${progressPct()}%`"></div>
                            </div>
                            <div class="mt-1 text-xs text-slate-600"
                                x-text="`${doneCount()}/${steps.length} steps completed`"></div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </main>

    @include('partials.student-footer')
    <script src="https://unpkg.com/lucide@latest"></script>
    <script> lucide.createIcons(); </script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function simRun(runId, steps) {
            return {
                runId,
                steps,
                active: steps.length ? steps[0].rs_id : null,
                reflection: @json($run->reflection_text ?? ''),
                get idx() { return this.steps.findIndex(s => s.rs_id === this.active); },
                current() { return this.steps[this.idx] || null; },
                prev() { if (this.idx > 0) this.active = this.steps[this.idx - 1].rs_id; },
                next() { if (this.idx < this.steps.length - 1) this.active = this.steps[this.idx + 1].rs_id; },

                async toggle() {
                    const res = await fetch(`{{ url('/student/practice/run') }}/${this.runId}/step/${this.active}/toggle`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                    });
                    const data = await res.json();
                    if (data.ok) this.steps[this.idx].is_done = data.is_done;
                },

                get notes() { return (this.current() || {}).notes || ''; },
                set notes(v) { if (this.current()) this.current().notes = v; },

                async saveNotes() {
                    await fetch(`{{ url('/student/practice/run') }}/${this.runId}/step/${this.active}/toggle`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({ notes: this.notes })
                    });
                },

                async saveReflection() {
                    await fetch(`{{ url('/student/practice/run') }}/${this.runId}/reflect`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({ reflection_text: this.reflection })
                    });
                },

                doneCount() { return this.steps.filter(s => s.is_done).length; },
                progressPct() { return Math.round(100 * this.doneCount() / (this.steps.length || 1)); }
            }
        }
        function escapeHtml(str) { return (str || '').replace(/[&<>\"']/g, s => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[s])); }
        function nl2br(str) { return (str || '').replace(/\n/g, '<br>'); }
    </script>
</body>

</html>