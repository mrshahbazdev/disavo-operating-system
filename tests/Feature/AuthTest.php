<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domains\Tenancy\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
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
            ['email' => 'auth_test@disavo.de'],
            ['name' => 'Auth Test User', 'password' => Hash::make('secret123')]
        );

        $this->user->tenants()->syncWithoutDetaching([$this->tenant->id => ['role' => 'Owner']]);
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('System Sign In');
    }

    public function test_user_can_authenticate_and_redirect_to_dashboard(): void
    {
        $response = $this->post('/login', [
            'email'    => $this->user->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->user);
    }

    public function test_user_cannot_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'email'    => $this->user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Disavo Operating System');
        $response->assertSee($this->user->name);

    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_user_can_logout(): void
    {
        $response = $this->actingAs($this->user)->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
