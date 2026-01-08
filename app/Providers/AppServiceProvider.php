<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->ensureSqliteExists();
        Schema::defaultStringLength(191);
        Paginator::useTailwind();

        if (Schema::hasTable('settings')) {
            $adminPrefix = Setting::get('admin.prefix', config('bizmap.admin_prefix'));
            config(['bizmap.admin_prefix' => $adminPrefix]);

            $driver = Setting::get('db.driver', config('database.default'));
            $connection = config("database.connections.$driver", config('database.connections.sqlite'));

            if ($driver === 'sqlite') {
                $dbPath = Setting::get('db.database', database_path('database.sqlite'));
                // allow relative paths stored in settings
                if (! str_starts_with($dbPath, '/')) {
                    $dbPath = base_path($dbPath);
                }

                if (! file_exists($dbPath)) {
                    @touch($dbPath);
                }

                $connection['database'] = $dbPath;
            } else {
                $connection['host'] = Setting::get('db.host', $connection['host'] ?? '127.0.0.1');
                $connection['port'] = Setting::get('db.port', $connection['port'] ?? '3306');
                $connection['database'] = Setting::get('db.database', $connection['database'] ?? '');
                $connection['username'] = Setting::get('db.username', $connection['username'] ?? 'root');
                $connection['password'] = Setting::get('db.password', $connection['password'] ?? '');
            }

            config([
                "database.connections.$driver" => $connection,
                'database.default' => $driver,
            ]);

            DB::purge($driver);
        }
    }

    protected function ensureSqliteExists(): void
    {
        $default = config('database.default');
        $connection = config("database.connections.$default");

        if (($connection['driver'] ?? null) !== 'sqlite') {
            return;
        }

        $dbPath = $connection['database'] ?? database_path('database.sqlite');
        if (! str_starts_with($dbPath, '/')) {
            $dbPath = base_path($dbPath);
        }

        // If the configured path points to a missing file (np. ze środowiska dev),
        // spróbuj użyć domyślnej ścieżki w bieżącym środowisku.
        $defaultPath = database_path('database.sqlite');
        if (! file_exists($dbPath) && file_exists(dirname($defaultPath))) {
            $dbPath = $defaultPath;
        }

        $dir = dirname($dbPath);
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        if (! file_exists($dbPath)) {
            @touch($dbPath);
        }

        config(["database.connections.$default.database" => $dbPath]);
    }
}
