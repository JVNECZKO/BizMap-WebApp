<?php

namespace App\Services;

use App\Models\Business;
use Illuminate\Support\Facades\Cache;

class LocationService
{
    protected const CACHE_KEY = 'bizmap_location_tree';

    public function tree(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            @set_time_limit(0);
            return $this->buildTree();
        });
    }

    public function getLists(?string $woj = null, ?string $powiat = null, ?string $gmina = null): array
    {
        $tree = $this->tree();

        $powiaty = $woj && isset($tree[$woj]) ? array_keys($tree[$woj]) : [];
        $gminy = ($woj && $powiat && isset($tree[$woj][$powiat])) ? array_keys($tree[$woj][$powiat]) : [];
        $miejscowosci = ($woj && $powiat && $gmina && isset($tree[$woj][$powiat][$gmina])) ? $tree[$woj][$powiat][$gmina] : [];

        sort($powiaty);
        sort($gminy);
        sort($miejscowosci);

        return [
            'powiaty' => $powiaty,
            'gminy' => $gminy,
            'miejscowosci' => $miejscowosci,
        ];
    }

    public function clear(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function rebuild(): void
    {
        $this->clear();
        $this->tree();
    }

    protected function buildTree(): array
    {
        $tree = [];
        $powiaty = Business::query()
            ->select('wojewodztwo', 'powiat')
            ->whereNotNull('wojewodztwo')
            ->whereNotNull('powiat')
            ->distinct()
            ->get();

        $gminy = Business::query()
            ->select('wojewodztwo', 'powiat', 'gmina')
            ->whereNotNull('wojewodztwo')
            ->whereNotNull('powiat')
            ->whereNotNull('gmina')
            ->distinct()
            ->get();

        $miejscowosci = Business::query()
            ->select('wojewodztwo', 'powiat', 'gmina', 'miejscowosc')
            ->whereNotNull('wojewodztwo')
            ->whereNotNull('powiat')
            ->whereNotNull('gmina')
            ->whereNotNull('miejscowosc')
            ->distinct()
            ->get();

        foreach ($powiaty as $row) {
            $woj = trim((string) $row->wojewodztwo);
            $pow = trim((string) $row->powiat);
            if ($woj === '' || $pow === '') continue;
            $tree[$woj] = $tree[$woj] ?? [];
            $tree[$woj][$pow] = $tree[$woj][$pow] ?? [];
        }

        foreach ($gminy as $row) {
            $woj = trim((string) $row->wojewodztwo);
            $pow = trim((string) $row->powiat);
            $gmi = trim((string) $row->gmina);
            if ($woj === '' || $pow === '' || $gmi === '') continue;
            $tree[$woj] = $tree[$woj] ?? [];
            $tree[$woj][$pow] = $tree[$woj][$pow] ?? [];
            $tree[$woj][$pow][$gmi] = $tree[$woj][$pow][$gmi] ?? [];
        }

        foreach ($miejscowosci as $row) {
            $woj = trim((string) $row->wojewodztwo);
            $pow = trim((string) $row->powiat);
            $gmi = trim((string) $row->gmina);
            $mie = trim((string) $row->miejscowosc);
            if ($woj === '' || $pow === '' || $gmi === '' || $mie === '') continue;
            $tree[$woj][$pow][$gmi][$mie] = true;
        }

        // sort and convert leaf sets to arrays
        foreach ($tree as $woj => $powiaty) {
            ksort($powiaty, SORT_STRING);
            foreach ($powiaty as $pow => $gminy) {
                ksort($gminy, SORT_STRING);
                foreach ($gminy as $gmi => $miej) {
                    $miejList = array_keys($miej);
                    sort($miejList, SORT_STRING);
                    $gminy[$gmi] = $miejList;
                }
                $powiaty[$pow] = $gminy;
            }
            $tree[$woj] = $powiaty;
        }

        ksort($tree, SORT_STRING);

        return $tree;
    }
}
