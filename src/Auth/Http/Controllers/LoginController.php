<?php

namespace LCFramework\Framework\Auth\Http\Controllers;

use Illuminate\Routing\Controller;

class LoginController extends Controller
{
    public function create()
    {
        return view('lcframework::auth.login');
    }
}
