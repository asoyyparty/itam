<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new \Illuminate\Routing\Controllers\Middleware('permission:action_manage_settings', only: ['create', 'store', 'edit', 'update', 'destroy', 'importExcel', 'updateStatus', 'complete', 'generateTag', 'returnAsset', 'pingBatch', 'ping', 'updateAll']),
        ];
    }

    public function index()
    {
        // Group settings by their 'group' field
        $settings = Setting::all()->groupBy('group');

        return view('settings.index', compact('settings'));
    }

    public function updateAll(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user && ($user->hasRole('Super Admin') || $user->id === 1);

        $settings = $request->except(['_token', '_method', 'active_tab']);

        foreach ($settings as $key => $value) {
            // Protect AI settings from non-Super Admin users
            if (in_array($key, ['ai_provider', 'gemini_api_key', 'openai_api_key']) && !$isSuperAdmin) {
                continue;
            }

            Setting::where('key', $key)->update(['value' => $value ?? '']);
        }

        $activeTab = $request->input('active_tab');
        $queryParams = request()->query();
        if (!empty($activeTab)) {
            $queryParams['active_tab'] = $activeTab;
        }

        return redirect()->route('settings.index', $queryParams)->with('success', __('messages.updated_success') ?? 'Settings updated successfully.');
    }
}
