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
use App\Notifications\UserCreatedNotification;
use Illuminate\Support\Facades\Notification;

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

        $geo = \App\Helpers\LocationHelper::geo();

        // 🔹 Step 3: Create User
        $user = User::create([
            'user_id'      => User::generateNextUserId(
                $validated['account_type'],
                $validated['country_code']
            ),
            'name'         => $validated['first_name'],
            'first_name'   => $validated['first_name'],
            'phone'        => $validated['phone'],
            'country_iso'  => $geo['country_code'] ?? null,
            'account_type' => $validated['account_type'],
            'email'        => $validated['email'],
            'password'     => Hash::make($validated['password']),
            'latitude'     => $validated['latitude'] ?? null,
            'longitude'    => $validated['longitude'] ?? null,
        ]);

        // Notify Admins and Managers
        $adminsAndManagers = User::whereIn('account_type', ['admin', 'manager'])->get();
        Notification::send($adminsAndManagers, new UserCreatedNotification($user));

        // 🔹 Step 4: Send Verification Email
        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            // Log error or show message if SMTP fails
            return redirect()->route('verification.notice')->with('error', 'Could not send verification email. Please check your SMTP settings.');
        }

        // 🔹 Step 5: Login & redirect
        Auth::login($user);

        return redirect()->route('verification.notice')->with('success', 'Registration successful. Please verify your email.');
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

        return $this->redirectByRole(auth()->user());
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::user()->update([
                'last_logout_at' => now()
            ]);
        }
        
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


    public function showSupportForm()
    {

        return view('auth.forms.supportTeam');
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
            'business_email'      => 'required|email|unique:users,email,' . Auth::id(),
            'address'             => 'required|string',
            'city'                => 'required|string',
            'state'               => 'required|string',
            'country'             => 'required|string',
            'post_code'           => 'required|string',
            'website'             => 'nullable|url',
            'social_media'        => 'nullable|url',
            'representative_name' => 'required|string',
            'dob'                 => 'required|date|before:-16 years',
            'id_type'             => 'required|string',
            'id_number'           => 'required|string',
            'designation'         => 'required|string',
            'whatsapp_number'     => 'required|string',
            'bank_name'           => 'required|string',
            'bank_country'        => 'required|string',
            'account_number'      => 'required|string',
            'registration_doc'    => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'id_doc'              => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'business_logo'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'dob.before' => 'Representative must be at least 16 years old.',
        ]);

        $data = $validated;
        // Do NOT set $data['user_id'] = Auth::id(); because it overwrites the custom user_id string
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

        // 🔑 Mark verification pending
        $data['kyc_status'] = 'pending';

        Auth::user()->update($data);

        // 🔑 Unique reference for Shufti
        $reference = 'user_' . Auth::id() . '_' . time();

        // 🔑 START Shufti verification (NOT result)
        $shuftiResponse = $this->startShuftiVerification(Auth::user(), $reference);

        if (!isset($shuftiResponse['verification_url'])) {

            $error = $shuftiResponse['error'] ?? null;

            Auth::user()->update([
                'shufti_status' => 'failed',
                'shufti_error'  => $error ? json_encode($error) : null
            ]);

            $errorMessage = is_array($error)
                ? ($error['message'] ?? 'Unknown error')
                : ($error ?? 'Unknown error');

            return back()->with(
                'error',
                'Shufti verification start nahi ho paayi: ' . $errorMessage
            );
        }

        // Save reference
        Auth::user()->update([
            'shufti_reference' => $reference,
            'shufti_status' => 'pending',
            'shufti_error' => null
        ]);

        // 🔥 Redirect user to Shufti
        return redirect($shuftiResponse['verification_url']);
    }

    /**
     * Store Student details
     */
    public function storeStudentDetails(Request $request)
    {



        $validated = $request->validate([
            'full_name'           => 'required|string|max:255',
            'dob'                 => 'required|date|before:-16 years',
            'id_type'             => 'required|string',
            'id_number'           => 'required|string',
            'primary_contact'     => 'required|string',
            'email'               => 'required|email|unique:users,email,' . Auth::id(),
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
        ], [
            'dob.before' => 'Student must be at least 16 years old.',
            'id_doc.max' => 'Document size may not exceed 5MB.',
            'id_doc_final.max' => 'Document size may not exceed 5MB.',
        ]);

        $data = $validated;
        // Do NOT set $data['user_id'] = Auth::id(); because it overwrites the custom user_id string

        // Handle File Uploads
        if ($request->hasFile('id_doc')) {
            $data['id_doc'] = $request->file('id_doc')->store('student_docs', 'public');
        }
        if ($request->hasFile('id_doc_final')) {
            // Overwrite or store as final
            $data['id_doc_final'] = $request->file('id_doc_final')->store('student_docs', 'public');
        }

        // 🔑 Mark verification pending
        $data['kyc_status'] = 'pending';

        Auth::user()->update($data);

        // 🔑 Unique reference for Shufti
        $reference = 'user_' . Auth::id() . '_' . time();

        // 🔑 START Shufti verification (NOT result)
        $shuftiResponse = $this->startShuftiVerification(Auth::user(), $reference);

        if (!isset($shuftiResponse['verification_url'])) {

            $error = $shuftiResponse['error'] ?? null;

            Auth::user()->update([
                'shufti_status' => 'failed',
                'shufti_error'  => $error ? json_encode($error) : null
            ]);

            $errorMessage = is_array($error)
                ? ($error['message'] ?? 'Unknown error')
                : ($error ?? 'Unknown error');

            return back()->with(
                'error',
                'Shufti verification start nahi ho paayi: ' . $errorMessage
            );
        }

        // Save reference
        Auth::user()->update([
            'shufti_reference' => $reference,
            'shufti_status' => 'pending',
            'shufti_error' => null
        ]);

        // 🔥 Redirect user to Shufti
        return redirect($shuftiResponse['verification_url']);
    }



  private function startShuftiVerification($user, $reference)
{
    $payload = [
        "reference" => $reference,
        "country" => $user->country ?? "GB",
        "language" => "EN",
        "email" => $user->email,

        // server callback
        "callback_url" => route('shufti.callback'),

        // 🔥 THIS IS IMPORTANT - user redirect back to your site
        "redirect_url" => route('shufti.redirect'),

        "verification_mode" => "any",

        "document" => [
            "supported_types" => ["passport", "id_card", "driving_license"]
        ],

        "face" => new \stdClass(),
    ];

    $response = Http::withBasicAuth(
        env('SHUFTI_CLIENT_ID'),
        env('SHUFTI_SECRET_KEY')
    )->post('https://api.shuftipro.com/', $payload);

    return $response->json();
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
        if ($user->account_type === 'student' && !$user->exam_purpose) {
            return redirect()->route('auth.form.student');
        }
        if (in_array($user->account_type, ['reseller_agent', 'agent']) && !$user->business_name) {
            return redirect()->route('auth.forms.B2BResellerAgent');
        }

        return redirect()->route('dashboard');
    }


    /**
     * Private helper for Shufti Pro Verification
     */
    // private function verifyWithShufti($email, $country, $name, $dob, $idNumber, $idDocPath)
    // {
    //     // 🔹 Mock Verification for Testing or if credentials are missing
    //     if (config('services.shuftipro.mock', true) || !config('services.shuftipro.client_id')) {
    //         $user = Auth::user();

    //         // Auto-approve in mock mode
    //         $user->update([
    //             'profile_verification_status' => 'verified',
    //             'verified_at' => now(),
    //             'shufti_status' => 'verified',
    //             'shufti_error' => null
    //         ]);

    //         try {
    //             Mail::to($user->email)->send(new \App\Mail\UserApproved($user));
    //         } catch (\Exception $e) {
    //             \Log::error("Mail Error during mock auto-approval: " . $e->getMessage());
    //         }

    //         return [
    //             'event' => 'verification.accepted',
    //             'status' => 'success',
    //             'reference' => 'MOCK_' . Auth::id() . '_' . time(),
    //             'message' => 'Verification accepted (Mock Mode)'
    //         ];
    //     }

    //     try {
    //         $filePath = storage_path('app/public/' . $idDocPath);
    //         if (!file_exists($filePath)) {
    //             return ['status' => 'failed', 'error' => 'ID document not found'];
    //         }

    //         $idDocBase64 = base64_encode(file_get_contents($filePath));
    //         $finfo = new \finfo(FILEINFO_MIME_TYPE);
    //         $mimeType = $finfo->file($filePath);

    //         $response = Http::withBasicAuth(
    //             config('services.shuftipro.client_id'),
    //             config('services.shuftipro.secret_key')
    //         )->post('https://api.shuftipro.com/', [
    //             'reference'         => 'UC_' . Auth::id() . '_' . time(),
    //             'country'           => $country,
    //             'email'             => $email,
    //             'verification_mode' => 'any',
    //             'document'          => [
    //                 'name'            => ['full_name' => $name],
    //                 'dob'             => $dob,
    //                 'document_number' => $idNumber,
    //                 'proof'           => 'data:' . $mimeType . ';base64,' . $idDocBase64,
    //                 'supported_types' => ['id_card', 'passport', 'driving_license'],
    //             ],
    //         ]);

    //         $result = $response->json();

    //         $user = Auth::user();
    //         // Auto-approve logic if verification is accepted
    //         if (isset($result['event']) && $result['event'] === 'verification.accepted') {
    //             $user->update([
    //                 'profile_verification_status' => 'verified',
    //                 'verified_at' => now(),
    //                 'shufti_status' => 'verified',
    //                 'shufti_error' => null
    //             ]);

    //             try {
    //                 Mail::to($user->email)->send(new \App\Mail\UserApproved($user));
    //             } catch (\Exception $e) {
    //                 \Log::error("Mail Error during auto-approval: " . $e->getMessage());
    //             }
    //         } elseif (isset($result['event']) && $result['event'] === 'verification.declined') {
    //             $user->update([
    //                 'shufti_status' => 'failed',
    //                 'shufti_error' => $result['declined_reason'] ?? 'Verification declined'
    //             ]);
    //         } elseif (isset($result['error'])) {
    //             $user->update([
    //                 'shufti_status' => 'failed',
    //                 'shufti_error' => $result['error']['message'] ?? 'API Error'
    //             ]);
    //         }
    //         return $result;
    //     } catch (\Exception $e) {
    //         Auth::user()->update([
    //             'shufti_status' => 'failed',
    //             'shufti_error' => $e->getMessage()
    //         ]);
    //         return ['status' => 'failed', 'error' => $e->getMessage()];
    //     }
    // }


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
