<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecountPopularPkdCommand extends Command
{
    protected $signature = 'pkd:recount-popular {limit=0}';

    protected $description = 'Przelicza tabelę pkd_popularity na podstawie business_pkd_codes';

    public function handle(): int
    {
        $this->info('Czyszczę tabelę pkd_popularity ...');
        DB::table('pkd_popularity')->truncate();

        $this->info('Agreguję ... (może potrwać)');
        $query = DB::table('business_pkd_codes')
            ->select('pkd_code', DB::raw('COUNT(*) as total'))
            ->groupBy('pkd_code');

        $limit = (int) $this->argument('limit');
        if ($limit > 0) {
            $query->orderByDesc('total')->limit($limit);
        }

        $rows = $query->get();
        $payload = $rows->map(function ($row) {
            return [
                'pkd_code' => $row->pkd_code,
                'total' => $row->total,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->all();

        if (! empty($payload)) {
            DB::table('pkd_popularity')->insert($payload);
        }

        $this->info('Gotowe: ' . count($payload) . ' kodów.');

        return Command::SUCCESS;
    }
}
