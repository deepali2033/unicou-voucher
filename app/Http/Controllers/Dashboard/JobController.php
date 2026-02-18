<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\JobVacancy;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class JobController extends Controller
{
    // List job vacancies for Admin/Manager
    public function index()
    {
        $vacancies = JobVacancy::latest()->get();
        return view('dashboard.jobs.index', compact('vacancies'));
    }

    // Create job vacancy form
    public function create()
    {
        return view('dashboard.jobs.create');
    }

    // Store job vacancy
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:manager,support_team',
            'country' => 'nullable|string|max:100',
        ]);

        JobVacancy::create($validated);
        return redirect()->route('jobs.index')->with('success', 'Job vacancy created successfully.');
    }

    // Delete vacancy
    public function destroy(JobVacancy $vacancy)
    {
        $vacancy->delete();
        return back()->with('success', 'Job vacancy deleted successfully.');
    }

    // List applications
    public function applications()
    {
        $applications = JobApplication::with('vacancy')->latest()->get();
        return view('dashboard.jobs.applications', compact('applications'));
    }

    // Update application status
    public function updateApplicationStatus(Request $request, JobApplication $application)
    {
        $request->validate(['status' => 'required|in:selected,rejected']);
        $application->update(['status' => $request->status]);

        if ($request->status === 'selected') {
            // Check if user already exists
            $user = User::where('email', $application->email)->first();
            if (!$user) {
                $password = Str::random(10);
                $user = User::create([
                    'name' => $application->name,
                    'email' => $application->email,
                    'phone' => $application->phone,
                    'password' => Hash::make($password),
                    'account_type' => $application->vacancy->category,
                    'is_active' => true,
                ]);

                // Send Email Notification
                try {
                    $message = "Congratulations! Your application for '{$application->vacancy->title}' has been SELECTED.\n\n" .
                        "Your account has been created on UniCou.\n" .
                        "Login Email: {$application->email}\n" .
                        "Temporary Password: {$password}\n\n" .
                        "Please login and reset your password immediately.\n" .
                        "Login URL: " . url('/login');

                    Mail::raw($message, function ($mail) use ($application) {
                        $mail->to($application->email)
                            ->subject('UniCou Job Selection - Account Created');
                    });
                } catch (\Exception $e) {
                    \Log::error("Failed to send job selection email: " . $e->getMessage());
                }
            }
        }

        return back()->with('success', 'Application status updated to ' . $request->status);
    }

    // Public list for careers
    public function careers()
    {
        $vacancies = JobVacancy::where('status', 'open')->latest()->get();
        return view('home.careers', compact('vacancies'));
    }

    // Application form
    public function applyForm(JobVacancy $vacancy)
    {
        return view('auth.forms.supportTeam', compact('vacancy'));
    }

    // Submit application
    public function submitApplication(Request $request)
    {
        $validated = $request->validate([
            'vacancy_id' => 'nullable|exists:job_vacancies,id',
            'name' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'social_link' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'post_code' => 'nullable|string|max:20',
            'reference_name' => 'nullable|string|max:255',
            'organization_name' => 'nullable|string|max:255',
            'reference_email' => 'nullable|email|max:255',
            'reference_phone' => 'nullable|string|max:20',
            'id_type' => 'nullable|string',
            'id_number' => 'nullable|string|max:50',
            'designation' => 'nullable|string',
            'bank_name' => 'nullable|string|max:255',
            'bank_country' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'id_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'photograph' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'reference_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',


        ]);

        // Handle File Uploads
        if ($request->hasFile('id_document')) {
            $validated['id_document'] = $request->file('id_document')->store('applications/ids', 'public');
        }
        if ($request->hasFile('photograph')) {
            $validated['photograph'] = $request->file('photograph')->store('applications/photos', 'public');
        }
        if ($request->hasFile('reference_letter')) {
            $validated['reference_letter'] = $request->file('reference_letter')->store('applications/references', 'public');
        }

        JobApplication::create($validated);
        return redirect()->route('home')->with('success', 'Your application has been submitted successfully.');
    }

    public function destroyApplication(JobApplication $application)
    {
        $application->delete();
        return back()->with('success', 'Application deleted successfully.');
    }
}
