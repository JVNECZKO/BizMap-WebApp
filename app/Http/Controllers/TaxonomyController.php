<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\PkdTaxonomy;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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

    public function group(string $groupSlug)
    {
        $groupItems = PkdTaxonomy::query()
            ->where('group_slug', $groupSlug)
            ->orderBy('subgroup_name')
            ->get();

        if ($groupItems->isEmpty()) {
            abort(404);
        }

        $title = $groupItems->first()->group_name;
        return $this->renderListing($groupItems, $title, route('taxonomy.group', $groupSlug));
    }

    public function subgroup(string $groupSlug, string $subgroupSlug)
    {
        $groupItems = PkdTaxonomy::query()
            ->where('group_slug', $groupSlug)
            ->orderBy('subgroup_name')
            ->get();

        if ($groupItems->isEmpty()) {
            abort(404);
        }

        $current = $groupItems->firstWhere('subgroup_slug', $subgroupSlug);
        if (! $current) {
            abort(404);
        }

        return $this->renderListing(
            collect([$current]),
            $current->group_name.' – '.$current->subgroup_name,
            route('taxonomy.subgroup', [$groupSlug, $subgroupSlug]),
            $groupItems
        );
    }

    protected function renderListing(Collection $taxonomies, string $title, string $canonical, ?Collection $siblings = null)
    {
        $codes = [];
        foreach ($taxonomies as $item) {
            $codes = array_merge(
                $codes,
                array_filter([$item->primary_code]),
                $item->secondary_codes ?? []
            );
        }
        $codes = array_values(array_unique(array_filter($codes)));

        $perPage = (int) request('per_page', 25);
        $perPage = max(10, min($perPage, 100));

        $businesses = Business::query()
            ->select(['id','full_name','slug','glowny_kod_pkd','miejscowosc','wojewodztwo','powiat','status_dzialalnosci','imported_at'])
            ->when($codes, fn($q) => $q->whereIn('glowny_kod_pkd', $codes))
            ->orderByDesc('imported_at')
            ->orderByDesc('id')
            ->simplePaginate($perPage)
            ->withQueryString();

        return view('taxonomy.list', [
            'title' => $title,
            'canonical' => $canonical,
            'businesses' => $businesses,
            'siblings' => $siblings,
            'groupSlug' => $taxonomies->first()->group_slug,
        ]);
    }
}
