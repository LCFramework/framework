<?php

namespace LCFramework\Framework\Auth\Hashing;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Manager;
use LCFramework\Framework\Auth\Hashing\Drivers\PlainTextHashingDriver;
use LCFramework\Framework\Auth\Hashing\Drivers\Sha256HashingDriver;

class HashingManager extends Manager implements Hasher
{
    public function getDefaultDriver(): string
    {
        $version = $this->config->get('lcframework.last_chaos.version');

        return match ($version) {
            4 => 'sha256',
            default => 'plainText',
        };
    }

    public function createSha256Driver(): Sha256HashingDriver
    {
        return $this->container->make(Sha256HashingDriver::class);
    }

    public function createPlainTextDriver(): PlainTextHashingDriver
    {
        return $this->container->make(PlainTextHashingDriver::class);
    }

    public function info($hashedValue): array
    {
        return $this->driver()->info($hashedValue);
    }

    public function make($value, array $options = []): string
    {
        return $this->driver()->make($value, $options);
    }

    public function check($value, $hashedValue, array $options = []): bool
    {
        return $this->driver()->check($value, $hashedValue, $options);
    }

    public function needsRehash($hashedValue, array $options = []): bool
    {
        return $this->driver()->needsRehash($hashedValue, $options);
    }
}
