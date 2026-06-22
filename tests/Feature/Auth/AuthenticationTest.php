<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_active_staff_can_login_and_login_is_audited(): void
    {
        $user = User::factory()->create(['email' => 'admin@example.com', 'password' => 'Secret123!']);
        $user->assignRole('Admin');

        $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'Secret123!',
            'device_name' => 'Postman',
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.roles.0', 'Admin')
            ->assertJsonStructure(['data' => ['token', 'user' => ['permissions']]]);

        $this->assertNotNull($user->refresh()->last_login_at);
        $this->assertDatabaseHas('activity_log', ['description' => 'User logged in']);
    }

    public function test_inactive_staff_cannot_login(): void
    {
        User::factory()->create(['email' => 'disabled@example.com', 'password' => 'Secret123!', 'is_active' => false]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'disabled@example.com', 'password' => 'Secret123!',
        ])->assertForbidden()->assertJsonPath('success', false);
    }

    public function test_authenticated_staff_can_read_profile_and_change_password(): void
    {
        $user = User::factory()->create(['password' => 'OldSecret123!']);
        $user->assignRole('Visa Consultant');
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/auth/profile')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.roles.0', 'Visa Consultant');

        $this->putJson('/api/v1/auth/password', [
            'current_password' => 'OldSecret123!',
            'password' => 'NewSecret456!',
            'password_confirmation' => 'NewSecret456!',
        ])->assertOk();

        $this->assertTrue(Hash::check('NewSecret456!', $user->refresh()->password));
    }

    public function test_logout_revokes_the_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('Postman');

        $this->withToken($token->plainTextToken)->postJson('/api/v1/auth/logout')->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
