<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BusinessController as AdminBusinessController;
use App\Http\Controllers\Admin\DatabaseController as AdminDatabaseController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ImportController as AdminImportController;
use App\Http\Controllers\Admin\MappingController as AdminMappingController;
use App\Http\Controllers\Admin\SeoController as AdminSeoController;
use App\Http\Controllers\Admin\SitemapController as AdminSitemapController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/firmy', [SearchController::class, 'index'])->name('companies.index');
Route::get('/firmy/export', [SearchController::class, 'export'])->name('companies.export');
Route::get('/locations', [SearchController::class, 'locations'])->name('locations');
Route::get('/pkd-codes', [SearchController::class, 'pkdCodes'])->name('pkd.codes');
Route::post('/ab/pass', function (Request $r) {
    $minutes = 10; // short TTL
    return response()->json(['ok' => true])
        ->cookie(cookie('ab_ok', '1', $minutes, null, null, true, true, false, 'Lax'));
})->name('ab.pass');
Route::post('/ab/fail', function () {
    return response()->json(['ok' => true])
        ->cookie(Cookie::forget('ab_ok'));
})->name('ab.fail');
Route::get('/firma/{id}-{slug}', [CompanyController::class, 'show'])->name('company.show');
Route::get('/pkd/{code}/{slug}/{region?}', [\App\Http\Controllers\SeoController::class, 'pkdLanding'])->name('seo.pkd');
Route::get('/pkd', [\App\Http\Controllers\PageController::class, 'pkd'])->name('pkd.index');
Route::get('/o-nas', [\App\Http\Controllers\PageController::class, 'about'])->name('about');
Route::get('/kontakt', [\App\Http\Controllers\PageController::class, 'contact'])->name('contact');
Route::post('/kontakt', [\App\Http\Controllers\ContactFormController::class, 'send'])->name('contact.send');
Route::get('/polityka-prywatnosci', [\App\Http\Controllers\PageController::class, 'privacy'])->name('privacy');
Route::get('/branze', [\App\Http\Controllers\TaxonomyController::class, 'index'])->name('taxonomy.public');

Route::get('/login', [AdminAuthController::class, 'showLogin'])->middleware('guest')->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->middleware('guest')->name('login.submit');
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

