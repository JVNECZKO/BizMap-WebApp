<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Setting;
use App\Services\BusinessSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    public function index(Request $request, BusinessSearchService $searchService)
    {
        $filters = $request->only([
            'q',
            'imie',
            'nazwisko',
            'pkd',
            'wojewodztwo',
            'powiat',
            'gmina',
            'miejscowosc',
            'kod_pocztowy',
            'status',
            'date_from',
            'date_to',
        ]);

        $cursor = $request->get('cursor');
        $perPage = (int) $request->get('per_page', config('bizmap.pagination.per_page'));

        $results = $searchService->search($filters, $cursor, $perPage);
        $filterOptions = app(\App\Services\FilterService::class)->get();
        $pkdVersion = Setting::get('pkd.version', '2007');

        return view('companies.index', [
            'results' => $results,
            'filters' => $filters,
            'filterOptions' => $filterOptions,
            'pkdVersion' => $pkdVersion,
        ]);
    }

    public function export(Request $request)
    {
        $filters = $request->only([
            'q',
            'imie',
            'nazwisko',
            'pkd',
            'wojewodztwo',
            'powiat',
            'gmina',
            'miejscowosc',
            'kod_pocztowy',
            'status',
            'date_from',
            'date_to',
        ]);

        $onlyActive = $request->boolean('only_active');
        $onlyEmail = $request->boolean('only_email');
        $onlyPhone = $request->boolean('only_phone');
        $selectedFields = $request->input('fields', []);

        $availableFields = [
            'full_name' => 'Nazwa firmy',
            'nip' => 'NIP',
            'regon' => 'REGON',
            'status_dzialalnosci' => 'Status',
            'glowny_kod_pkd' => 'PKD główne',
            'wojewodztwo' => 'Województwo',
            'powiat' => 'Powiat',
            'gmina' => 'Gmina',
            'miejscowosc' => 'Miejscowość',
            'ulica' => 'Ulica',
            'nr_budynku' => 'Nr budynku',
            'nr_lokalu' => 'Nr lokalu',
            'kod_pocztowy' => 'Kod pocztowy',
            'telefon' => 'Telefon',
            'email' => 'Email',
            'adres_www' => 'Adres www',
            'data_rozpoczecia_dzialalnosci' => 'Data rozpoczęcia',
            'data_zawieszenia_dzialalnosci' => 'Data zawieszenia',
            'data_wznowienia_dzialalnosci' => 'Data wznowienia',
        ];

        $fields = array_values(array_intersect(array_keys($availableFields), $selectedFields));
        if (empty($fields)) {
            $fields = ['full_name', 'nip', 'regon', 'status_dzialalnosci', 'glowny_kod_pkd', 'wojewodztwo', 'miejscowosc', 'telefon', 'email'];
        }

        $query = Business::query()->select($fields)->filter($filters)->orderBy('id');

        if ($onlyActive) {
            $query->whereRaw('LOWER(status_dzialalnosci) = ?', ['aktywny']);
        }
        if ($onlyEmail) {
            $query->whereNotNull('email')->where('email', '!=', '');
        }
        if ($onlyPhone) {
            $query->whereNotNull('telefon')->where('telefon', '!=', '');
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bizmap_export.csv"',
        ];

        return response()->stream(function () use ($query, $fields, $availableFields) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, array_map(fn($f) => $availableFields[$f], $fields));

            $query->chunk(1000, function ($chunk) use ($handle, $fields) {
                foreach ($chunk as $row) {
                    $line = [];
                    foreach ($fields as $field) {
                        $line[] = $row->{$field};
                    }
                    fputcsv($handle, $line);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    public function locations(Request $request)
    {
        $woj = $request->get('wojewodztwo');
        $powiat = $request->get('powiat');
        $gmina = $request->get('gmina');

        $lists = app(\App\Services\LocationService::class)->getLists($woj, $powiat, $gmina);

        return response()->json($lists);
    }

    public function pkdCodes(Request $request)
    {
        $version = Setting::get('pkd.version', '2007');
        $codes = Cache::rememberForever("pkd_codes_{$version}_leaf", function () use ($version) {
            return \App\Models\PkdCode::query()
                ->where('version', $version)
                ->orderBy('code')
                ->get(['code', 'name'])
                ->filter(fn($c) => preg_match('/[A-Z]$/', $c->code))
                ->values();
        });

        return response()->json($codes);
    }
}
