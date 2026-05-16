@php
    $approved = $status === 'approved';
    $rejected = $status === 'rejected';
@endphp

<x-mail::message>
{{-- Brand header (centered) --}}
<div style="text-align:center; margin-bottom: 12px;">
  <div style="font-size:22px; font-weight:700; color:#0f172a; letter-spacing:.2px;">
    NurSync
  </div>
</div>

{{-- Big title --}}
<h1 style="text-align:center; font-size:20px; font-weight:700; margin: 0 0 16px; color:#0f172a;">
  {{ $approved ? 'Welcome aboard!' : ($rejected ? 'Application Update' : 'Thanks for signing up!') }}
</h1>

<p style="text-align:center; color:#475569; margin:0 0 20px;">
  {{ $intro }}
</p>

{{-- Primary button --}}
<div style="text-align:center; margin: 24px 0;">
  <x-mail::button :url="$actionUrl">
    {{ $actionText }}
  </x-mail::button>
</div>

{{-- Fallback link --}}
<p style="font-size:12px; color:#64748b; line-height:1.5; margin-top:8px;">
  If the button doesn’t work, copy and paste this link into your browser:<br>
  <a href="{{ $actionUrl }}" style="color:#0f172a; word-break:break-all;">{{ $actionUrl }}</a>
</p>

<hr style="border:none; border-top:1px solid #e2e8f0; margin:24px 0;">

{{-- What's next --}}
<h3 style="margin:0 0 8px; color:#0f172a;">What’s next?</h3>
<ul style="margin:0; padding-left:18px; color:#475569;">
  @if($approved)
    <li>Sign in to view your dashboard and classes.</li>
    <li>Update your profile details and photo.</li>
  @elseif($rejected)
    <li>If you believe this was an error, contact the administrator.</li>
  @else
    <li>We’ll email you once the admin finishes reviewing your application.</li>
  @endif
</ul>

<p style="color:#94a3b8; font-size:12px; margin-top:20px;">
  — {{ config('app.name') }} Team
</p>
</x-mail::message>
