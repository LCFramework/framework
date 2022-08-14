<?php

namespace LCFramework\Framework\Auth\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class PasswordRequestController extends Controller
{
    use ValidatesRequests;

    public function create()
    {
        return view('lcframework::auth.password-request');
    }
}
