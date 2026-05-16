{{-- resources/views/faculty/chartings/_frame-open.blade.php --}}
{{-- Minimal “open” frame. Do NOT output <!doctype html> here to avoid double markup.
   This exists only to satisfy legacy @include('faculty.chartings._frame-open', ['title' => '...']). --}}

@php($_frame_title = $title ?? 'NurSync (CI)')
{{-- no-op --}}
