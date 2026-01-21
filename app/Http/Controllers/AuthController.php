<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'confirm_password' => ['required', 'same:password'],
            'phone' => ['required', 'string'],
            'account_type' => ['required', 'in:reseller_agent,student'],
            'country_iso' => ['required', 'string', 'max:5'],
        ]);

        $countryIso = $request->country_iso;
        $type = $request->account_type;

        // Generate User ID
        // Agent: UN + ISO + 00171...
        // Student: UN + ISO + A + 00171...

        $count = User::where('account_type', $type)->count();
        $nextNumber = 171 + $count;
        $paddedNumber = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        $userId = 'UN' . $countryIso;
        if ($type === 'student') {
            $userId .= 'A';
        }
        $userId .= $paddedNumber;

        $user = User::create([
            'user_id' => $userId,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'account_type' => $type,
            'profile_verification_status' => 'pending',
        ]);

        Auth::login($user);

        return $this->redirectUser($user);
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return $this->redirectUser(Auth::user());
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    protected function redirectUser($user)
    {
        if ($user->account_type === 'reseller_agent') {
            return redirect()->route('agent.dashboard');
        } elseif ($user->account_type === 'student') {
            return redirect()->route('student.dashboard');
        }

        return redirect('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
