<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{

    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Show register form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    private function storeLocationInSession(Request $request)
    {
        try {
            $ip = $request->ip();
            if ($ip == '127.0.0.1' || $ip == '::1') {
                $ip = '103.255.4.42'; // Pakistan IP for local testing
            }

            // Using ipapi.co for all-in-one detection
            $response = Http::get("https://ipapi.co/{$ip}/json/");
            dd($response);
            if ($response->successful()) {
                $data = $response->json();

                // session([
                //     'user_country_code'   => $data['country_code'] ?? 'PK',
                //     'user_timezone'       => $data['timezone'] ?? 'Asia/Karachi',
                //     'user_currency'       => $data['currency'] ?? 'PKR',
                //     'user_currency_symbol' => $data['currency'] ?? 'PKR',
                //     'user_flag_url'       => "https://flagcdn.com/w320/" . strtolower($data['country_code'] ?? 'pk') . ".png",
                // ]);

                // Map symbols
                $symbols = ['PKR' => 'Rs.', 'INR' => '₹', 'BDT' => '৳', 'USD' => '$', 'AED' => 'DH'];
                if (isset($symbols[session('user_currency')])) {
                    session(['user_currency_symbol' => $symbols[session('user_currency')]]);
                }
            }
        } catch (\Exception $e) {
        }

        // Fallback
        // if (!session()->has('user_country_code')) {
        //     session([
        //         'user_country_code'   => 'PK',
        //         'user_timezone'       => 'Asia/Karachi',
        //         'user_currency'       => 'PKR',
        //         'user_currency_symbol' => 'Rs.',
        //         'user_flag_url'       => 'https://flagcdn.com/w320/pk.png',
        //     ]);
        // }
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'account_type' => ['required', 'in:user,recruiter,freelancer,manager,reseller_agent,support_team,student,admin'],
            'first_name'   => ['nullable', 'string', 'max:255'],
            'last_name'    => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'full_phone'   => ['required', 'string', 'max:20'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'     => ['required', 'confirmed', 'min:8'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | COUNTRY DETECTION (by phone prefix)
        |--------------------------------------------------------------------------
        */
        $countryCode = '+91';
        $countryIso  = 'IN';

        if (str_starts_with($validated['full_phone'], '+1')) {
            $countryCode = '+1';
            $countryIso  = 'US';
        } elseif (str_starts_with($validated['full_phone'], '+971')) {
            $countryCode = '+971';
            $countryIso  = 'AE';
        } elseif (str_starts_with($validated['full_phone'], '+91')) {
            $countryCode = '+91';
            $countryIso  = 'IN';
        }

        /*
        |--------------------------------------------------------------------------
        | Create User
        |--------------------------------------------------------------------------
        */
        $user = User::create([
            'first_name'   => $validated['first_name'] ?? null,
            'last_name'    => $validated['last_name'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'phone'        => $validated['full_phone'],
            'country_code' => $countryCode,
            'country_iso'  => $countryIso,
            'account_type' => $validated['account_type'],
            'email'        => $validated['email'],
            'password'     => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $this->storeLocationInSession($request);

        return $this->redirectByRole($user);
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $remember = (bool) ($credentials['remember'] ?? false);

        if (!Auth::attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            $remember
        )) {
            return back()->withErrors(['email' => 'Invalid credentials']);
        }

        $request->session()->regenerate();
        $this->storeLocationInSession($request);

        // Auto-verify email (testing only)
        if (!auth()->user()->hasVerifiedEmail()) {
            auth()->user()->email_verified_at = now();
            auth()->user()->save();
        }

        return $this->redirectByRole(auth()->user());
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Role-based redirect
     */
    private function redirectByRole(User $user)
    {
        return match ($user->account_type) {
            'reseller_agent' => redirect()->route('agent.dashboard'),
            'student'        => redirect()->route('student.dashboard'),
            'admin'          => redirect()->route('admin.dashboard'),
            default          => redirect('/'),
        };
    }
}
