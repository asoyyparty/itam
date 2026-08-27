<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiTicketController extends Controller
{
    /**
     * Get list of tickets for mobile app.
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['asset', 'employee']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        $perPage = (int) $request->input('per_page', 15);
        $tickets = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tickets,
        ]);
    }

    /**
     * Create new support ticket from Flutter mobile app.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'employee_id' => 'required|exists:employees,id',
            'asset_id' => 'nullable|exists:assets,id',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'description' => 'required|string',
        ]);

        $ticketNumber = 'TICK-' . strtoupper(Str::random(6));

        $ticket = Ticket::create([
            'ticket_number' => $ticketNumber,
            'title' => $request->input('title'),
            'employee_id' => $request->input('employee_id'),
            'asset_id' => $request->input('asset_id'),
            'priority' => $request->input('priority'),
            'status' => 'Open',
            'description' => $request->input('description'),
        ]);

        $ticket->load(['asset', 'employee']);

        return response()->json([
            'success' => true,
            'message' => 'Tiket kendala berhasil dibuat.',
            'data' => $ticket,
        ], 201);
    }

    /**
     * Get ticket details.
     */
    public function show($id)
    {
        $ticket = Ticket::with(['asset', 'employee'])->find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ticket,
        ]);
    }
}
