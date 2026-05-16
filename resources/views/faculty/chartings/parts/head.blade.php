{{-- resources/views/faculty/chartings/parts/head.blade.php --}}
{{-- Optional helper: lets pages pass ['title' => '...'] if they use a shared layout.
   Your current pages already define their own <head>, so this partial is a no-op.
   Keeping it lightweight avoids double <head> tags while satisfying @include() calls. --}}

@php($__title = $title ?? 'NurSync (CI)')
{{-- If you later adopt a layout, you can @push('title') here. For now: intentionally empty. --}}
