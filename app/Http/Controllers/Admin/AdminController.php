<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        ];

        return view('admin.dashboard', compact('users', 'stats'));
    }

    public function users()
    {
        return $this->dashboard();
    }

    public function viewUser(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return back()->with('success', 'User deleted successfully');
    }
}
