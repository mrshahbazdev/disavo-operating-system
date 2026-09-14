<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Domains\Tenancy\Context\TenantContext;
use App\Domains\Tenancy\Models\Membership;
use App\Domains\Tenancy\Models\Tenant;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $tenant = $user->tenants()->first();
            if ($tenant) {
                TenantContext::setTenant($tenant);
                $request->session()->put('tenant_id', $tenant->id);
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'organization_name'     => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $orgName = !empty($validated['organization_name']) ? trim($validated['organization_name']) : ($validated['name'] . ' Organization');
        $slug = Str::slug($orgName);
        if (Tenant::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(5));
        }

        $tenant = Tenant::create([
            'name'      => $orgName,
            'slug'      => $slug,
            'is_active' => true,
        ]);

        Membership::create([
            'tenant_id' => $tenant->id,
            'user_id'   => $user->id,
            'role'      => Membership::ROLE_OWNER,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        TenantContext::setTenant($tenant);
        $request->session()->put('tenant_id', $tenant->id);

        return redirect()->route('dashboard')->with('success', "Welcome to Disavo OS, {$user->name}! Organization '{$tenant->name}' created.");
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
