<?php

namespace LCFramework\Framework\Theme\Installer;

use Illuminate\Support\Arr;
use LCFramework\Framework\Installer\ComponentInstaller;
use LCFramework\Framework\Theme\Facade\Themes;

class ThemeInstaller extends ComponentInstaller implements ThemeInstallerInterface
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

        if (Themes::find($name) !== null) {
            return false;
        }

        $paths = config('lcframework.themes.paths');
        if (empty($paths)) {
            return false;
        }

        return $this->extract($zip, $name, Arr::first($paths));
    }
}
