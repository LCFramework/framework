<?php

namespace LCFramework\Framework\Module\Installer;

interface ModuleInstallerInterface
{
    public function install(string $path): ?string;

    public function publishAssets(array $providers): void;
}
