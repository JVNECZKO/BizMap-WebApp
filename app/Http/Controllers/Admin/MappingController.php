<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MappingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'format' => 'nullable|string|max:20',
            'mapping' => 'required|array',
            'static_values' => 'array',
        ]);

        ImportMapping::updateOrCreate(
            ['slug' => Str::slug($data['name'])],
            [
                'name' => $data['name'],
                'format' => $data['format'] ?? null,
                'mapping' => $data['mapping'],
                'static_values' => $data['static_values'] ?? [],
                'detected_columns' => $data['detected_columns'] ?? [],
                'last_used_at' => now(),
            ]
        );

        return back()->with('status', 'Szablon importu zapisany.');
    }

    public function destroy(ImportMapping $mapping)
    {
        $mapping->delete();

        return back()->with('status', 'Szablon usunięty.');
    }

    public function export(ImportMapping $mapping)
    {
        return response()->json([
            'name' => $mapping->name,
            'format' => $mapping->format,
            'mapping' => $mapping->mapping,
            'static_values' => $mapping->static_values,
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json,txt',
        ]);

        $payload = json_decode(file_get_contents($request->file('file')->getRealPath()), true);

        if (! $payload || ! isset($payload['name'], $payload['mapping'])) {
            return back()->withErrors(['file' => 'Nieprawidłowy plik szablonu.']);
        }

        ImportMapping::updateOrCreate(
            ['slug' => Str::slug($payload['name'])],
            [
                'name' => $payload['name'],
                'format' => $payload['format'] ?? null,
                'mapping' => $payload['mapping'],
                'static_values' => $payload['static_values'] ?? [],
                'detected_columns' => $payload['detected_columns'] ?? [],
                'last_used_at' => now(),
            ]
        );

        return back()->with('status', 'Zaimportowano szablon mapowania.');
    }
}
