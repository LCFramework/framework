<?php

namespace LCFramework\Framework\Module\Installer;

interface ModuleInstallerInterface
{
    public function install(string $path): bool;
}
