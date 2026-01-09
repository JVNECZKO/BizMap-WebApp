<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportLog;
use App\Models\ImportMapping;
use App\Models\ImportSession;
use App\Services\BusinessImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    public function index()
    {
        $mappings = ImportMapping::query()->orderByDesc('created_at')->get();
        $logs = ImportLog::query()->orderByDesc('started_at')->limit(20)->get();

        return view('admin.imports.index', compact('mappings', 'logs'));
    }

    public function upload(Request $request, BusinessImportService $importService)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt,json,xml,xls,xlsx',
            ]);

            $file = $request->file('file');
            $format = strtolower($file->getClientOriginalExtension());
            $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $format;
            $path = $file->storeAs('imports', $filename);
            $fullPath = Storage::path($path);

            $columns = $importService->detectColumns($fullPath, $format);
            $preview = $importService->preview($fullPath, $format, config('bizmap.import.preview_rows'));

            $session = ImportSession::create([
                'filename' => $filename,
                'path' => $fullPath,
                'format' => $format,
                'status' => 'uploaded',
                'detected_columns' => $columns,
                'total_rows' => 0,
                'chunk_size' => config('bizmap.import.chunk'),
            ]);

            return response()->json([
                'token' => $session->token,
                'columns' => $columns,
                'preview' => $preview,
                'chunk' => $session->chunk_size,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Import upload failed', [
                'error' => $e->getMessage(),
                'file' => $request->file('file')?->getClientOriginalName(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Błąd wczytywania pliku. Sprawdź logi aplikacji.'], 500);
        }
    }

    public function start(Request $request)
    {
        $data = $request->validate([
            'token' => 'required|string',
            'mapping_id' => 'nullable|integer|exists:import_mappings,id',
        ]);

        $session = ImportSession::where('token', $data['token'])->firstOrFail();
        $session->status = 'running';
        $session->started_at = now();
        $session->mapping_id = $data['mapping_id'] ?? null;
        $session->save();

        ImportLog::create([
            'filename' => $session->filename,
            'format' => $session->format,
            'total_rows' => $session->total_rows,
            'imported_rows' => $session->imported_rows,
            'started_at' => $session->started_at,
            'status' => 'running',
            'meta' => ['token' => $session->token],
        ]);

        return response()->json(['status' => 'started', 'token' => $session->token]);
    }

    public function run(Request $request, BusinessImportService $importService)
    {
        try {
            $payload = $request->validate([
                'token' => 'required|string',
                'mapping' => 'array',
                'mapping_id' => 'nullable|integer|exists:import_mappings,id',
                'static_values' => 'array',
                'pkd_version' => 'nullable|string|in:2007,2025',
            ]);

            $session = ImportSession::where('token', $payload['token'])->firstOrFail();

            $selectedMapping = $payload['mapping'] ?? [];
            $static = $payload['static_values'] ?? [];

            if (empty($selectedMapping) && ! empty($payload['mapping_id'])) {
                $template = ImportMapping::find($payload['mapping_id']);
                if ($template) {
                    $selectedMapping = $template->mapping ?? [];
                    $static = $template->static_values ?? [];
                }
            }

            if (empty($selectedMapping)) {
                return response()->json(['error' => 'Brak mapowania kolumn.'], 422);
            }

            $pkdVersion = $payload['pkd_version'] ?? '2007';
            $result = $importService->runChunk($session, $selectedMapping, $static, $pkdVersion);

            $log = ImportLog::where('filename', $session->filename)->orderByDesc('started_at')->first();
            if ($log) {
                $log->imported_rows = $session->imported_rows;
                $log->status = $session->status;
                $log->finished_at = $session->status === 'finished' ? now() : null;
                $log->save();
            }

            if ($session->status === 'finished') {
                cache()->flush();
                app(\App\Services\FilterService::class)->clear();
                app(\App\Services\LocationService::class)->clear();
            }

            return response()->json([
                'imported' => $result['imported'],
                'processed' => $result['processed'],
                'status' => $session->status,
                'total_rows' => $session->total_rows,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Import run failed', [
                'error' => $e->getMessage(),
                'token' => $request->input('token'),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Błąd importu: ' . $e->getMessage()], 500);
        }
    }
}
