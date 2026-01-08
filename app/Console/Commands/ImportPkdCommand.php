<?php

namespace App\Console\Commands;

use App\Models\PkdCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportPkdCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkd:import {version=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importuje kody PKD z plików JSON (storage/app/pkd)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $versionArg = $this->argument('version');
        $versions = $versionArg === 'all' ? ['2007', '2025'] : [$versionArg];

        foreach ($versions as $version) {
            $file = $this->fileForVersion($version);
            if (! Storage::exists($file)) {
                $this->error("Brak pliku: {$file}");
                continue;
            }

            $data = json_decode(Storage::get($file), true);
            $rows = $data['data']['pozycje'] ?? null;
            if (! $rows) {
                $this->error("Pusty plik lub brak sekcji danych: {$file}");
                continue;
            }

            $payload = [];
            foreach ($rows as $row) {
                [$code, $parent, $level, $isLeaf] = $this->normalizeSymbol($row['symbol']);
                if (! $code) {
                    continue;
                }
                $payload[] = [
                    'code' => $code,
                    'name' => $row['nazwa'] ?? '',
                    'version' => $version,
                    'parent_code' => $parent,
                    'level' => $level,
                    'is_leaf' => $isLeaf,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (empty($payload)) {
                $this->warn("Brak rekordów do zapisu dla wersji {$version}");
                continue;
            }

            $total = 0;
            foreach (array_chunk($payload, 200) as $chunk) {
                PkdCode::upsert($chunk, ['code', 'version'], ['name', 'parent_code', 'level', 'is_leaf', 'updated_at']);
                $total += count($chunk);
            }

            $this->info("Zaimportowano " . $total . " pozycji PKD {$version}");
        }

        return Command::SUCCESS;
    }

    protected function fileForVersion(string $version): string
    {
        return match ($version) {
            '2007' => 'pkd/KlasyfikacjaPKD2007.json',
            '2025' => 'pkd/KlasyfikacjaPKD2025.json',
            default => "pkd/KlasyfikacjaPKD{$version}.json",
        };
    }

    protected function normalizeSymbol(string $symbol): array
    {
        $symbol = trim($symbol);
        $isSection = str_starts_with($symbol, 'SEKCJA');
        if ($isSection) {
            $parts = explode(' ', $symbol);
            $code = end($parts);
            return [$code, null, 1, false];
        }

        $code = $symbol;
        $segments = explode('.', $code);
        $level = count($segments) + 1;
        $parent = null;
        if (count($segments) > 1) {
            array_pop($segments);
            $parent = implode('.', $segments);
        } else {
            // division level -> parent is section letter
            $parent = $this->sectionFromDivision($code);
        }

        $isLeaf = str_contains($code, 'Z') || ! str_contains($code, '.');

        return [$code, $parent, $level, $isLeaf];
    }

    protected function sectionFromDivision(string $code): ?string
    {
        $sectionMap = [
            'A' => ['01', '02', '03'],
            'B' => ['05', '06', '07', '08', '09'],
            'C' => ['10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33'],
            'D' => ['35'],
            'E' => ['36', '37', '38', '39'],
            'F' => ['41', '42', '43'],
            'G' => ['45', '46', '47'],
            'H' => ['49', '50', '51', '52', '53'],
            'I' => ['55', '56'],
            'J' => ['58', '59', '60', '61', '62', '63'],
            'K' => ['64', '65', '66'],
            'L' => ['68'],
            'M' => ['69', '70', '71', '72', '73', '74', '75'],
            'N' => ['77', '78', '79', '80', '81', '82'],
            'O' => ['84'],
            'P' => ['85'],
            'Q' => ['86', '87', '88'],
            'R' => ['90', '91', '92', '93'],
            'S' => ['94', '95', '96'],
            'T' => ['97', '98'],
            'U' => ['99'],
        ];

        foreach ($sectionMap as $section => $divisions) {
            if (in_array(substr($code, 0, 2), $divisions, true)) {
                return $section;
            }
        }

        return null;
    }
}
