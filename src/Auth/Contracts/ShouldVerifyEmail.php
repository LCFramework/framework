<?php

namespace LCFramework\Framework\Auth\Contracts;

use Illuminate\Contracts\Auth\MustVerifyEmail;

interface ShouldVerifyEmail extends MustVerifyEmail
{
    public function shouldVerifyEmail(): bool;
}
