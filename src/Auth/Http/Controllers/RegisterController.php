<?php

namespace LCFramework\Framework\Auth\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use LCFramework\Framework\Auth\Models\User;

class RegisterController extends Controller
{
    use ValidatesRequests;

    public function create()
    {
        return view('lcframework::auth.register');
    }

    public function store(Request $request)
    {
        $inputs = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()]
        ]);

        $user = User::create([
            'username' => $inputs['username'],
            'email' => $inputs['email'],
            'password' => Hash::make($inputs['password'])
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect('/');
    }
}
