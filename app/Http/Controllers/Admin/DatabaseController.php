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
