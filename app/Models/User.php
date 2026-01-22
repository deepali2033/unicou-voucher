<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'first_name',
        'last_name',
        'company_name',
        'phone',
        'account_type',
        'email',
        'password',
        'address',
        'city',
        'state',
        'zip_code',
        'work_experience',
        'education',
        'certifications',
        'profile_photo',
        'resume',
        'cover_letter',
        'aadhar_card',
        'pan_card',
        'profile_verification_status',
        'aadhaar_number',
        'aadhaar_document_path',
        'verification_documents',
        'verification_notes',
        'verified_at',
        'verified_by',
        'profile_updated_at',
        'pending_profile_data',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'verification_documents' => 'array',
            'verified_at' => 'datetime',
            'profile_updated_at' => 'datetime',
            'pending_profile_data' => 'array',
        ];
    }

    /**
     * Check if the user is a freelancer.
     *
     * @return bool
     */
    public function isFreelancer(): bool
    {
        return $this->account_type === 'freelancer';
    }

    /**
     * Check if the user is a recruiter.
     *
     * @return bool
     */
    public function isRecruiter(): bool
    {
        return $this->account_type === 'recruiter';
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->account_type === 'admin';
    }

    /**
     * Check if the user is a regular user.
     *
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->account_type === 'user';
    }

    /**
     * Check if the freelancer profile is verified.
     *
     * @return bool
     */
    public function isProfileVerified(): bool
    {
        return $this->profile_verification_status === 'verified';
    }

    /**
     * Check if the freelancer profile verification is pending.
     *
     * @return bool
     */
    public function isProfileVerificationPending(): bool
    {
        return $this->profile_verification_status === 'pending';
    }

    /**
     * Check if the freelancer profile verification is rejected.
     *
     * @return bool
     */
    public function isProfileVerificationRejected(): bool
    {
        return $this->profile_verification_status === 'rejected';
    }

    /**
     * Check if freelancer can apply for jobs.
     *
     * @return bool
     */
    public function canApplyForJobs(): bool
    {
        return $this->isFreelancer() && $this->isProfileVerified();
    }

    public function cleanerProfileSlug(): string
    {
        $nameSlug = Str::slug($this->name ?? 'freelancer');

        return 'cleaner-' . $this->id . ($nameSlug ? '-' . $nameSlug : '');
    }

    /**
     * Get the admin who verified this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get all users verified by this admin.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function verifiedUsers()
    {
        return $this->hasMany(User::class, 'verified_by');
    }

    /**
     * Mark profile as verified.
     *
     * @param User $admin
     * @param string|null $notes
     * @return bool
     */
    public function verifyProfile(User $admin, string $notes = null): bool
    {
        $this->profile_verification_status = 'verified';
        $this->verified_at = now();
        $this->verified_by = $admin->id;
        if ($notes) {
            $this->verification_notes = $notes;
        }

        // Apply pending profile data if any
        if ($this->pending_profile_data) {
            foreach ($this->pending_profile_data as $key => $value) {
                if (in_array($key, $this->fillable)) {
                    $this->$key = $value;
                }
            }
            $this->pending_profile_data = null;
        }

        return $this->save();
    }

    /**
     * Reject profile verification.
     *
     * @param User $admin
     * @param string $notes
     * @return bool
     */
    public function rejectProfile(User $admin, string $notes): bool
    {
        $this->profile_verification_status = 'rejected';
        $this->verified_by = $admin->id;
        $this->verification_notes = $notes;
        $this->pending_profile_data = null; // Clear pending data on rejection

        return $this->save();
    }

    /**
     * Submit profile for verification.
     *
     * @param array $profileData
     * @param array $documents
     * @return bool
     */
    public function submitProfileForVerification(array $profileData, array $documents = []): bool
    {
        $this->pending_profile_data = $profileData;
        $this->verification_documents = $documents;
        $this->profile_verification_status = 'pending';
        $this->profile_updated_at = now();

        return $this->save();
    }

    /**
     * Get job applications as freelancer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */


    /**
     * Get job applications as recruiter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */


    /**
     * Get job listings posted by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */


    /**
     * Get candidates (applications) submitted by this freelancer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */


    /**
     * Generate the next custom User ID based on country and account type.
     * Format: UC + Country ISO + (A if student) + 5-digit incrementing number starting from 00171
     */
    public static function generateNextUserId(string $accountType, string $countryCode = 'IN'): string
    {
        $countryCode = strtoupper($countryCode);
        $prefix = 'UC' . $countryCode;

        if ($accountType === 'student') {
            $prefix .= 'A';
        }

        // Find last user with this exact prefix
        // We use a regex match to ensure we only increment the numeric part at the end
        $lastUser = self::where('user_id', 'like', $prefix . '%')
            ->orderBy('user_id', 'desc')
            ->first();

        if ($lastUser && preg_match('/(\d+)$/', $lastUser->user_id, $matches)) {
            $lastNumber = (int)$matches[1];
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00171';
        }

        return $prefix . $newNumber;
    }

    /**
     * Get the plans booked by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
}
