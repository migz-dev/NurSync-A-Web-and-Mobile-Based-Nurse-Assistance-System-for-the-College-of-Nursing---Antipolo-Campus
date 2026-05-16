<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureFacultyApproved
{
    public function handle(Request $request, Closure $next)
    {
        $faculty = Auth::guard('faculty')->user();
        if (!$faculty) {
            return redirect()->route('faculty.login');
        }

        // adjust to your schema (is_approved / status)
        $isApproved = (bool) ($faculty->is_approved ?? ($faculty->status === 'approved'));

        if (!$isApproved) {
            return redirect()->route('faculty.pending')
                ->with('error', 'Your account is pending approval.');
        }

        return $next($request);
    }
}
