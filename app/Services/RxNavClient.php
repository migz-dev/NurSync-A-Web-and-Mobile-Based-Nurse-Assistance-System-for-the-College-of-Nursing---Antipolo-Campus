<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RxNavClient
{
    protected string $base = 'https://rxnav.nlm.nih.gov/REST';

    protected function getJson(string $url): array
    {
        $res = Http::timeout(30)->retry(2, 250)->get($url);
        $res->throw();
        return $res->json() ?? [];
    }

    public function rxcuiFromName(string $name): ?string
    {
        $u = $this->base.'/rxcui.json?name='.urlencode($name);
        $j = $this->getJson($u);
        return $j['idGroup']['rxnormId'][0] ?? null;
    }

    public function brandNames(string $rxcui): array
    {
        $u = $this->base.'/rxcui/'.urlencode($rxcui).'/related.json?tty=BN';
        $j = $this->getJson($u);
        $items = $j['relatedGroup']['conceptGroup'][0]['conceptProperties'] ?? [];
        return collect($items)->pluck('name')->unique()->values()->all();
    }

    public function interactions(string $rxcui): array
    {
        $u = $this->base.'/interaction/interaction.json?rxcui='.urlencode($rxcui);
        $j = $this->getJson($u);

        $pairs = [];
        foreach (($j['interactionTypeGroup'][0]['interactionType'][0]['interactionPair'] ?? []) as $p) {
            $desc = $p['description'] ?? '';
            $severity = strtolower($p['severity'] ?? 'moderate');
            $with = $p['interactionConcept'][1]['minConceptItem']['name'] ?? null;
            if ($with) {
                $pairs[] = [
                    'with'       => $with,
                    'severity'   => in_array($severity, ['minor','moderate','major']) ? $severity : 'moderate',
                    'mechanism'  => null,
                    'management' => $desc ?: null,
                ];
            }
        }
        return collect($pairs)->unique('with')->values()->all();
    }

    /** NEW: get all concepts for a TTY (default IN = ingredients) */
    public function allConcepts(string $tty = 'IN'): array
    {
        return Cache::remember("rxnav:allconcepts:$tty", now()->addDay(), function () use ($tty) {
            $url = $this->base.'/allconcepts.json?tty='.urlencode($tty);
            $j = $this->getJson($url);
            $items = $j['minConceptGroup']['minConcept'] ?? [];
            return collect($items)->map(fn($i) => [
                'rxcui' => (string)($i['rxcui'] ?? ''),
                'name'  => (string)($i['name']  ?? ''),
                'tty'   => (string)($i['tty']   ?? ''),
            ])->filter(fn($x)=>$x['rxcui'] && $x['name'])->values()->all();
        });
    }
}
