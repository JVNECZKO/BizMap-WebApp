<?php

namespace App\Http\Controllers\Admin;

use App\Console\Commands\ImportPkdCommand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class PkdController extends Controller
{
    public function index()
    {
        return view('admin.pkd.index');
    }

    public function import(Request $request): JsonResponse
    {
        return $this->runCommand('pkd:import', ['version' => 'all']);
    }

    public function normalize(Request $request): JsonResponse
    {
        return $this->runCommand('pkd:normalize-business');
    }

    public function recount(Request $request): JsonResponse
    {
        return $this->runCommand('pkd:recount-popular');
    }

    protected function runCommand(string $command, array $params = []): JsonResponse
    {
        try {
            @set_time_limit(0);
            Artisan::call($command, $params);
            $output = Artisan::output();
            return response()->json([
                'ok' => true,
                'output' => $output,
            ]);
        } catch (\Throwable $e) {
            Log::error("PKD command error: {$command}", ['error' => $e->getMessage()]);
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
