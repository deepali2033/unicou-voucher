<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vouchar;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $users = User::latest()->paginate(10);
            $stats = [
                'total_users' => User::count(),
                'agents' => User::where('account_type', 'reseller_agent')->count(),
                'students' => User::where('account_type', 'student')->count(),
                'pending_approvals' => User::where('profile_verification_status', 'pending')->count(),
            ];
            return view('dashboard.admin_dashboard', compact('users', 'stats'));
        }

        if ($user->isManager()) {
            $stats = [
                'total_users' => User::count(),
                'pending_approvals' => User::where('profile_verification_status', 'pending')->count(),
                'active_vouchers' => 1245,
                'low_stock_alerts' => 2,
            ];
            return view('dashboard.manager_dashboard', compact('stats'));
        }

        if ($user->isAgent()) {
            $currentTime = now()->timezone(session('user_timezone', 'UTC'))->format('l d F Y, h:i A');
            return view('dashboard.agent_dashboard', compact('currentTime'));
        }

        if ($user->isStudent()) {
            return view('dashboard.student_dashboard');
        }

        if ($user->isSupport()) {
            $stats = [
                'total_users' => User::count(),
                'pending_approvals' => User::where('profile_verification_status', 'pending')->count(),
                'students' => User::where('account_type', 'student')->count(),
            ];
            return view('dashboard.support_dashboard', compact('stats'));
        }

        return redirect('/');
    }

    public function notifications()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        auth()->user()->unreadNotifications->markAsRead();
        return view('dashboard.notifications.index', compact('notifications'));
    }

    public function stockAlerts()
    {
        $vouchers = Vouchar::where('stock', '<', 10)->get();
        $alerts = $vouchers->map(function ($v) {
            return [
                'type' => $v->name,
                'remaining' => $v->stock,
                'threshold' => 10
            ];
        });
        return view('dashboard.stock.alerts', compact('alerts'));
    }

    public function manageAccount()
    {
        $user = auth()->user();
        return view('dashboard.account.manage', compact('user'));
    }

    public function updateAccount(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only('first_name', 'last_name', 'email'));

        return back()->with('success', 'Account updated successfully.');
    }
}
