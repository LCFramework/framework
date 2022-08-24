<?php

namespace LCFramework\Framework\Theme\Installer;

interface ThemeInstallerInterface
{
    public function install(string $path): ?string;

    public function publishAssets(array $providers): void;
}
