<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
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

        $settings = $request->except(['_token', '_method']);

        foreach ($settings as $key => $value) {
            // Protect AI settings from non-Super Admin users
            if (in_array($key, ['ai_provider', 'gemini_api_key', 'openai_api_key']) && !$isSuperAdmin) {
                continue;
            }

            Setting::where('key', $key)->update(['value' => $value ?? '']);
        }

        return redirect()->route('settings.index', request()->query())->with('success', __('messages.updated_success') ?? 'Settings updated successfully.');
    }
}
