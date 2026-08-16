<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Employee;
use App\Models\Ticket;
use App\Services\AiService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    protected AiService $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * AJAX Endpoint: Analyze Ticket with AI
     */
    public function analyzeTicket(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'employee_id' => 'nullable|exists:employees,id',
            'asset_id' => 'nullable|exists:assets,id',
        ]);

        $employee = $request->employee_id ? Employee::find($request->employee_id) : null;
        $asset = $request->asset_id ? Asset::with(['computer', 'printer', 'monitor', 'networkDetail', 'cctv', 'category'])->find($request->asset_id) : null;

        $assetSpecs = null;
        if ($asset) {
            if ($asset->computer) {
                $assetSpecs = "CPU: {$asset->computer->cpu}, RAM: {$asset->computer->ram}, SSD: {$asset->computer->ssd}, OS: {$asset->computer->os}";
            } elseif ($asset->printer) {
                $assetSpecs = "Tipe: {$asset->printer->type}, Toner: {$asset->printer->toner_status}";
            } elseif ($asset->networkDetail) {
                $assetSpecs = "Firmware: {$asset->networkDetail->firmware}, Ports: {$asset->networkDetail->port_count}";
            }
        }

        $analysis = $this->aiService->analyzeTicket([
            'title' => $request->title,
            'description' => $request->description ?? '',
            'employee_name' => $employee->name ?? 'User',
            'asset_name' => $asset->name ?? null,
            'asset_specs' => $assetSpecs,
        ]);

        return response()->json([
            'success' => true,
            'data' => $analysis,
        ]);
    }

    /**
     * AJAX Endpoint: Natural Language Search Query
     */
    public function querySearch(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|min:2',
        ]);

        $result = $this->aiService->queryNaturalLanguage($request->prompt);

        return response()->json([
            'success' => true,
            'result' => $result,
        ]);
    }
}
