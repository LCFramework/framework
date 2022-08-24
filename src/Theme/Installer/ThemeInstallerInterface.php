<?php

namespace LCFramework\Framework\Theme\Installer;

interface ThemeInstallerInterface
{
    public function install(string $path): ?string;

    public function publishAssets(
        string $type,
        string $name,
        array $providers
    ): void;
}