Route::prefix(config('bizmap.admin_prefix'))->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/firmy', [AdminBusinessController::class, 'index'])->name('businesses.index');
    Route::get('/firmy/{business}/edit', [AdminBusinessController::class, 'edit'])->name('businesses.edit');
    Route::put('/firmy/{business}', [AdminBusinessController::class, 'update'])->name('businesses.update');
    Route::delete('/firmy/{business}', [AdminBusinessController::class, 'destroy'])->name('businesses.destroy');
    Route::post('/firmy/bulk-delete', [AdminBusinessController::class, 'bulkDestroy'])->name('businesses.bulk-delete');
    Route::post('/firmy/wipe-all', [AdminBusinessController::class, 'wipeAll'])->name('businesses.wipe');

    Route::get('/importy', [AdminImportController::class, 'index'])->name('imports.index');
    Route::post('/importy/upload', [AdminImportController::class, 'upload'])->name('imports.upload');
    Route::post('/importy/start', [AdminImportController::class, 'start'])->name('imports.start');
    Route::post('/importy/run', [AdminImportController::class, 'run'])->name('imports.run');

    Route::post('/importy/mappings', [AdminMappingController::class, 'store'])->name('mappings.store');
    Route::post('/importy/mappings/import', [AdminMappingController::class, 'import'])->name('mappings.import');
    Route::get('/importy/mappings/{mapping}/export', [AdminMappingController::class, 'export'])->name('mappings.export');
    Route::delete('/importy/mappings/{mapping}', [AdminMappingController::class, 'destroy'])->name('mappings.destroy');

    Route::get('/seo', [AdminSeoController::class, 'index'])->name('seo.index');
    Route::post('/seo', [AdminSeoController::class, 'update'])->name('seo.update');

    Route::get('/branding', [\App\Http\Controllers\Admin\BrandingController::class, 'index'])->name('branding.index');
    Route::post('/branding', [\App\Http\Controllers\Admin\BrandingController::class, 'update'])->name('branding.update');
    Route::get('/kontakt', [\App\Http\Controllers\Admin\ContactSettingsController::class, 'index'])->name('contact.index');
    Route::post('/kontakt', [\App\Http\Controllers\Admin\ContactSettingsController::class, 'update'])->name('contact.update');
    Route::get('/kontakt/{message}', [\App\Http\Controllers\Admin\ContactSettingsController::class, 'show'])->name('contact.show');
    Route::get('/lokalizacje', [\App\Http\Controllers\Admin\LocationController::class, 'index'])->name('locations.index');
    Route::post('/lokalizacje/rebuild', [\App\Http\Controllers\Admin\LocationController::class, 'rebuild'])->name('locations.rebuild');
    Route::get('/pkd', [\App\Http\Controllers\Admin\PkdController::class, 'index'])->name('pkd.index');
    Route::post('/pkd/import', [\App\Http\Controllers\Admin\PkdController::class, 'import'])->name('pkd.import');
    Route::post('/pkd/normalize', [\App\Http\Controllers\Admin\PkdController::class, 'normalize'])->name('pkd.normalize');
    Route::post('/pkd/recount', [\App\Http\Controllers\Admin\PkdController::class, 'recount'])->name('pkd.recount');
    Route::get('/pkd-taksonomia', [\App\Http\Controllers\Admin\PkdTaxonomyController::class, 'index'])->name('taxonomy.index');
    Route::post('/pkd-taksonomia/import', [\App\Http\Controllers\Admin\PkdTaxonomyController::class, 'import'])->name('taxonomy.import');
    Route::put('/pkd-taksonomia/{taxonomy}', [\App\Http\Controllers\Admin\PkdTaxonomyController::class, 'update'])->name('taxonomy.update');
    Route::delete('/pkd-taksonomia', [\App\Http\Controllers\Admin\PkdTaxonomyController::class, 'destroyAll'])->name('taxonomy.destroyAll');
    Route::get('/konto', [\App\Http\Controllers\Admin\AccountController::class, 'edit'])->name('account.edit');
    Route::post('/konto', [\App\Http\Controllers\Admin\AccountController::class, 'update'])->name('account.update');
    Route::get('/two-factor', [\App\Http\Controllers\Admin\TwoFactorController::class, 'show'])->name('2fa.show');
    Route::post('/two-factor', [\App\Http\Controllers\Admin\TwoFactorController::class, 'verify'])->name('2fa.verify');

    Route::get('/sitemap', [AdminSitemapController::class, 'index'])->name('sitemap.index');
    Route::post('/sitemap/start', [AdminSitemapController::class, 'start'])->name('sitemap.start');
    Route::post('/sitemap/update', [AdminSitemapController::class, 'update'])->name('sitemap.update');
    Route::post('/sitemap/pkd', [AdminSitemapController::class, 'startPkd'])->name('sitemap.pkd');
    Route::post('/sitemap/run', [AdminSitemapController::class, 'run'])->name('sitemap.run');
    Route::post('/sitemap/clear', [AdminSitemapController::class, 'clear'])->name('sitemap.clear');
    Route::post('/sitemap/reindex', [AdminSitemapController::class, 'reindex'])->name('sitemap.reindex');
    Route::get('/reklamy', [\App\Http\Controllers\Admin\AdsController::class, 'index'])->name('ads.index');
    Route::post('/reklamy', [\App\Http\Controllers\Admin\AdsController::class, 'update'])->name('ads.update');

    Route::get('/debug', [\App\Http\Controllers\Admin\DebugController::class, 'index'])->name('debug.index');
    Route::post('/debug', [\App\Http\Controllers\Admin\DebugController::class, 'update'])->name('debug.update');

    Route::get('/baza', [AdminDatabaseController::class, 'index'])->name('database.index');
    Route::post('/baza/update', [AdminDatabaseController::class, 'update'])->name('database.update');
    Route::post('/baza/test', [AdminDatabaseController::class, 'test'])->name('database.test');
    Route::post('/baza/migrate', [AdminDatabaseController::class, 'migrate'])->name('database.migrate');
    Route::match(['post','get'],'/baza/migration/save', [AdminDatabaseController::class, 'migrationSave'])->name('database.migration.save');
    Route::match(['post','get'],'/baza/migration/clear', [AdminDatabaseController::class, 'migrationClear'])->name('database.migration.clear');
    Route::match(['post','get'],'/baza/migration/start', [AdminDatabaseController::class, 'migrationStart'])->name('database.migration.start');
    Route::match(['post','get'],'/baza/migration/run', [AdminDatabaseController::class, 'migrationRun'])->name('database.migration.run');
    Route::match(['post','get'],'/baza/migration/direct', [AdminDatabaseController::class, 'migrationDirect'])->name('database.migration.direct');
    // aliasy bez słowa "migration" (na wypadek WAF)
    Route::match(['post','get'],'/baza/transfer/save', [AdminDatabaseController::class, 'migrationSave'])->name('database.transfer.save');
    Route::match(['post','get'],'/baza/transfer/clear', [AdminDatabaseController::class, 'migrationClear'])->name('database.transfer.clear');
    Route::match(['post','get'],'/baza/transfer/start', [AdminDatabaseController::class, 'migrationStart'])->name('database.transfer.start');
    Route::match(['post','get'],'/baza/transfer/run', [AdminDatabaseController::class, 'migrationRun'])->name('database.transfer.run');
    Route::match(['post','get'],'/baza/transfer/direct', [AdminDatabaseController::class, 'migrationDirect'])->name('database.transfer.direct');
});
