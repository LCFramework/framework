<?php

namespace LCFramework\Framework\Module\Installer;

use Illuminate\Support\Arr;
use LCFramework\Framework\Installer\ComponentInstaller;
use LCFramework\Framework\Module\Facade\Modules;

class ModuleInstaller extends ComponentInstaller implements ModuleInstallerInterface
{
    public function install(string $path): bool
    {
        if (! ($zip = $this->getArchive($path))) {
            return false;
        }

        if (! ($index = $this->findComposerIndex($zip))) {
            return false;
        }

        if (! ($name = $this->getName($zip, $index))) {
            return false;
        }

        if (Modules::find($name) !== null) {
            return false;
        }

        $paths = config('lcframework.modules.paths');
        if (empty($paths)) {
            return false;
        }

        return $this->extract($zip, $name, Arr::first($paths));
    }
}
