<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vouchar;
use App\Models\InventoryVoucher;
use App\Models\Order;
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
                'total_vouchers' => InventoryVoucher::sum('quantity'),
                'total_sales' => Order::where('status', 'delivered')->count(),
                'total_revenue' => Order::where('status', 'delivered')->sum('amount'),
                'total_referral_points' => Order::sum('referral_points'),
                'total_bonus_points' => Order::sum('bonus_amount'),
            ];
            return view('dashboard.admin_dashboard', compact('users', 'stats'));
        }

        if ($user->isManager()) {
            $stats = [
                'total_users' => User::count(),
                'pending_approvals' => User::where('profile_verification_status', 'pending')->count(),
                'active_vouchers' => InventoryVoucher::sum('quantity'),
                'low_stock_alerts' => InventoryVoucher::where('quantity', '<', 10)->count(),
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
                'active_vouchers' => InventoryVoucher::sum('quantity'),
            ];
            return view('dashboard.support_dashboard', compact('stats'));
        }

        return redirect('/');
    }

    public function notifications()
    {
        $notifications = auth()->user()->notifications()->paginate(10);
        return view('dashboard.notifications.index', compact('notifications'));
    }

    public function markAllNotificationsAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    }

    public function notificationsBulkAction(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'No notifications selected.');
        }

        $notifications = auth()->user()->notifications()->whereIn('id', $ids);

        if ($action === 'read') {
            $notifications->update(['read_at' => now()]);
            $message = 'Selected notifications marked as read.';
        } elseif ($action === 'delete') {
            $notifications->delete();
            $message = 'Selected notifications deleted.';
        } else {
            return back()->with('error', 'Invalid action.');
        }

        return back()->with('success', $message);
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
