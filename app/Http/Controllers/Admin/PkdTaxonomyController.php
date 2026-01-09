<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PkdTaxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PkdTaxonomyController extends Controller
{
    public function index()
    {
        $items = PkdTaxonomy::query()
            ->orderBy('group_name')
            ->orderBy('subgroup_name')
            ->paginate(50);

        return view('admin.pkd_taxonomy.index', [
            'items' => $items,
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $path = $request->file('file')->getRealPath();
        $rows = array_map('str_getcsv', file($path));
        if (count($rows) <= 1) {
            return back()->withErrors(['file' => 'Brak danych w pliku.']);
        }

        $header = array_map(fn($h) => trim($h), array_shift($rows));
        $expected = ['aleo_grupa','aleo_podgrupa','pkd_primary','pkd_secondary'];
        if (array_map('strtolower', $header) !== array_map('strtolower', $expected)) {
            return back()->withErrors(['file' => 'Nagłówek CSV musi zawierać: '.implode(',', $expected)]);
        }

        PkdTaxonomy::truncate();
        foreach ($rows as $row) {
            if (count($row) < 4) {
                continue;
            }
            [$group, $sub, $primary, $secondary] = $row;
            $secondaryCodes = $secondary ? array_values(array_filter(array_map('trim', explode('|', $secondary)))) : [];

            PkdTaxonomy::create([
                'group_name' => trim($group),
                'subgroup_name' => trim($sub),
                'group_slug' => Str::slug($group),
                'subgroup_slug' => Str::slug($sub),
                'primary_code' => trim($primary) ?: null,
                'secondary_codes' => $secondaryCodes,
            ]);
        }

        return back()->with('status', 'Zaimportowano mapowanie PKD.');
    }

    public function update(Request $request, PkdTaxonomy $taxonomy)
    {
        $data = $request->validate([
            'group_name' => ['required', 'string', 'max:255'],
            'subgroup_name' => ['required', 'string', 'max:255'],
            'primary_code' => ['nullable', 'string', 'max:50'],
            'secondary_codes' => ['nullable', 'string'],
        ]);

        $secondary = $data['secondary_codes'] ?? '';
        $secondaryCodes = $secondary ? array_values(array_filter(array_map('trim', explode('|', $secondary)))) : [];

        $taxonomy->update([
            'group_name' => $data['group_name'],
            'subgroup_name' => $data['subgroup_name'],
            'group_slug' => Str::slug($data['group_name']),
            'subgroup_slug' => Str::slug($data['subgroup_name']),
            'primary_code' => $data['primary_code'] ?: null,
            'secondary_codes' => $secondaryCodes,
        ]);

        return back()->with('status', 'Zapisano zmiany.');
    }

    public function destroyAll()
    {
        PkdTaxonomy::truncate();
        return back()->with('status', 'Usunięto wszystkie rekordy.');
    }
}
