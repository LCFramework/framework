<?php

namespace LCFramework\Framework\Installer\Http\Middleware;

use Closure;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\File;

class EnsureEnvFileExists
{
    public function handle($request, Closure $next)
    {
        if (!File::exists(base_path('.env'))) {
            File::put('.env', 'APP_KEY=' . $this->generateRandomKey());
        }

        return $next($request);
    }

    protected function generateRandomKey()
    {
        return 'base64:' . base64_encode(
                Encrypter::generateKey(config('app.cipher'))
            );
    }
}
