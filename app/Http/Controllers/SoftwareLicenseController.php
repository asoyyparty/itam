<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SoftwareLicense;
use Illuminate\Http\Request;

class SoftwareLicenseController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new \Illuminate\Routing\Controllers\Middleware('permission:action_manage_settings', only: ['create', 'store', 'edit', 'update', 'destroy', 'importExcel', 'updateStatus', 'complete', 'generateTag', 'returnAsset', 'pingBatch', 'ping', 'updateAll']),
        ];
    }

    public function index(Request $request)
    {
        $now = now()->startOfDay();
        $in30Days = now()->addDays(30)->endOfDay();

        // Calculate KPI Summary from all licenses
        $allLicenses = SoftwareLicense::all();
        $totalLicenses = $allLicenses->count();
        $totalSeats = (int) $allLicenses->sum('total_seats');

        $expiringSoonCount = $allLicenses->filter(function ($lic) use ($now, $in30Days) {
            return $lic->expiry_date && $lic->expiry_date >= $now && $lic->expiry_date <= $in30Days;
        })->count();

        $expiredCount = $allLicenses->filter(function ($lic) use ($now) {
            return $lic->expiry_date && $lic->expiry_date < $now;
        })->count();

        $lifetimeCount = $allLicenses->filter(function ($lic) {
            return is_null($lic->expiry_date);
        })->count();

        $activeCount = $allLicenses->filter(function ($lic) use ($now) {
            return is_null($lic->expiry_date) || $lic->expiry_date >= $now;
        })->count();

        $summary = [
            'total_licenses' => $totalLicenses,
            'total_seats' => $totalSeats,
            'active_count' => $activeCount,
            'expiring_soon' => $expiringSoonCount,
            'expired_count' => $expiredCount,
            'lifetime_count' => $lifetimeCount,
        ];

        $statusFilter = $request->get('status', 'all');

        $query = SoftwareLicense::with('pic');

        if ($statusFilter === 'active') {
            $query->where(function ($q) use ($now) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', $now);
            });
        } elseif ($statusFilter === 'expiring_soon') {
            $query->whereNotNull('expiry_date')
                  ->whereBetween('expiry_date', [$now, $in30Days]);
        } elseif ($statusFilter === 'expired') {
            $query->whereNotNull('expiry_date')
                  ->where('expiry_date', '<', $now);
        } elseif ($statusFilter === 'lifetime') {
            $query->whereNull('expiry_date');
        }

        if ($request->has('pic_id') && $request->pic_id != '') {
            $query->where('pic_id', $request->pic_id);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('license_key', 'like', "%$search%");
            });
        }

        $software_licenses = $query->orderBy('name')->get();
        $employees = Employee::orderBy('name')->get();

        return view('software_licenses.index', compact('software_licenses', 'employees', 'summary', 'statusFilter'));
    }

    public function create()
    {
        $employees = Employee::orderBy('name')->get();

        return view('software_licenses.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'license_key' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'total_seats' => 'required|integer|min:1',
            'pic_id' => 'nullable|exists:employees,id',
            'notes' => 'nullable|string',
        ]);

        try {
            SoftwareLicense::create($request->all());

            return redirect()->route('software_licenses.index', request()->query())->with('success', __('messages.created_success'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create Software License: '.$e->getMessage());
        }
    }

    public function edit(SoftwareLicense $softwareLicense)
    {
        $employees = Employee::orderBy('name')->get();

        return view('software_licenses.edit', compact('softwareLicense', 'employees'));
    }

    public function update(Request $request, SoftwareLicense $softwareLicense)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'license_key' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'total_seats' => 'required|integer|min:1',
            'pic_id' => 'nullable|exists:employees,id',
            'notes' => 'nullable|string',
        ]);

        try {
            $softwareLicense->update($request->all());

            return redirect()->route('software_licenses.index', request()->query())->with('success', __('messages.updated_success'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update Software License: '.$e->getMessage());
        }
    }

    public function destroy(SoftwareLicense $softwareLicense)
    {
        $softwareLicense->delete();

        return redirect()->route('software_licenses.index', request()->query())->with('success', __('messages.deleted_success'));
    }
}
