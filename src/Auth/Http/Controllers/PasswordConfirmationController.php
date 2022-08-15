<?php

namespace LCFramework\Framework\Auth\Http\Controllers;

use Illuminate\Routing\Controller;

class PasswordConfirmationController extends Controller
{
    public function create()
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        return view('lcframework::auth.password-confirmation');
    }
}
