<?php

namespace LCFramework\Framework\Theme\Installer;

use Exception;
use Illuminate\Support\Arr;
use LCFramework\Framework\Installer\ComponentInstaller;
use LCFramework\Framework\Theme\Facade\Themes;

class ThemeInstaller extends ComponentInstaller implements ThemeInstallerInterface
{
    public function install(string $path): ?string
    {
        if (! ($zip = $this->getArchive($path))) {
            return null;
        }

        if (! ($index = $this->findManifestIndex($zip))) {
            return null;
        }

        if (! ($manifest = $this->getManifest($zip, $index))) {
            return null;
        }

        if (! $this->validate($manifest)) {
            return null;
        }

        $name = $manifest['name'];

        if (Themes::find($name) !== null) {
            return null;
        }

        $paths = config('lcframework.themes.paths');
        if (empty($paths)) {
            return null;
        }

        if(!$this->extract($zip, $name, Arr::first($paths))) {
            return null;
        }

        return $name;
    }

    protected function validate(array $manifest): bool
    {
        try {
            if (! isset($manifest['name'])) {
                return false;
            }

            if (! isset($manifest['extra'])) {
                return false;
            }

            if (! isset($manifest['extra']['lcframework'])) {
                return false;
            }

            return isset($manifest['extra']['lcframework']['theme']);
        } catch (Exception) {
            return false;
        }
    }
}
