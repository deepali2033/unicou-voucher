<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{

    private function storeLocationInSession(Request $request)
    {
        try {
            $ip = $request->ip();
            // If local, use a dummy IP for testing (e.g., India) or just let it fall back
            if ($ip == '127.0.0.1' || $ip == '::1') {
                $ip = ''; // Let ipapi.co detect the server/public IP if possible, or fail
            }

            $response = Http::timeout(5)->get("https://ipapi.co/{$ip}/json/");
            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['error'])) {
                    throw new \Exception($data['reason'] ?? 'IP API Error');
                }

                $countryCode = $data['country_code'] ?? 'US';
                $flagUrl = "https://flagcdn.com/w40/" . strtolower($countryCode) . ".png";

                session([
                    'user_country_code' => $countryCode,
                    'user_country_name' => $data['country_name'] ?? 'United States',
                    'user_timezone'     => $data['timezone'] ?? 'UTC',
                    'user_currency'     => $data['currency'] ?? 'USD',
                    'user_city'         => $data['city'] ?? 'Unknown',
                    'user_flag'         => $flagUrl,
                ]);

                // Map symbols
                $symbols = ['PKR' => 'Rs.', 'INR' => '₹', 'BDT' => '৳', 'USD' => '$', 'AE' => 'DH'];
                if (isset($symbols[$data['currency'] ?? ''])) {
                    session(['user_currency_symbol' => $symbols[$data['currency']]]);
                }

                session()->forget('api_error');
            }
        } catch (\Exception $e) {
            session(['api_error' => 'Location detection failed. Defaulting to US.']);
            session([
                'user_country_code' => 'US',
                'user_country_name' => 'United States',
                'user_timezone'     => 'UTC',
                'user_currency'     => 'USD',
                'user_flag'         => 'https://flagcdn.com/w40/us.png',
            ]);
        }
    }

    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        return view('auth.register');
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
