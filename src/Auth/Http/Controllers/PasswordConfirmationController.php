<?php

namespace LCFramework\Framework\Auth\Http\Controllers;

use Illuminate\Routing\Controller;

class PasswordConfirmationController extends Controller
{
    public function create()
    {
        return view('lcframework::auth.password-confirmation');
    }
}
