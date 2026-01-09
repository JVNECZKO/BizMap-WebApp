<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseTransferDirect extends Command
{
    protected $signature = 'db:transfer-direct {--chunk=50000 : Liczba rekordów na partię}';
    protected $description = 'Szybka migracja danych z aktualnej bazy (sqlite) do docelowej (MySQL) w jednym przebiegu.';

    protected array $businessIdMapOldToNew = [];
    protected int $chunk;

    public function handle(): int
    {
        $this->chunk = (int) $this->option('chunk');

        $target = Setting::get('migration.target');
        if (! is_array($target) || empty($target['database'])) {
            $this->error('Brak konfiguracji docelowej bazy w ustawieniach (migration.target).');
            return self::FAILURE;
        }

        $config = $this->buildConnectionConfig($target);
        Config::set('database.connections.migration', $config);

        $this->info('Łączenie z bazą docelową...');
        try {
            DB::purge('migration');
            DB::connection('migration')->getPdo();
        } catch (\Throwable $e) {
            $this->error('Połączenie nie działa: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('Migracje schematu na bazie docelowej...');
        try {
            Artisan::call('migrate', ['--database' => 'migration', '--force' => true]);
            $this->line(Artisan::output());
        } catch (\Throwable $e) {
            $this->error('Migracje schematu nie powiodły się: ' . $e->getMessage());
            return self::FAILURE;
        }

        $targetConn = DB::connection('migration');
        $this->ensureMigrationMapTable($targetConn, true);

        foreach ($this->migrationTables() as $table) {
            $name = $table['name'];
            $orderBy = $table['order'];

            $this->info("Kopiuję {$name} (chunk {$this->chunk})...");
            $offset = 0;
            while (true) {
                $rows = DB::connection()->table($name)
                    ->orderBy($orderBy)
                    ->offset($offset)
                    ->limit($this->chunk)
                    ->get()
                    ->map(fn($r) => (array) $r)
                    ->all();

                if (empty($rows)) {
                    break;
                }

                $this->transferRows($targetConn, $name, $rows);
                $offset += count($rows);
                $this->line("✔ {$name}: przeniesiono kolejnych " . count($rows) . " (offset {$offset})");
            }

            $this->line("✔ {$name}: ukończono.");
        }

        $this->info('Gotowe. Możesz przełączyć bazę danych.');
        return self::SUCCESS;
    }

    protected function transferRows($target, string $name, array $rows): void
    {
        if ($name === 'businesses') {
            $this->migrateBusinesses($target, $rows);
            return;
        }
        if (in_array($name, ['business_pkd_codes', 'business_raw_payloads'], true)) {
            $this->migrateBusinessChildren($target, $name, $rows);
            return;
        }
        if ($name === 'pkd_codes') {
            $this->migratePkdCodes($target, $rows);
            return;
        }
        $this->insertGeneric($target, $name, $rows);
    }

    protected function migrateBusinesses($target, array $rows): void
    {
        if (empty($rows)) {
            return;
        }

        $batch = [];
        $oldBySlug = [];
        $slugs = [];
        foreach ($rows as $row) {
            $oldId = $row['id'] ?? null;
            unset($row['id']);
            $batch[] = $row;
            if (! empty($row['slug'])) {
                $slugs[] = $row['slug'];
                if ($oldId) {
                    $oldBySlug[$row['slug']] = $oldId;
                }
            }
        }

        foreach (array_chunk($batch, 500) as $chunk) {
            $target->table('businesses')->upsert($chunk, ['slug']);
        }

        if (! empty($slugs)) {
            $existing = $target->table('businesses')->whereIn('slug', $slugs)->get(['id', 'slug']);
            foreach ($existing as $item) {
                if (isset($oldBySlug[$item->slug])) {
                    $this->businessIdMapOldToNew[$oldBySlug[$item->slug]] = $item->id;
                    $target->table('migration_business_map')->updateOrInsert(
                        ['old_id' => $oldBySlug[$item->slug]],
                        ['slug' => $item->slug, 'new_id' => $item->id]
                    );
                }
            }
        }
    }

    protected function migrateBusinessChildren($target, string $table, array $rows): void
    {
        if (empty($rows)) {
            return;
        }

        $batch = [];
        foreach ($rows as $row) {
            unset($row['id']);
            $bizId = $row['business_id'] ?? null;
            $mapped = $target->table('migration_business_map')->where('old_id', $bizId)->first();
            if ($mapped && $mapped->new_id) {
                $row['business_id'] = $mapped->new_id;
            } elseif ($bizId && isset($this->businessIdMapOldToNew[$bizId])) {
                $row['business_id'] = $this->businessIdMapOldToNew[$bizId];
            } else {
                continue;
            }

            $batch[] = $row;
        }

        foreach (array_chunk($batch, 1000) as $chunk) {
            $target->table($table)->insertOrIgnore($chunk);
        }
    }

    protected function migratePkdCodes($target, array $rows): void
    {
        $batch = [];
        foreach ($rows as $row) {
            unset($row['id']);
            $batch[] = $row;
        }
        foreach (array_chunk($batch, 1000) as $chunk) {
            $target->table('pkd_codes')->upsert($chunk, ['code', 'version']);
        }
    }

    protected function insertGeneric($target, string $table, array $rows): void
    {
        $batch = [];
        foreach ($rows as $row) {
            unset($row['id']);
            $batch[] = $row;
        }
        foreach (array_chunk($batch, 1000) as $chunk) {
            $target->table($table)->insert($chunk);
        }
    }

    protected function ensureMigrationMapTable($connection, bool $reset = false): void
    {
        $schema = Schema::connection($connection->getName());
        if (! $schema->hasTable('migration_business_map')) {
            $schema->create('migration_business_map', function ($table) {
                $table->unsignedBigInteger('old_id')->unique();
                $table->string('slug')->nullable();
                $table->unsignedBigInteger('new_id')->nullable();
                $table->index('slug');
            });
        } elseif ($reset) {
            $connection->table('migration_business_map')->truncate();
        }
    }

    protected function buildConnectionConfig(array $data): array
    {
        return [
            'driver' => 'mysql',
            'host' => $data['host'] ?? '127.0.0.1',
            'port' => $data['port'] ?? '3306',
            'database' => $data['database'],
            'username' => $data['username'] ?? 'root',
            'password' => $data['password'] ?? '',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => 'InnoDB',
        ];
    }

    protected function migrationTables(): array
    {
        return [
            ['name' => 'businesses', 'order' => 'id'],
            ['name' => 'business_pkd_codes', 'order' => 'id'],
            ['name' => 'business_raw_payloads', 'order' => 'id'],
            ['name' => 'pkd_codes', 'order' => 'id'],
            ['name' => 'pkd_popularity', 'order' => 'pkd_code'],
            ['name' => 'import_logs', 'order' => 'id'],
            ['name' => 'import_mappings', 'order' => 'id'],
            ['name' => 'settings', 'order' => 'id'],
        ];
    }
}
