<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function controlIndex()
    {
        $settings = SystemSetting::all()->pluck('settings_value', 'settings_key');
        $logs = AuditLog::where('action', 'like', 'System %')->latest()->limit(10)->get();
        return view('dashboard.system.control', compact('settings', 'logs'));
    }

    public function updateControl(Request $request)
    {
        foreach ($request->settings as $key => $value) {
            SystemSetting::updateOrCreate(
                ['settings_key' => $key],
                ['settings_value' => $value]
            );
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
        $logs = AuditLog::with('user')->latest()->paginate(20);
        return view('dashboard.audit.index', compact('logs'));
    }

    public function toggleSystem(Request $request)
    {
        return back()->with('success', 'System status updated successfully.');
    }
}
