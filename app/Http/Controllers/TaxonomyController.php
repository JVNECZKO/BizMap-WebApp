<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\PkdTaxonomy;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TaxonomyController extends Controller
{
    public function index()
    {
        $taxonomies = PkdTaxonomy::query()
            ->orderBy('group_name')
            ->orderBy('subgroup_name')
            ->get();

        $counts = Cache::remember('taxonomy_counts', now()->addMinutes(30), function () use ($taxonomies) {
            $result = [];
            foreach ($taxonomies as $item) {
                $codes = array_values(array_filter(array_merge(
                    $item->primary_code ? [$item->primary_code] : [],
                    $item->secondary_codes ?? []
                )));

                if (empty($codes)) {
                    $result[$item->id] = 0;
                    continue;
                }

                $result[$item->id] = Business::query()
                    ->whereIn('glowny_kod_pkd', $codes)
                    ->count();
            }
            return $result;
        });

        $grouped = $taxonomies->groupBy('group_name')->map(function (Collection $items) use ($counts) {
            return $items->map(function ($item) use ($counts) {
                $item->computed_count = $counts[$item->id] ?? 0;
                return $item;
            });
        });

        return view('taxonomy.index', [
            'grouped' => $grouped,
        ]);
    }
}
