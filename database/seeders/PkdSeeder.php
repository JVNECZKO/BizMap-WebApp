<?php

namespace Database\Seeders;

use App\Models\PkdCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class PkdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $datasets = [
            '2007' => base_path('database/data/pkd_2007.json'),
            '2025' => base_path('database/data/pkd_2025.json'),
        ];

        foreach ($datasets as $version => $path) {
            $items = File::exists($path)
                ? json_decode(File::get($path), true) ?? []
                : $this->fallbackSet($version);

            $payload = collect($items)
                ->map(function ($item) use ($version, $now) {
                    return [
                        'code' => $item['code'],
                        'name' => $item['name'],
                        'version' => $version,
                        'parent_code' => $item['parent_code'] ?? null,
                        'level' => $item['level'] ?? 0,
                        'is_leaf' => $item['is_leaf'] ?? false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                })
                ->values()
                ->all();

            if (empty($payload)) {
                continue;
            }

            PkdCode::upsert($payload, ['code', 'version'], ['name', 'parent_code', 'level', 'is_leaf', 'updated_at']);
        }
    }

    protected function fallbackSet(string $version): array
    {
        if ($version === '2007') {
            return [
                [
                    'code' => '01',
                    'name' => 'Rolnictwo, leśnictwo, łowiectwo i rybactwo',
                    'level' => 1,
                    'parent_code' => null,
                    'is_leaf' => false,
                ],
                [
                    'code' => '01.1',
                    'name' => 'Uprawy rolne, chów i hodowla zwierząt',
                    'level' => 2,
                    'parent_code' => '01',
                    'is_leaf' => false,
                ],
                [
                    'code' => '01.11.Z',
                    'name' => 'Uprawa zbóż, roślin strączkowych i oleistych',
                    'level' => 3,
                    'parent_code' => '01.1',
                    'is_leaf' => true,
                ],
                [
                    'code' => '46',
                    'name' => 'Handel hurtowy z wyłączeniem handlu pojazdami samochodowymi',
                    'level' => 1,
                    'parent_code' => null,
                    'is_leaf' => false,
                ],
                [
                    'code' => '46.90.Z',
                    'name' => 'Sprzedaż hurtowa niewyspecjalizowana',
                    'level' => 2,
                    'parent_code' => '46',
                    'is_leaf' => true,
                ],
            ];
        }

        return [
            [
                'code' => 'A',
                'name' => 'Uprawa roślin i chów zwierząt, łowiectwo, leśnictwo',
                'level' => 1,
                'parent_code' => null,
                'is_leaf' => false,
            ],
            [
                'code' => 'A.01',
                'name' => 'Rolnictwo i gospodarka łowiecka',
                'level' => 2,
                'parent_code' => 'A',
                'is_leaf' => false,
            ],
            [
                'code' => 'A.01.11',
                'name' => 'Uprawa zbóż i roślin nasiennych',
                'level' => 3,
                'parent_code' => 'A.01',
                'is_leaf' => true,
            ],
            [
                'code' => 'J',
                'name' => 'Informacja i komunikacja',
                'level' => 1,
                'parent_code' => null,
                'is_leaf' => false,
            ],
            [
                'code' => 'J.62',
                'name' => 'Działalność związana z oprogramowaniem',
                'level' => 2,
                'parent_code' => 'J',
                'is_leaf' => true,
            ],
        ];
    }
}
