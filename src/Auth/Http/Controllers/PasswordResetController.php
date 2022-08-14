<?php

namespace LCFramework\Framework\Auth\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class PasswordResetController extends Controller
{
    use ValidatesRequests;

    public function create()
    {
        return view('lcframework::auth.password-reset');
    }
}
