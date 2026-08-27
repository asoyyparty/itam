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

    public function index()
    {
        $pics = Pic::orderBy('name')->get();

        return view('pics.index', compact('pics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Pic::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'PIC ditambahkan berhasil.');
    }

    public function update(Request $request, Pic $pic)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $pic->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'PIC diperbarui berhasil.');
    }

    public function destroy(Pic $pic)
    {
        $pic->delete();

        return redirect()->back()->with('success', 'PIC dihapus berhasil.');
    }
}
