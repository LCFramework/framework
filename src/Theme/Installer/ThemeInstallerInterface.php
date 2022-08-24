<?php

namespace LCFramework\Framework\Theme\Installer;

interface ThemeInstallerInterface
{
    public function install(string $path): bool;
}
