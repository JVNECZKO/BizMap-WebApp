<?php

namespace App\Console\Commands;

use App\Services\FilterService;
use Illuminate\Console\Command;

class RefreshFiltersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filters:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Buduje i keszuje listy filtrów (województwa, powiaty, gminy, statusy)';

    /**
     * Execute the console command.
     */
    public function handle(FilterService $filters)
    {
        $this->info('Odświeżam snapshot filtrów...');
        $filters->clear();
        $data = $filters->refresh();
        $this->info('Gotowe. Zapisano: ' . collect($data)->map(fn($v, $k) => $k . ':' . count($v))->implode(', '));

        return Command::SUCCESS;
    }
}
