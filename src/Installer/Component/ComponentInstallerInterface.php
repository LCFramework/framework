<?php

namespace LCFramework\Framework\Installer\Component;

interface ComponentInstallerInterface
{
    public function install(string $path, ?string &$reason = null): ?string;

    public function publish(array $providers): void;
}
