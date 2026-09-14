<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information (name & email).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        return redirect()->route('dashboard')->with(
            'success',
            "✓ Profil erfolgreich aktualisiert: {$user->name} ({$user->email})"
        );
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Das eingegebene aktuelle Passwort ist nicht korrekt.',
            'password.min'                      => 'Das neue Passwort muss mindestens 8 Zeichen lang sein.',
            'password.confirmed'                => 'Die Passwort-Bestätigung stimmt nicht überein.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('dashboard')->with(
            'success',
            '✓ Passwort wurde erfolgreich geändert.'
        );
    }
}
