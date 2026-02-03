<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AgentDetail;
use App\Models\StudentDetail;
use App\Helpers\LocationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

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
     * Show registration form
     */
    public function showRegister($type = null)
    {
        if ($type) {
            request()->merge(['type' => $type]);
        }
        return view('auth.register');
    }

    /**
     * Handle registration
     */

    public function register(Request $request)
    {
        // 🔹 Step 1: Validate form + captcha
        $validated = $request->validate([
            'account_type' => [
                'required',
                'in:agent,manager,reseller_agent,support_team,student,admin'
            ],
            'first_name'   => ['required', 'string', 'max:255'],
            'phone'        => ['required', 'string', 'max:20'],
            'country_code' => ['required', 'string', 'max:5'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'     => ['required', 'confirmed', 'min:8'],
            'g-recaptcha-response' => ['required'],
            'latitude'     => ['nullable', 'string'],
            'longitude'    => ['nullable', 'string'],
        ]);

        // 🔹 Step 2: Verify captcha with Google
        $captchaResponse = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret'   => env('RECAPTCHA_SECRET'),
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ]
        );

        if (! $captchaResponse->json('success')) {
            return back()
                ->withErrors(['captcha' => 'Captcha verification failed'])
                ->withInput();
        }

        // 🔹 Step 3: Create user
        $user = User::create([
            'user_id'      => User::generateNextUserId(
                $validated['account_type'],
                $validated['country_code']
            ),
            'name'         => $validated['first_name'],
            'first_name'   => $validated['first_name'],
            'phone'        => $validated['phone'],
            'country_iso'  => $validated['country_code'],
            'account_type' => $validated['account_type'],
            'email'        => $validated['email'],
            'password'     => Hash::make($validated['password']),
            'latitude'     => $validated['latitude'] ?? null,
            'longitude'    => $validated['longitude'] ?? null,
        ]);

        // 🔹 Step 4: Send Verification Email
        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            // Log error or show message if SMTP fails
            return redirect()->route('verification.notice')->with('error', 'Could not send verification email. Please check your SMTP settings.');
        }

        // 🔹 Step 5: Login & redirect to verification notice
        Auth::login($user);


        return redirect()->route('verification.notice');
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

        // 🔴 IMPORTANT: Freeze check
        if (auth()->user()->is_active == 0) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Your account is temporarily frozen by admin. Please contact support.'
            ]);
        }

        $request->session()->regenerate();

        // Update last login time
        auth()->user()->update([
            'last_login_at' => now()
        ]);

        if (!auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
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
     * Show Agent details form
     */
    public function showAgentForm()
    {
        if (Auth::user()->business_name) {
            return redirect()->route('dashboard');
        }
        return view('auth.forms.B2BResellerAgent');
    }

    /**
     * Show Student details form
     */
    public function showStudentForm()
    {
        if (Auth::user()->exam_purpose) {
            return redirect()->route('dashboard');
        }
        return view('auth.forms.student-admission-form');
    }

    /**
     * Store Agent details
     */
    public function storeAgentDetails(Request $request)
    {

        $validated = $request->validate([
            'agentType'           => 'required|string',
            'business_name'       => 'required|string|max:255',
            'business_type'       => 'required|string',
            'registration_number' => 'required|string',
            'business_contact'    => 'required|string',
            'business_email'      => 'required|email',
            'address'             => 'required|string',
            'city'                => 'required|string',
            'state'               => 'required|string',
            'country'             => 'required|string',
            'post_code'           => 'required|string',
            'website'             => 'nullable|url',
            'social_media'        => 'nullable|url',
            'representative_name' => 'required|string',
            'dob'                 => 'required|date',
            'id_type'             => 'required|string',
            'id_number'           => 'required|string',
            'designation'         => 'required|string',
            'whatsapp_number'     => 'required|string',
            'bank_name'           => 'required|string',
            'bank_country'        => 'required|string',
            'account_number'      => 'required|string',
            'registration_doc'    => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'id_doc'              => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'business_logo'       => 'nullable|image|max:2048',
        ]);

        $data = $validated;
        $data['user_id'] = Auth::id();
        $data['agent_type'] = $validated['agentType'];
        unset($data['agentType']);

        // Handle File Uploads
        if ($request->hasFile('registration_doc')) {
            $data['registration_doc'] = $request->file('registration_doc')->store('agent_docs', 'public');
        }
        if ($request->hasFile('id_doc')) {
            $data['id_doc'] = $request->file('id_doc')->store('agent_docs', 'public');
        }
        if ($request->hasFile('business_logo')) {
            $data['business_logo'] = $request->file('business_logo')->store('agent_logos', 'public');
        }

        // Shufti Pro Verification
        $shuftiResponse = $this->verifyWithShufti(
            Auth::user()->email,
            Auth::user()->country_iso,
            $validated['representative_name'],
            $validated['dob'],
            $validated['id_number'],
            $data['id_doc']
        );

        if (isset($shuftiResponse['error']) || (isset($shuftiResponse['event']) && $shuftiResponse['event'] === 'verification.declined')) {
            $errorMsg = $shuftiResponse['error'] ?? ($shuftiResponse['declined_reason'] ?? 'Verification declined by third party.');
            return back()->withInput()->with('error', 'Verification Issue: ' . $errorMsg);
        }

        // Store Shufti reference/status if needed
        $data['shufti_reference'] = $shuftiResponse['reference'] ?? null;

        Auth::user()->update($data);

        return redirect()->route('dashboard')->with('success', 'Profile completed and verified successfully.');
    }

    /**
     * Store Student details
     */
    public function storeStudentDetails(Request $request)
    {
        $validated = $request->validate([
            'full_name'           => 'required|string|max:255',
            'dob'                 => 'required|date',
            'id_type'             => 'required|string',
            'id_number'           => 'required|string',
            'primary_contact'     => 'required|string',
            'email'               => 'required|email',
            'whatsapp_number'     => 'required|string',
            'address'             => 'required|string',
            'city'                => 'required|string',
            'state'               => 'required|string',
            'country'             => 'required|string',
            'post_code'           => 'required|string',
            'id_doc'              => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'exam_purpose'        => 'required|string',
            'highest_education'   => 'required|string',
            'passing_year'        => 'required|numeric',
            'preferred_countries' => 'nullable|array',
            'bank_name'           => 'required|string',
            'bank_country'        => 'required|string',
            'account_number'      => 'required|string',
            'id_doc_final'        => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $validated;
        $data['user_id'] = Auth::id();

        // Handle File Uploads
        if ($request->hasFile('id_doc')) {
            $data['id_doc'] = $request->file('id_doc')->store('student_docs', 'public');
        }
        if ($request->hasFile('id_doc_final')) {
            // Overwrite or store as final
            $data['id_doc_final'] = $request->file('id_doc_final')->store('student_docs', 'public');
        }

        // Shufti Pro Verification
        $shuftiResponse = $this->verifyWithShufti(
            Auth::user()->email,
            Auth::user()->country_iso,
            $validated['full_name'],
            $validated['dob'],
            $validated['id_number'],
            $data['id_doc']
        );

        if (isset($shuftiResponse['error']) || (isset($shuftiResponse['event']) && $shuftiResponse['event'] === 'verification.declined')) {
            $errorMsg = $shuftiResponse['error'] ?? ($shuftiResponse['declined_reason'] ?? 'Verification declined by third party.');
            return back()->withInput()->with('error', 'Verification Issue: ' . $errorMsg);
        }

        $data['shufti_reference'] = $shuftiResponse['reference'] ?? null;

        Auth::user()->update($data);

        return redirect()->route('dashboard')->with('success', 'Profile completed and verified successfully.');
    }

    /**
     * Private helper for Shufti Pro Verification
     */
    private function verifyWithShufti($email, $country, $name, $dob, $idNumber, $idDocPath)
    {
        // 🔹 Mock Verification for Testing or if credentials are missing
        if (config('services.shuftipro.mock', true) || !config('services.shuftipro.client_id')) {
            $user = Auth::user();

            // Auto-approve in mock mode
            $user->update([
                'profile_verification_status' => 'verified',
                'verified_at' => now(),
            ]);

            try {
                Mail::to($user->email)->send(new \App\Mail\UserApproved($user));
            } catch (\Exception $e) {
                \Log::error("Mail Error during mock auto-approval: " . $e->getMessage());
            }

            return [
                'event' => 'verification.accepted',
                'status' => 'success',
                'reference' => 'MOCK_' . Auth::id() . '_' . time(),
                'message' => 'Verification accepted (Mock Mode)'
            ];
        }

        try {
            $filePath = storage_path('app/public/' . $idDocPath);
            if (!file_exists($filePath)) {
                return ['status' => 'failed', 'error' => 'ID document not found'];
            }

            $idDocBase64 = base64_encode(file_get_contents($filePath));
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($filePath);

            $response = Http::withBasicAuth(
                config('services.shuftipro.client_id'),
                config('services.shuftipro.secret_key')
            )->post('https://api.shuftipro.com/', [
                'reference'         => 'UC_' . Auth::id() . '_' . time(),
                'country'           => $country,
                'email'             => $email,
                'verification_mode' => 'any',
                'document'          => [
                    'name'            => ['full_name' => $name],
                    'dob'             => $dob,
                    'document_number' => $idNumber,
                    'proof'           => 'data:' . $mimeType . ';base64,' . $idDocBase64,
                    'supported_types' => ['id_card', 'passport', 'driving_license'],
                ],
            ]);

            $result = $response->json();

            // Auto-approve logic if verification is accepted
            if (isset($result['event']) && $result['event'] === 'verification.accepted') {
                $user = Auth::user();
                $user->update([
                    'profile_verification_status' => 'verified',
                    'verified_at' => now(),
                ]);

                try {
                    Mail::to($user->email)->send(new \App\Mail\UserApproved($user));
                } catch (\Exception $e) {
                    \Log::error("Mail Error during auto-approval: " . $e->getMessage());
                }
            }

            return $result;
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    public function test()
    {
        $response = Http::withBasicAuth(

            config('services.shuftipro.client_id'),
            config('services.shuftipro.secret_key')
        )->post('https://api.shuftipro.com/', [
            'reference' => 'test_' . uniqid(),
            'country'   => 'IN',
            'email'     => 'test@example.com',
            'language'  => 'EN',
            'document'  => [
                'proof' => 'id_card'
            ],
        ]);

        return response()->json($response->json());
        // ya debugging ke liye:
        // dd($response->json());
    }

    public function resendVerification(Request $request)
    {
        try {
            $request->user()->sendEmailVerificationNotification();
            return back()->with('message', 'Verification link sent!');
        } catch (\Exception $e) {
            \Log::error("Mail Error: " . $e->getMessage());
            return back()->with('error', 'Mail Error: ' . $e->getMessage());
        }
    }

    /**
     * Role-based redirect
     */
    private function redirectByRole(User $user)
    {
        if ($user->account_type === 'student' && !$user->studentDetail) {
            return redirect()->route('auth.form.student');
        }
        if ($user->account_type === 'reseller_agent' && !$user->agentDetail) {
            return redirect()->route('auth.forms.B2BResellerAgent');
        }

        return redirect()->route('dashboard');
    }


    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);

        $user->is_active = !$user->is_active;

        if ($user->is_active == 0) {
            $user->frozen_at = now();
        } else {
            $user->frozen_at = null;
        }

        $user->save();

        return back()->with('success', 'User status updated successfully');
    }
}
