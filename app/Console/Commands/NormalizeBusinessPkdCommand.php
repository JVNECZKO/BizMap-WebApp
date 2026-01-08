<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\BusinessPkdCode;
use App\Models\Setting;
use Illuminate\Console\Command;

class NormalizeBusinessPkdCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkd:normalize-business';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalizuje kody PKD w tabelach businesses i business_pkd_codes (dodaje kropki/duże litery)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Normalizacja kodów PKD (firmy + pivot) ...');
        $pkdVersion = Setting::get('pkd.version', '2007');

        $chunkSize = 80; // małe porcje dla SQLite (limit parametrów)

        Business::query()
            ->orderBy('id')
            ->chunkById($chunkSize, function ($chunk) use ($pkdVersion, $chunkSize) {
                $now = now();
                $pivotPayload = [];

                foreach ($chunk as $biz) {
                    $main = $this->normalize($biz->glowny_kod_pkd);
                    $extras = $this->normalizeList($biz->pozostale_kody_pkd);
                    $joinedExtras = empty($extras) ? null : implode('$##$', $extras);

                    $biz->glowny_kod_pkd = $main;
                    $biz->pozostale_kody_pkd = $joinedExtras;
                    $biz->updated_at = $now;
                    $biz->save();

                    $codes = array_filter(array_unique(array_merge($main ? [$main] : [], $extras)));
                    foreach ($codes as $code) {
                        $pivotPayload[] = [
                            'business_id' => $biz->id,
                            'pkd_code' => $code,
                            'pkd_version' => $pkdVersion,
                            'created_at' => $now,
                        ];
                    }
                }

                if (! empty($pivotPayload)) {
                    $ids = $chunk->pluck('id')->all();
                    foreach (array_chunk($ids, $chunkSize) as $idChunk) {
                        BusinessPkdCode::whereIn('business_id', $idChunk)->delete();
                    }
                    BusinessPkdCode::insert($pivotPayload);
                }
            });

        $this->info('Zakończono normalizację PKD.');

        return Command::SUCCESS;
    }

    protected function normalize(?string $code): ?string
    {
        if ($code === null) {
            return null;
        }
        $clean = strtoupper(trim($code));
        $clean = preg_replace('/[^0-9A-Z\\.]/', '', $clean ?? '');
        $clean = str_replace([' ', "\t", "\n", "\r"], '', $clean);

        if ($clean === '') {
            return null;
        }
        if (! preg_match('/\\d/', $clean)) {
            return null;
        }
        if (str_contains($clean, '.')) {
            return $clean;
        }
        if (preg_match('/^(\\d{4})([A-Z])$/', $clean, $m)) {
            return substr($m[1], 0, 2) . '.' . substr($m[1], 2, 2) . '.' . $m[2];
        }
        if (preg_match('/^(\\d{4})$/', $clean, $m)) {
            return substr($m[1], 0, 2) . '.' . substr($m[1], 2, 2);
        }
        if (preg_match('/^(\\d{3})([A-Z])$/', $clean, $m)) {
            return substr($m[1], 0, 2) . '.' . substr($m[1], 2, 1) . '.' . $m[2];
        }
        if (preg_match('/^(\\d{3})$/', $clean, $m)) {
            return substr($m[1], 0, 2) . '.' . substr($m[1], 2, 1);
        }

        return $clean ?: null;
    }

    protected function normalizeList(?string $value): array
    {
        if (! $value) {
            return [];
        }

        $parts = preg_split('/\\$##\\$|,|;|\\s+/', $value);

        return collect($parts)
            ->map(fn ($item) => $this->normalize($item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
