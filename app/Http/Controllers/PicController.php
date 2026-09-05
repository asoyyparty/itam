<?php

namespace App\Http\Controllers;

use App\Models\Pic;
use Illuminate\Http\Request;

class PicController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new \Illuminate\Routing\Controllers\Middleware('permission:action_manage_master_data', only: ['create', 'store', 'edit', 'update', 'destroy', 'importExcel', 'updateStatus', 'complete', 'generateTag', 'returnAsset', 'pingBatch', 'ping', 'updateAll']),
        ];
    }

    public function index(Request $request)
    {
        $query = Pic::query()
            ->withCount([
                'tickets as active_tickets_count' => function ($q) {
                    $q->whereIn('status', ['open', 'in_progress']);
                },
                'tickets as resolved_tickets_count' => function ($q) {
                    $q->whereIn('status', ['resolved', 'closed']);
                },
                'tickets as total_tickets_count',
            ])
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('name', 'like', "%{$search}%");
        }

        $pics = $query->get();

        $totalPics = Pic::count();
        $totalActiveTickets = \App\Models\Ticket::whereNotNull('pic_id')
            ->whereIn('status', ['open', 'in_progress'])
            ->count();
        $totalResolvedTickets = \App\Models\Ticket::whereNotNull('pic_id')
            ->whereIn('status', ['resolved', 'closed'])
            ->count();
        $standbyPicsCount = $pics->where('active_tickets_count', 0)->count();

        return view('pics.index', compact(
            'pics',
            'totalPics',
            'totalActiveTickets',
            'totalResolvedTickets',
            'standbyPicsCount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Pic::create([
            'name' => trim($request->name),
        ]);

        return redirect()->back()->with('success', __('messages.pic_created_success') ?? 'PIC ditambahkan berhasil.');
    }

    public function update(Request $request, Pic $pic)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $pic->update([
            'name' => trim($request->name),
        ]);

        return redirect()->back()->with('success', __('messages.pic_updated_success') ?? 'PIC diperbarui berhasil.');
    }

    public function destroy(Pic $pic)
    {
        $pic->delete();

        return redirect()->back()->with('success', __('messages.pic_deleted_success') ?? 'PIC dihapus berhasil.');
    }
}
