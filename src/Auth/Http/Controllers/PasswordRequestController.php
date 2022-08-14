<?php

namespace LCFramework\Framework\Auth\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;

class PasswordRequestController extends Controller
{
    use ValidatesRequests;

    public function create()
    {
        return view('lcframework::auth.forgot-password');
    }

    public function store(Request $request)
    {
        $inputs = $request->validate([
            'email' => ['required', 'email']
        ]);

        $status = Password::sendResetLink($inputs);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()
            ->withInput($inputs)
            ->withErrors(['email' => __($status)]);
    }
}
