<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\UserApproved;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'pending_approvals' => User::where('profile_verification_status', 'pending')->count(),
            'active_vouchers' => 1245, // Placeholder
            'low_stock_alerts' => 2,    // Placeholder
        ];

        return view('manager.dashboard', compact('stats'));
    }

    public function auditTransactions()
    {
        return view('manager.audit.index');
    }

    public function manageUsers(Request $request)
    {
        $query = User::where('account_type', '!=', 'admin');
        $users = $query->latest()->paginate(15);
        return view('manager.users.index', compact('users'));
    }

    public function approveDetails(User $user)
    {
        return view('manager.approvals.show', compact('user'));
    }

    public function approveUser(User $user)
    {
        $user->update([
            'profile_verification_status' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id()
        ]);

        try {
            Mail::to($user->email)->send(new UserApproved($user));
        } catch (\Exception $e) {
            // Handle mail error
        }

        return redirect()->route('manager.users')->with('success', 'User approved successfully.');
    }

    public function voucherStock()
    {
        return view('manager.vouchers.stock');
    }

    public function disputes()
    {
        return view('manager.disputes.index');
    }

    public function addCredit(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:300',
        ]);

        // Logic to add store credit would go here
        // For now, we just simulate success
        
        return back()->with('success', 'USD ' . $request->amount . ' credit added to ' . $user->first_name . '\'s account.');
    }

    public function stopSystem()
    {
        return view('manager.system.stop');
    }

    public function reports()
    {
        return view('manager.reports.index');
    }
}
