<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        // BUG-06: Use Laravel's authorize() consistently instead of custom private method
        $this->authorize('viewAny', \App\Models\User::class); // Only ADMIN can access settings

        $settings = SystemSetting::all()->keyBy('key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $this->authorize('viewAny', \App\Models\User::class); // Only ADMIN

        $validated = $request->validate([
            'system_name'              => 'nullable|string|max:100',
            'organization'             => 'nullable|string|max:100',
            'timezone'                 => 'nullable|string|max:50',
            'date_format'              => 'nullable|string|max:30',
            'low_stock_threshold'      => 'nullable|integer|min:0|max:100',
            'critical_stock_threshold' => 'nullable|integer|min:0|max:100',
            'expiry_warning_days'      => 'nullable|integer|min:1|max:365',
            'session_timeout'          => 'nullable|integer|min:5|max:1440',
            'low_stock_alert'          => 'nullable',
            'expiry_alert'             => 'nullable',
            'critical_stock_alert'     => 'nullable',
            'email_notification'       => 'nullable',
            'two_factor_auth'          => 'nullable',
        ]);

        $booleans = ['low_stock_alert', 'expiry_alert', 'critical_stock_alert', 'email_notification', 'two_factor_auth'];

        $old = [];
        foreach ($validated as $key => $value) {
            $setting = SystemSetting::where('key', $key)->first();
            if ($setting) {
                $old[$key] = $setting->value;
            }
        }

        foreach ($validated as $key => $value) {
            if (in_array($key, $booleans)) {
                SystemSetting::setValue($key, isset($validated[$key]) ? '1' : '0');
            } else {
                SystemSetting::setValue($key, $value ?? '');
            }
        }

        // Handle unchecked booleans (not present in request)
        foreach ($booleans as $bool) {
            if (!array_key_exists($bool, $validated)) {
                SystemSetting::setValue($bool, '0');
            }
        }

        AuditLogService::logUpdated('Settings', 0, $old, $validated);

        return back()->with('success', 'System settings have been updated successfully.');
    }
}
