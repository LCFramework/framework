<?php

namespace LCFramework\Framework\Support;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use LCFramework\Framework\Auth\Models\User;
use Spatie\Permission\Models\Role;

class Auth
{
    public static function login(
        array $credentials,
        bool $updatePasswordConfirmed = true
    ): void {
        $username = $credentials['email'] ?? $credentials['username'];

        if (! auth()->attempt([
            function ($query) use ($username) {
                $query->orWhere('email', '=', $username)
                    ->orWhere('user_id', '=', $username);
            },
            'password' => $credentials['password'],
        ], $credentials['remember'] ?? false)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid email address or password',
            ]);
        }

        if ($updatePasswordConfirmed) {
            session()->put('auth.password_confirmed_at', time());
        }
    }

    public static function register(
        array $credentials,
        bool $loginAfter = true,
        bool $silently = false
    ): User {
        $user = User::create([
            'username' => $credentials['username'],
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
        ]);

        $user->assignRole(Role::findById(1));

        if (! $silently) {
            event(new Registered($user));
        }

        if ($loginAfter) {
            auth()->login($user);
        }

        return $user;
    }
}
