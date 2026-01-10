<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessPkdCode;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Cache;

class BusinessSearchService
{
    public function search(array $filters, ?string $cursor = null, ?int $perPage = null): CursorPaginator
    {
        $perPage = $perPage ?: config('bizmap.pagination.per_page');
        $cacheKey = $this->cacheKey('search', array_merge($filters, ['cursor' => $cursor, 'perPage' => $perPage]));
        $ttl = $this->ttl();

        if ($cursor === null) {
            return Cache::remember($cacheKey, $ttl, function () use ($filters, $perPage) {
                return $this->buildQuery($filters)->cursorPaginate($perPage);
            });
        }

        return $this->buildQuery($filters)->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function company(int $id): ?Business
    {
        return Cache::remember(
            $this->cacheKey('company', ['id' => $id]),
            $this->ttl(),
            fn() => Business::with('pkdCodes')->find($id)
        );
    }

    public function latest(int $limit = 10)
    {
        return Cache::remember(
            $this->cacheKey('latest', ['limit' => $limit]),
            $this->ttl(),
            fn() => Business::query()->recent()->limit($limit)->get()
        );
    }

    public function popularPkd(int $limit = 8)
    {
        $cacheKey = $this->cacheKey('popular_pkd', ['limit' => $limit]);

        return Cache::rememberForever($cacheKey, function () use ($limit) {
            $hasSnapshot = Cache::rememberForever('pkd_popularity_snapshot_exists', function () {
                return \Illuminate\Support\Facades\DB::table('pkd_popularity')->exists();
            });

            if ($hasSnapshot) {
                return \Illuminate\Support\Facades\DB::table('pkd_popularity')
                    ->select('pkd_code', 'total')
                    ->orderByDesc('total')
                    ->limit($limit)
                    ->get();
            }

            // fallback na pełne group by, ale zapisujemy wynik w pkd_popularity aby kolejny raz był szybki
            $rows = BusinessPkdCode::query()
                ->selectRaw('pkd_code, count(*) as total')
                ->groupBy('pkd_code')
                ->orderByDesc('total')
                ->limit($limit)
                ->get();

            $payload = $rows->map(function ($row) {
                return [
                    'pkd_code' => $row->pkd_code,
                    'total' => $row->total,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->all();

            if (! empty($payload)) {
                \Illuminate\Support\Facades\DB::table('pkd_popularity')->upsert($payload, ['pkd_code'], ['total', 'updated_at']);
                Cache::forever('pkd_popularity_snapshot_exists', true);
            }

            return $rows;
        });
    }

    public function clear(): void
    {
        Cache::flush();
        Cache::forget($this->cacheKey('popular_pkd', ['limit' => 8]));
        Cache::forget('pkd_popularity_snapshot_exists');
    }

    protected function buildQuery(array $filters)
    {
        return Business::query()
            ->select([
                'id',
                'full_name',
                'slug',
                'imie',
                'nazwisko',
                'nip',
                'regon',
                'glowny_kod_pkd',
                'kod_pocztowy',
                'powiat',
                'gmina',
                'miejscowosc',
                'status_dzialalnosci',
                'imported_at',
            ])
            ->filter($filters)
            ->when($filters['q'] ?? null, function ($q, $term) {
                // jeśli MySQL z MATCH dostępny, użyjemy fulltext; fallback na like w scopeFilter
                $q->when($this->supportsFulltext(), fn($qq) => $qq->searchFullText($term));
            })
            ->recent();
    }

    protected function cacheKey(string $prefix, array $payload): string
    {
        return 'bizmap_' . $prefix . '_' . md5(json_encode($payload));
    }

    protected function ttl(): int
    {
        return (int) config('bizmap.cache.ttl', 900);
    }

    protected function supportsFulltext(): bool
    {
        // zakładamy MySQL/MariaDB; w SQLite MATCH nie zadziała
        return config('database.default') !== 'sqlite';
    }
}
