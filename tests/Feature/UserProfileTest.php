<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use DatabaseTransactions;

    protected Tenant $tenant;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::firstOrCreate(
            ['slug' => 'disavo'],
            ['name' => 'Disavo Holding GmbH']
        );

        $this->user = User::firstOrCreate(
            ['email' => 'profile_tester@disavo.de'],
            ['name' => 'Profile Tester', 'password' => Hash::make('secret123')]
        );

        $this->user->tenants()->syncWithoutDetaching([$this->tenant->id => ['role' => 'Owner']]);
        TenantContext::setTenant($this->tenant);
    }

    protected function tearDown(): void
    {
        TenantContext::clear();
        parent::tearDown();
    }

    public function test_authenticated_user_can_update_profile_name_and_email(): void
    {
        $response = $this->actingAs($this->user)->post('/actions/profile/update', [
            'name'  => 'Alexander von Disavo',
            'email' => 'alexander@disavo.de',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('Alexander von Disavo', $this->user->name);
        $this->assertEquals('alexander@disavo.de', $this->user->email);
    }

    public function test_profile_update_allows_keeping_same_email(): void
    {
        $response = $this->actingAs($this->user)->post('/actions/profile/update', [
            'name'  => 'Updated Name Same Email',
            'email' => $this->user->email,
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('Updated Name Same Email', $this->user->name);
    }

    public function test_profile_update_fails_if_email_is_taken_by_another_user(): void
    {
        $otherUser = User::firstOrCreate(
            ['email' => 'other_user@disavo.de'],
            ['name' => 'Other User', 'password' => Hash::make('secret123')]
        );

        $response = $this->actingAs($this->user)->post('/actions/profile/update', [
            'name'  => 'Attempted Name Change',
            'email' => $otherUser->email,
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_authenticated_user_can_change_password_with_valid_current_password(): void
    {
        $response = $this->actingAs($this->user)->post('/actions/profile/password', [
            'current_password'      => 'secret123',
            'password'              => 'new_secure_password_99',
            'password_confirmation' => 'new_secure_password_99',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertTrue(Hash::check('new_secure_password_99', $this->user->password));
    }

    public function test_password_change_fails_with_invalid_current_password(): void
    {
        $response = $this->actingAs($this->user)->post('/actions/profile/password', [
            'current_password'      => 'incorrect_password',
            'password'              => 'new_secure_password_99',
            'password_confirmation' => 'new_secure_password_99',
        ]);

        $response->assertSessionHasErrors('current_password');

        $this->user->refresh();
        $this->assertTrue(Hash::check('secret123', $this->user->password));
    }

    public function test_password_change_fails_when_confirmation_does_not_match(): void
    {
        $response = $this->actingAs($this->user)->post('/actions/profile/password', [
            'current_password'      => 'secret123',
            'password'              => 'new_secure_password_99',
            'password_confirmation' => 'mismatching_confirmation',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_password_change_fails_if_password_is_under_eight_characters(): void
    {
        $response = $this->actingAs($this->user)->post('/actions/profile/password', [
            'current_password'      => 'secret123',
            'password'              => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_unauthenticated_user_cannot_access_profile_routes(): void
    {
        $resp1 = $this->post('/actions/profile/update', ['name' => 'Ghost', 'email' => 'ghost@ghost.com']);
        $resp1->assertRedirect('/login');

        $resp2 = $this->post('/actions/profile/password', [
            'current_password'      => 'secret123',
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $resp2->assertRedirect('/login');
    }
}
