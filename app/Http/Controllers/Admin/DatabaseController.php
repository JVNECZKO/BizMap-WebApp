<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    public function index()
    {
        return view('admin.database.index', [
            'driver' => Setting::get('db.driver', config('database.default')),
            'host' => Setting::get('db.host', '127.0.0.1'),
            'port' => Setting::get('db.port', '3306'),
            'database' => Setting::get('db.database', database_path('database.sqlite')),
            'username' => Setting::get('db.username', 'root'),
            'password' => Setting::get('db.password', ''),
            'target' => Setting::get('migration.target', [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => '',
                'username' => '',
                'password' => '',
            ]),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'driver' => 'required|in:sqlite,mysql',
            'host' => 'nullable|string|max:255',
            'port' => 'nullable|string|max:10',
            'database' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        Setting::setValue('db.driver', $data['driver']);
        Setting::setValue('db.host', $data['host'] ?? '');
        Setting::setValue('db.port', $data['port'] ?? '');
        Setting::setValue('db.database', $data['database']);
        Setting::setValue('db.username', $data['username'] ?? '');
        Setting::setValue('db.password', $data['password'] ?? '');

        return back()->with('status', 'Zapisano konfigurację bazy danych.');
    }

    public function test(Request $request)
    {
        $data = $request->validate([
            'driver' => 'required|in:sqlite,mysql',
            'host' => 'nullable|string|max:255',
            'port' => 'nullable|string|max:10',
            'database' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        $config = $this->buildConnectionConfig($data);

        try {
            Config::set('database.connections.runtime', $config);
            DB::purge('runtime');
            DB::connection('runtime')->getPdo();
        } catch (\Throwable $e) {
            return back()->withErrors(['database' => 'Błąd połączenia: ' . $e->getMessage()]);
        }

        return back()->with('status', 'Połączenie działa poprawnie.');
    }

    public function migrate(Request $request)
    {
        $data = $request->validate([
            'driver' => 'required|in:sqlite,mysql',
            'host' => 'nullable|string|max:255',
            'port' => 'nullable|string|max:10',
            'database' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        $config = $this->buildConnectionConfig($data);
        Config::set('database.connections.runtime', $config);

        try {
            DB::purge('runtime');
            Artisan::call('migrate', [
                '--database' => 'runtime',
                '--force' => true,
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['database' => 'Migracja nie powiodła się: ' . $e->getMessage()]);
        }

        return back()->with('status', 'Schema bazy danych została zaktualizowana.');
    }

    public function migrationSave(Request $request)
    {
        $data = $request->validate([
            'driver' => 'required|in:mysql',
            'host' => 'required|string|max:255',
            'port' => 'nullable|string|max:10',
            'database' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        Setting::setValue('migration.target', $data, 'json');

        return back()->with('status', 'Konfiguracja docelowej bazy zapisana.');
    }

    public function migrationClear()
    {
        Setting::setValue('migration.target', null, 'json');

        return back()->with('status', 'Usunięto konfigurację migracji.');
    }

    public function migrationRun()
    {
        $target = Setting::get('migration.target');
        if (! is_array($target) || empty($target['database'])) {
            return response()->json(['error' => 'Brak zapisanej konfiguracji bazy docelowej.'], 422);
        }

        $config = $this->buildConnectionConfig($target);
        Config::set('database.connections.migration', $config);

        $log = [];
        $this->logStep($log, 'Łączenie z bazą docelową...');

        try {
            DB::purge('migration');
            DB::connection('migration')->getPdo();
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Połączenie z bazą docelową nie działa: ' . $e->getMessage()], 500);
        }

        try {
            $this->logStep($log, 'Uruchamiam migracje schematu na bazie docelowej...');
            Artisan::call('migrate', ['--database' => 'migration', '--force' => true]);
            $this->logStep($log, trim(Artisan::output()));
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Migracja schematu nie powiodła się: ' . $e->getMessage(), 'log' => $log], 500);
        }

        $tables = [
            'businesses' => 'id',
            'business_pkd_codes' => 'id',
            'business_raw_payloads' => 'id',
            'pkd_codes' => 'id',
            'pkd_popularity' => 'pkd_code',
            'import_logs' => 'id',
            'import_mappings' => 'id',
            'settings' => 'id',
        ];

        foreach ($tables as $table => $orderBy) {
            try {
                $this->copyTable($table, $orderBy, $log);
            } catch (\Throwable $e) {
                $this->logStep($log, "❌ Błąd kopiowania {$table}: " . $e->getMessage());
                \Log::error('Migration copy table failed', ['table' => $table, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                return response()->json(['error' => "Migracja przerwana na tabeli {$table}: " . $e->getMessage(), 'log' => $log], 500);
            }
        }

        $this->logStep($log, 'Migracja danych zakończona. Możesz teraz bezpiecznie przełączyć bazę danych.');

        return response()->json(['ok' => true, 'log' => $log, 'message' => 'Możesz teraz bezpiecznie przełączyć bazę danych.']);
    }

    protected function copyTable(string $table, string $orderBy, array &$log): void
    {
        $source = DB::connection(); // aktualna (sqlite)
        $target = DB::connection('migration');
        $chunk = 500;

        $total = $source->table($table)->count();
        $this->logStep($log, "Kopiuję tabelę {$table} ({$total} rekordów)...");

        $source->table($table)->orderBy($orderBy)->chunk($chunk, function ($rows) use ($target, $table, &$log) {
            $payload = [];
            foreach ($rows as $row) {
                $payload[] = (array) $row;
            }
            if (! empty($payload)) {
                foreach (array_chunk($payload, 200) as $batch) {
                    $target->table($table)->insert($batch);
                }
            }
            $this->logStep($log, "✔ {$table}: " . count($payload) . " rekordów przeniesiono.");
        });
    }

    protected function logStep(array &$log, string $message): void
    {
        $log[] = '[' . now()->format('H:i:s') . '] ' . $message;
    }

    protected function buildConnectionConfig(array $data): array
    {
        if ($data['driver'] === 'sqlite') {
            return [
                'driver' => 'sqlite',
                'database' => $data['database'],
                'prefix' => '',
                'foreign_key_constraints' => true,
            ];
        }

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
            'options' => [
                \PDO::ATTR_PERSISTENT => true,
            ],
        ];
    }
}
