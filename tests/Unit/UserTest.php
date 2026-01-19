<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_freelancer_returns_true_for_freelancer(): void
    {
        $user = User::factory()->create(['account_type' => 'freelancer']);

        $this->assertTrue($user->isFreelancer());
    }

    public function test_is_freelancer_returns_false_for_user(): void
    {
        $user = User::factory()->create(['account_type' => 'user']);

        $this->assertFalse($user->isFreelancer());
    }

    public function test_is_recruiter_returns_true_for_recruiter(): void
    {
        $user = User::factory()->create(['account_type' => 'recruiter']);

        $this->assertTrue($user->isRecruiter());
    }

    public function test_is_admin_returns_true_for_admin(): void
    {
        $user = User::factory()->create(['account_type' => 'admin']);

        $this->assertTrue($user->isAdmin());
    }

    public function test_is_user_returns_true_for_user(): void
    {
        $user = User::factory()->create(['account_type' => 'user']);

        $this->assertTrue($user->isUser());
    }
}
