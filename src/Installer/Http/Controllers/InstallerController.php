<?php

namespace LCFramework\Framework\Installer\Http\Controllers;

use Illuminate\Routing\Controller;
use LCFramework\Framework\LCFramework;

class InstallerController extends Controller
{
    public function show()
    {
        if (LCFramework::installed()) {
            return redirect()->intended();
        }

        return view('lcframework::installer.index');
    }
}
