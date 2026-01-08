<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::setValue('seo.meta_title', 'BizMap – Ogólnopolski rejestr działalności gospodarczych');
        Setting::setValue('seo.meta_description', 'Szybka wyszukiwarka firm w Polsce. Aktualne dane CEIDG, filtry PKD, powiaty, gminy i miejscowości.');
        Setting::setValue('seo.meta_keywords', 'rejestr firm, ceidg, pkd, baza firm, wyszukiwarka działalności');
        Setting::setValue('pkd.version', '2007');

        Setting::setValue('admin.prefix', config('bizmap.admin_prefix'));

        $defaultDriver = env('DB_CONNECTION', 'sqlite');
        $defaultDatabase = $defaultDriver === 'sqlite'
            ? database_path('database.sqlite')
            : env('DB_DATABASE', 'laravel');

        Setting::setValue('db.driver', $defaultDriver);
        Setting::setValue('db.host', env('DB_HOST', '127.0.0.1'));
        Setting::setValue('db.port', env('DB_PORT', '3306'));
        Setting::setValue('db.database', $defaultDatabase);
        Setting::setValue('db.username', env('DB_USERNAME', 'root'));
        Setting::setValue('db.password', env('DB_PASSWORD', ''));

        Setting::setValue('sitemap.last_generated_at', null, 'json');
    }
}
