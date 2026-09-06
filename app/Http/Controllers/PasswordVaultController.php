<?php

namespace App\Http\Controllers;

use App\Models\PasswordVault;
use Illuminate\Http\Request;

class PasswordVaultController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new \Illuminate\Routing\Controllers\Middleware('permission:action_manage_settings', only: ['create', 'store', 'edit', 'update', 'destroy', 'importExcel', 'updateStatus', 'complete', 'generateTag', 'returnAsset', 'pingBatch', 'ping', 'updateAll']),
        ];
    }

    public function index(Request $request)
    {
        $totalPasswords = PasswordVault::count();

        // Count per category for ribbon tab counters
        $categoryCounts = PasswordVault::select('category', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $categories = collect(array_keys($categoryCounts))->filter()->sort()->values();
        $categoriesCount = $categories->count();

        // Infrastructure credentials count (Switch, Wifi, Router, CCTV, etc.)
        $networkInfraCount = PasswordVault::whereIn('category', ['Switch', 'Wifi', 'Router', 'Network', 'Firewall', 'CCTV'])->count();

        $summary = [
            'total_passwords' => $totalPasswords,
            'categories_count' => $categoriesCount,
            'network_infra_count' => $networkInfraCount,
            'category_counts' => $categoryCounts,
        ];

        $query = PasswordVault::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('device_name', 'like', "%$search%")
                    ->orWhere('username', 'like', "%$search%")
                    ->orWhere('category', 'like', "%$search%")
                    ->orWhere('notes', 'like', "%$search%");
            });
        }

        $activeCategory = $request->query('category', 'all');
        if (!empty($activeCategory) && $activeCategory !== 'all') {
            $query->where('category', $activeCategory);
        } else {
            $activeCategory = 'all';
        }

        $passwords = $query->orderBy('device_name')->get();

        return view('password_vaults.index', compact('passwords', 'categories', 'summary', 'activeCategory'));
    }

    public function create()
    {
        $categories = PasswordVault::select('category')->distinct()->pluck('category');

        return view('password_vaults.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'device_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'encrypted_password' => 'required|string',
            'category' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            PasswordVault::create([
                'device_name' => $request->device_name,
                'username' => $request->username,
                'encrypted_password' => $request->encrypted_password, // cast handles encryption
                'category' => $request->category,
                'notes' => $request->notes,
            ]);

            return redirect()->route('password_vaults.index', request()->query())->with('success', __('messages.created_success'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', __('messages.failed_to_save_vault').$e->getMessage());
        }
    }

    public function edit(PasswordVault $passwordVault)
    {
        $categories = PasswordVault::select('category')->distinct()->pluck('category');

        return view('password_vaults.edit', compact('passwordVault', 'categories'));
    }

    public function update(Request $request, PasswordVault $passwordVault)
    {
        $request->validate([
            'device_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'encrypted_password' => 'required|string',
            'category' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            $passwordVault->update([
                'device_name' => $request->device_name,
                'username' => $request->username,
                'encrypted_password' => $request->encrypted_password,
                'category' => $request->category,
                'notes' => $request->notes,
            ]);

            return redirect()->route('password_vaults.index', request()->query())->with('success', __('messages.updated_success'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', __('messages.failed_to_update_vault').$e->getMessage());
        }
    }

    public function destroy(PasswordVault $passwordVault)
    {
        $passwordVault->delete();

        return redirect()->route('password_vaults.index', request()->query())->with('success', __('messages.deleted_success'));
    }
}
