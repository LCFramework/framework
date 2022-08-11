<?php

namespace LCFramework\Framework\Setting\Drivers;

interface SettingsDriverInterface
{
    public function get(string $key, $default = null);

    public function put(string $key, $value): void;

    public function save(): void;

    public function forget(string $key): void;
}
