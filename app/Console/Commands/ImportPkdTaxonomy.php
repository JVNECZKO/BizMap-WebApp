<?php

namespace App\Console\Commands;

use App\Models\PkdTaxonomy;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportPkdTaxonomy extends Command
{
    protected $signature = 'pkd:import-taxonomy {file=storage/app/pkd/taxonomy.csv}';
    protected $description = 'Importuje mapowanie grup/podgrup PKD z pliku CSV';

    public function handle(): int
    {
        $path = $this->argument('file');
        if (! is_file($path)) {
            $this->error("Plik {$path} nie istnieje.");
            return self::FAILURE;
        }

        $rows = array_map('str_getcsv', file($path));
        if (count($rows) <= 1) {
            $this->error('Brak danych w CSV.');
            return self::FAILURE;
        }

        $header = array_map(fn($h) => trim($h), array_shift($rows));
        $expected = ['aleo_grupa','aleo_podgrupa','pkd_primary','pkd_secondary'];
        if (array_map('strtolower', $header) !== array_map('strtolower', $expected)) {
            $this->error('Nagłówek CSV musi zawierać kolumny: '.implode(',', $expected));
            return self::FAILURE;
        }

        PkdTaxonomy::truncate();

        $count = 0;
        foreach ($rows as $row) {
            if (count($row) < 4) {
                continue;
            }
            [$group, $sub, $primary, $secondary] = $row;
            $group = trim($group);
            $sub = trim($sub);
            $primary = trim($primary) ?: null;
            $secondary = trim($secondary);
            $secondaryCodes = $secondary ? array_values(array_filter(array_map('trim', explode('|', $secondary)))) : [];

            PkdTaxonomy::create([
                'group_name' => $group,
                'subgroup_name' => $sub,
                'group_slug' => Str::slug($group),
                'subgroup_slug' => Str::slug($sub),
                'primary_code' => $primary,
                'secondary_codes' => $secondaryCodes,
            ]);
            $count++;
        }

        $this->info("Zaimportowano {$count} rekordów taksonomii.");
        return self::SUCCESS;
    }
}
