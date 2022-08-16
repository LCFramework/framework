<?php

namespace LCFramework\Framework\Auth\Hashing\Drivers;

use Illuminate\Contracts\Hashing\Hasher;

class PlainTextHashingDriver implements Hasher
{
    public function info($hashedValue): array
    {
        return [];
    }

    public function make($value, array $options = []): string
    {
        return $value;
    }

    public function check($value, $hashedValue, array $options = []): bool
    {
        return $value === $hashedValue;
    }

    public function needsRehash($hashedValue, array $options = []): bool
    {
        return false;
    }
}
