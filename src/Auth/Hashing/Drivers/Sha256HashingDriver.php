<?php

namespace LCFramework\Framework\Auth\Hashing\Drivers;

use Illuminate\Contracts\Hashing\Hasher;

class Sha256HashingDriver implements Hasher
{
    protected string $salt;

    public function __construct()
    {
        $this->salt = config('lcframework.last_chaos.auth.salt');
    }

    public function info($hashedValue): array
    {
        return [];
    }

    public function make($value, array $options = []): string
    {
        $username = $options['username'] ?? '';

        return hash(
            'sha256',
            $value.$this->salt.$username
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
