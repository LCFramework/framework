<?php

namespace LCFramework\Framework\Installer\Http\Controllers;

use Illuminate\Routing\Controller;

class InstallerController extends Controller
{
    public function show()
    {
        return view('lcframework::installer.index');
    }
}
