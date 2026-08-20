<?php

namespace App\Http\Controllers;

use App\Exports\BrandExport;
use App\Imports\BrandImport;
use App\Models\Brand;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BrandController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new \Illuminate\Routing\Controllers\Middleware('permission:action_manage_master_data', only: ['create', 'store', 'edit', 'update', 'destroy', 'importExcel', 'updateStatus', 'complete', 'generateTag', 'returnAsset', 'pingBatch', 'ping', 'updateAll']),
        ];
    }

    public function index(Request $request)
    {
        $query = Brand::query();
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }
        $brands = $query->orderBy('name')->get();

        return view('master.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('master.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands',
            'description' => 'nullable|string',
        ]);

        Brand::create($request->all());
        \Illuminate\Support\Facades\Cache::forget('master_brands_all');

        return redirect()->route('brands.index', request()->query())->with('success', __('messages.created_success'));
    }

    public function edit(Brand $brand)
    {
        return view('master.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,'.$brand->id,
            'description' => 'nullable|string',
        ]);

        $brand->update($request->all());
        \Illuminate\Support\Facades\Cache::forget('master_brands_all');

        return redirect()->route('brands.index', request()->query())->with('success', __('messages.updated_success'));
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        \Illuminate\Support\Facades\Cache::forget('master_brands_all');

        return redirect()->route('brands.index', request()->query())->with('success', __('messages.deleted_success'));
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new BrandExport($request), 'brands_' . date('Ymd_His') . '.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new BrandImport, $request->file('file'));
            \Illuminate\Support\Facades\Cache::forget('master_brands_all');

            return redirect()->route('brands.index', request()->query())->with('success', 'Data imported successfully.');
        } catch (\Exception $e) {
            return redirect()->route('brands.index', request()->query())->with('error', 'Error importing data: '.$e->getMessage());
        }
    }
}
