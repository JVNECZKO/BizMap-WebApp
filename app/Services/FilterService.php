<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class FilterService
{
    protected const CACHE_KEY = 'bizmap_filter_snapshot';

    public function get(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $snapshot = Setting::get('filters.snapshot');
            if (is_array($snapshot)) {
                return $snapshot;
            }

            return $this->refresh();
        });
    }

    public function refresh(): array
    {
        $data = [
            'wojewodztwa' => Business::query()
                ->whereNotNull('wojewodztwo')
                ->groupBy('wojewodztwo')
                ->orderBy('wojewodztwo')
                ->pluck('wojewodztwo')
                ->toArray(),
            'powiaty' => Business::query()
                ->whereNotNull('powiat')
                ->groupBy('powiat')
                ->orderBy('powiat')
                ->pluck('powiat')
                ->toArray(),
            'gminy' => Business::query()
                ->whereNotNull('gmina')
                ->groupBy('gmina')
                ->orderBy('gmina')
                ->pluck('gmina')
                ->toArray(),
            'miejscowosci' => Business::query()
                ->whereNotNull('miejscowosc')
                ->groupBy('miejscowosc')
                ->orderBy('miejscowosc')
                ->pluck('miejscowosc')
                ->toArray(),
            'statusy' => Business::query()
                ->whereNotNull('status_dzialalnosci')
                ->groupBy('status_dzialalnosci')
                ->orderBy('status_dzialalnosci')
                ->pluck('status_dzialalnosci')
                ->toArray(),
        ];

        Setting::setValue('filters.snapshot', $data, 'array');
        Cache::forever(self::CACHE_KEY, $data);

        return $data;
    }

    public function clear(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
