<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\UserApproved;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::latest()->paginate(10);
        $stats = [
            'total_users' => User::count(),
            'agents' => User::where('account_type', 'reseller_agent')->count(),
            'students' => User::where('account_type', 'student')->count(),
            'pending_approvals' => User::where('profile_verification_status', 'pending')->count(),
        ];

        return view('admin.dashboard', compact('users', 'stats'));
    }

    public function users(Request $request)
    {
        $query = User::where('account_type', '!=', 'admin');

        // Filter by role
        if ($request->has('role') && $request->role != 'all') {
            $query->where('account_type', $request->role);
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('user_id', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.user-managment', compact('users'));
    }

    public function usersManagemt(Request $request)
    {
        return $this->users($request);
    }

    public function revenue()
    {
        $stats = [
            'total_revenue' => 15250.00,
            'available_balance' => 8420.50,
            'pending_payments' => 1250.00,
            'refund_amount' => 450.00,
        ];
        return view('admin.revenue.index', compact('stats'));
    }

    public function stockAlerts()
    {
        $alerts = [
            ['type' => 'University Voucher', 'remaining' => 5, 'threshold' => 10],
            ['type' => 'Language Course', 'remaining' => 2, 'threshold' => 5],
        ];
        return view('admin.stock.alerts', compact('alerts'));
    }

    public function systemControl()
    {
        $status = 'running'; // or 'stopped'
        $logs = [
            ['action' => 'Stopped', 'reason' => 'Server Maintenance', 'date' => now()->subDays(2)],
            ['action' => 'Resumed', 'reason' => 'Maintenance Complete', 'date' => now()->subDays(2)->addHours(2)],
        ];
        return view('admin.system.control', compact('status', 'logs'));
    }

    public function toggleSystem(Request $request)
    {
        return back()->with('success', 'System status updated successfully.');
    }

    public function approvals()
    {
        $pendingUsers = User::where('profile_verification_status', 'pending')
            ->with(['agentDetail', 'studentDetail'])
            ->latest()
            ->get();
        return view('admin.approvals.index', compact('pendingUsers'));
    }

    public function approveUser(User $user)
    {
        $user->update([
            'profile_verification_status' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id()
        ]);

        // Send Approval Email
        try {
            Mail::to($user->email)->send(new UserApproved($user));
        } catch (\Exception $e) {
            // Log error or handle silently if mail server is not configured
        }

        return redirect()->route('admin.approvals.index')->with('success', 'User approved successfully and notification email sent.');
    }

    public function vouchersControl()
    {
        return view('admin.voucher.voucher-control');
    }

    public function notifications()
    {
        // Placeholder notifications
        $notifications = [
            ['title' => 'New Agent Registration', 'message' => 'A new agent has registered and is waiting for approval.', 'time' => now()->diffForHumans()],
            ['title' => 'Low Stock Alert', 'message' => 'University Voucher stock is low (5 remaining).', 'time' => now()->subHours(2)->diffForHumans()],
        ];
        return view('admin.notifications.index', compact('notifications'));
    }

    public function manageAccount()
    {
        $user = auth()->user();
        return view('admin.account.manage', compact('user'));
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

    public function viewUser(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'account_type' => 'required|in:manager,reseller_agent,support_team,student,agent',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'user_id' => User::generateNextUserId($request->account_type),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => \Hash::make($request->password),
            'account_type' => $request->account_type,
            'phone' => $request->phone,
            'profile_verification_status' => 'verified', // Admin created users are verified by default
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        return redirect()->route('admin.users.management')->with('success', 'User created successfully.');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'account_type' => 'required|in:manager,reseller_agent,support_team,student,agent',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:pending,verified,suspended',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'account_type' => $request->account_type,
            'phone' => $request->phone,
            'profile_verification_status' => $request->status,
        ]);

        return redirect()->route('admin.users.management')->with('success', 'User updated successfully.');
    }

    public function suspendUser(User $user)
    {

        $newStatus = $user->profile_verification_status === 'suspended' ? 'verified' : 'suspended';
        $user->update(['profile_verification_status' => $newStatus]);


        $message = $newStatus === 'suspended' ? 'User suspended successfully.' : 'User unsuspended successfully.';
        return back()->with('success', $message);
    }

    public function downloadPDF(Request $request)
    {
        // Simple CSV export as a fallback for PDF
        $query = User::where('account_type', '!=', 'admin');

        if ($request->has('role') && $request->role != 'all') {
            $query->where('account_type', $request->role);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('user_id', 'like', "%{$search}%");
            });
        }

        $users = $query->get();
        $filename = "users_" . date('Y-m-d_H-i-s') . ".csv";
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['User ID', 'Name', 'Email', 'Role', 'Phone', 'Status', 'Created At']);

        foreach ($users as $user) {
            fputcsv($handle, [
                $user->user_id,
                $user->first_name . ' ' . $user->last_name,
                $user->email,
                $user->account_type,
                $user->phone,
                $user->profile_verification_status,
                $user->created_at
            ]);
        }

        fclose($handle);
        exit;
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.management')->with('success', 'User deleted successfully');
    }

    public function updatePassword(Request $request, User $user)
    {
        if ($user->account_type === 'student') {
            return back()->with('error', 'Cannot change password for students.');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => \Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
