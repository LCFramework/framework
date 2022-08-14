<?php

namespace LCFramework\Framework\Auth\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use LCFramework\Framework\Auth\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    use ValidatesRequests;

    public function create()
    {
        return view('lcframework::auth.login');
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended();
    }
}
