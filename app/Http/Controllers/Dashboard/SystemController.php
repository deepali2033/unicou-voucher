<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\VoucherPriceRule;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function controlIndex()
    {
        $settings = SystemSetting::all()->pluck('settings_value', 'settings_key');
        $logs = AuditLog::where('action', 'like', 'System %')->latest()->limit(10)->get();
        
        // Get unique countries from users table
        $countries = User::whereNotNull('country_iso')
            ->where('country_iso', '!=', '')
            ->select('country_iso', 'country')
            ->distinct()
            ->get();

        // Get vouchers from VoucherPriceRule
        $vouchers = VoucherPriceRule::select('id', 'brand_name', 'voucher_variant', 'country_name')->get();

        return view('dashboard.system.control', compact('settings', 'logs', 'countries', 'vouchers'));
    }

    public function updateControl(Request $request)
    {
        if ($request->has('settings')) {
            foreach ($request->settings as $key => $value) {
                // Permission checks for managers
                if (auth()->user()->account_type === 'manager') {
                    if ($key === 'stop_system_sales' && !auth()->user()->can_stop_system_sales) continue;
                    if ($key === 'stop_country_sales' && !auth()->user()->can_stop_country_sales) continue;
                    if ($key === 'stop_voucher_sales' && !auth()->user()->can_stop_voucher_sales) continue;

                    // Maintenance mode and others only for admin
                    if (in_array($key, ['maintenance_mode', 'registration_enabled', 'min_topup', 'daily_order_limit', 'announcement'])) {
                        continue;
                    }
                }

                if (is_array($value)) {
                    $value = json_encode($value);
                }

                SystemSetting::updateOrCreate(
                    ['settings_key' => $key],
                    ['settings_value' => $value]
                );
            }
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'System Update',
            'description' => 'System settings updated',
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'System settings updated.');
    }

    public function auditLogs()
    {
        $logs = AuditLog::with('user')->latest()->paginate(10);
        return view('dashboard.audit.index', compact('logs'));
    }

    public function toggleSystem(Request $request)
    {
        if (auth()->user()->account_type === 'manager' && !auth()->user()->can_stop_system_sales) {
            return back()->with('error', 'Unauthorized action.');
        }

        $currentStatus = SystemSetting::where('settings_key', 'emergency_freeze')->first();
        $newStatus = ($currentStatus && $currentStatus->settings_value == 'on') ? 'off' : 'on';

        SystemSetting::updateOrCreate(
            ['settings_key' => 'emergency_freeze'],
            ['settings_value' => $newStatus]
        );

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'System Toggle',
            'description' => 'Emergency freeze toggled to ' . $newStatus,
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'System emergency status updated to ' . $newStatus);
    }
}
