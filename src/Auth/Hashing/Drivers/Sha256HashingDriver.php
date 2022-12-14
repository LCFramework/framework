<?php

namespace LCFramework\Framework\Auth\Hashing\Drivers;

use Illuminate\Contracts\Hashing\Hasher;

class Sha256HashingDriver implements Hasher
{
    public function info($hashedValue): array
    {
        return [];
    }

    public function make($value, array $options = []): string
    {
        $salt = config('lcframework.last_chaos.auth.salt');
        $username = $options['user_id'] ?? '';

        return hash(
            'sha256',
            $value.$salt.$username
        );
    }

    public function check($value, $hashedValue, array $options = []): bool
    {
        return $this->make($value, $options) === $hashedValue;
    }

    public function needsRehash($hashedValue, array $options = []): bool
    {
        return false;
    }
}
