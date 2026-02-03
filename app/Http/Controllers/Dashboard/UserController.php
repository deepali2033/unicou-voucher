<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('riskLevel')
            ->where('account_type', '!=', 'admin');


        if ($request->has('role') && $request->role != 'all' && $request->role != '') {
            $query->where('account_type', $request->role);
        }

        if ($request->has('status') && $request->status != 'all' && $request->status != '') {
            $query->where('is_active', $request->status);
        }

        if ($request->has('verification_status') && $request->verification_status != 'all' && $request->verification_status != '') {
            $query->where('profile_verification_status', $request->verification_status);
        }

        if ($request->has('from_date') && $request->from_date != '') {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date != '') {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->has('search') && $request->search != '') {
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

        if ($request->ajax()) {
            return view('dashboard.partials.users-table', compact('users'))->render();
        }

        return view('dashboard.users.index', compact('users'));
    }

    public function create()
    {
        return view('dashboard.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'account_type' => 'required|in:manager,reseller_agent,support_team,student,agent',
            'phone' => 'nullable|string|max:20',
        ]);

        User::create([
            'user_id' => User::generateNextUserId($request->account_type),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'account_type' => $request->account_type,
            'phone' => $request->phone,
            'profile_verification_status' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        return redirect()->route('users.management')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('dashboard.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('dashboard.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
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

        return redirect()->route('users.management')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        if (request()->ajax()) {
            return response()->json(['success' => 'User deleted successfully']);
        }

        return redirect()->route('users.management')->with('success', 'User deleted successfully');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot freeze your own account'
            ], 403);
        }

        $user->is_active = $user->is_active ? 0 : 1;
        $user->save();

        return response()->json([
            'status'  => $user->is_active ? 'active' : 'frozen',
            'message' => $user->is_active
                ? 'User account unfrozen successfully'
                : 'User account frozen successfully'
        ]);
    }

    public function suspend(User $user)
    {
        $newStatus = $user->profile_verification_status === 'suspended' ? 'verified' : 'suspended';
        $user->update(['profile_verification_status' => $newStatus]);

        $message = $newStatus === 'suspended' ? 'User suspended successfully.' : 'User unsuspended successfully.';

        if (request()->ajax()) {
            return response()->json(['success' => $message]);
        }

        return back()->with('success', $message);
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function downloadPDF(Request $request)
    {
        $query = User::where('account_type', '!=', 'admin');

        if ($request->has('role') && $request->role != 'all' && $request->role != '') {
            $query->where('account_type', $request->role);
        }

        if ($request->has('status') && $request->status != 'all' && $request->status != '') {
            $query->where('is_active', $request->status);
        }

        if ($request->has('verification_status') && $request->verification_status != 'all' && $request->verification_status != '') {
            $query->where('profile_verification_status', $request->verification_status);
        }

        if ($request->has('from_date') && $request->from_date != '') {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date != '') {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('user_id', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->get();
        $filename = "users_export_" . date('Y-m-d_H-i-s') . ".csv";
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['User ID', 'Name', 'Email', 'Role', 'Phone', 'Status', 'Verification', 'Created At']);

        foreach ($users as $user) {
            fputcsv($handle, [
                $user->user_id,
                $user->first_name . ' ' . $user->last_name,
                $user->email,
                $user->account_type,
                $user->phone,
                $user->is_active ? 'Active' : 'Frozen',
                $user->profile_verification_status,
                $user->created_at->format('Y-m-d H:i')
            ]);
        }

        fclose($handle);
        exit;
    }
    public function profile()
    {
        $user = auth()->user();
        return view('dashboard.my-profile.index', compact('user'));
    }

    public function managers()
    {
        $users = User::where('account_type', 'manager')->latest()->paginate(10);
        return view('dashboard.pages.manager', compact('users'));
    }
    public function ResellerAgent()
    {
        $users = User::where('account_type', 'reseller_agent')->latest()->paginate(10);
        return view('dashboard.pages.reseller', compact('users'));
    }
    public function SupportTeam()
    {
        $users = User::where('account_type', 'support_team')->latest()->paginate(10);
        return view('dashboard.pages.support', compact('users'));
    }
    public function RegularAgent()
    {
        $users = User::where('account_type', 'agent')->latest()->paginate(10);
        return view('dashboard.pages.regularAgent', compact('users'));
    }
    public function Student()
    {
        $users = User::where('account_type', 'student')->latest()->paginate(10);
        return view('dashboard.pages.student', compact('users'));
    }
}
