<?php

namespace App\Http\Controllers;

use App\Exports\LocationExport;
use App\Imports\LocationImport;
use App\Models\Location;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LocationController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new \Illuminate\Routing\Controllers\Middleware('permission:action_manage_master_data', only: ['create', 'store', 'edit', 'update', 'destroy', 'importExcel', 'updateStatus', 'complete', 'generateTag', 'returnAsset', 'pingBatch', 'ping', 'updateAll']),
        ];
    }

    public function index(Request $request)
    {
        $query = Location::query();
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }
        $locations = $query->orderBy('name')->get();

        return view('master.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('master.locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        Location::create($request->all());
        \Illuminate\Support\Facades\Cache::forget('master_locations_all');

        return redirect()->route('locations.index', request()->query())->with('success', __('messages.created_success'));
    }

    public function edit(Location $location)
    {
        return view('master.locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,'.$location->id,
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $location->update($request->all());
        \Illuminate\Support\Facades\Cache::forget('master_locations_all');

        return redirect()->route('locations.index', request()->query())->with('success', __('messages.updated_success'));
    }

    public function destroy(Location $location)
    {
        $location->delete();
        \Illuminate\Support\Facades\Cache::forget('master_locations_all');

        return redirect()->route('locations.index', request()->query())->with('success', __('messages.deleted_success'));
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new LocationExport($request), 'locations_' . date('Ymd_His') . '.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new LocationImport, $request->file('file'));
            \Illuminate\Support\Facades\Cache::forget('master_locations_all');

            return redirect()->route('locations.index', request()->query())->with('success', 'Data imported successfully.');
        } catch (\Exception $e) {
            return redirect()->route('locations.index', request()->query())->with('error', 'Error importing data: '.$e->getMessage());
        }
    }
}
