<?php

namespace LCFramework\Framework\Auth\Contracts;

use Illuminate\Contracts\Auth\MustVerifyEmail;

interface OptionallyVerifyEmail extends MustVerifyEmail
{
    public function shouldVerifyEmail(): bool;
}
