<?php

namespace App\Http\Controllers\Student;

use App\Models\NursingRoadmap;
use Illuminate\Support\Str;

class RoadmapController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        // Pull everything (you said: “all of it should be displayed here”)
        $items = NursingRoadmap::orderBy('career_level')
            ->orderBy('category')
            ->orderBy('role')
            ->get();

        // Map DB rows → cards for your existing Blade grid
        $cards = $items->map(function ($r) {
            $iconSet = $this->iconPair($r->category, $r->career_level);
            $slug    = $r->slug ?: Str::slug($r->role);

            return [
                'href'     => route('student.roadmaps.show', $slug),
                'icon'     => $iconSet['icon'],
                'iconTone' => $iconSet['tone'],
                'subIcon'  => $iconSet['subIcon'],
                'title'    => $r->role, // Card title
                'desc'     => Str::limit($r->description ?: ($r->category . ' • ' . $r->career_level), 140),
            ];
        });

        // Render your existing page (keep animations in Blade as-is)
return view('student.roadmaps', [   // ← was 'roadmaps.index'
    'cards' => $cards,
]);
    }

    public function show(string $slug)
    {
        // Find by slug (your table already has this)
        $item = NursingRoadmap::where('slug', $slug)->firstOrFail();

        // Decode steps if JSON is present
        $steps = [];
        if (!empty($item->steps_json)) {
            try {
                $decoded = json_decode($item->steps_json, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $steps = $decoded;
                }
            } catch (\Throwable $e) {
                // ignore JSON errors; fallback to steps_text below
            }
        }

        return view('roadmaps.show', [
            'item'  => $item,
            'steps' => $steps, // may be empty; Blade will fallback to steps_text
        ]);
    }

    private function iconPair(?string $category, ?string $level): array
    {
        // Lightweight icon mapping; feel free to tweak
        $cat = Str::of($category ?? '')->lower()->value();

        return match (true) {
            str_contains($cat, 'critical')  => ['icon' => 'activity',      'tone' => 'text-rose-600',    'subIcon' => 'heart-pulse'],
            str_contains($cat, 'community') => ['icon' => 'users',         'tone' => 'text-sky-600',     'subIcon' => 'map'],
            str_contains($cat, 'pediatric') => ['icon' => 'baby',          'tone' => 'text-amber-600',   'subIcon' => 'toy-brick'],
            str_contains($cat, 'ob') || str_contains($cat, 'maternal')
                                           => ['icon' => 'heart',         'tone' => 'text-pink-600',    'subIcon' => 'flower'],
            str_contains($cat, 'oncology')  => ['icon' => 'flask-conical', 'tone' => 'text-purple-600',  'subIcon' => 'flask-round'],
            str_contains($cat, 'surgical')  => ['icon' => 'scalpel',       'tone' => 'text-emerald-600', 'subIcon' => 'bandage'],
            default                         => ['icon' => 'stethoscope',   'tone' => 'text-emerald-600', 'subIcon' => 'file-text'],
        };
    }
}
