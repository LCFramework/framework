<?php

namespace LCFramework\Framework\Theme\Installer;

use Exception;
use Illuminate\Support\Arr;
use LCFramework\Framework\Installer\ComponentInstaller;
use LCFramework\Framework\Theme\Facade\Themes;

class ThemeInstaller extends ComponentInstaller implements ThemeInstallerInterface
{
    public function install(string $path): bool
    {
        if (!($zip = $this->getArchive($path))) {
            return false;
        }

        if (!($index = $this->findManifestIndex($zip))) {
            return false;
        }

        if (!($manifest = $this->getManifest($zip, $index))) {
            return false;
        }

        if (!$this->validate($manifest)) {
            return false;
        }

        $name = $manifest['name'];

        if (Themes::find($name) !== null) {
            return false;
        }

        $paths = config('lcframework.themes.paths');
        if (empty($paths)) {
            return false;
        }

        $providers = (array)$manifest['extra']['lcframework']['theme']['providers'] ?? [];

        $this->publishAssets($providers);

        return $this->extract($zip, $name, Arr::first($paths));
    }

    protected function validate(array $manifest): bool
    {
        try {
            if (!isset($manifest['name'])) {
                return false;
            }

            if (!isset($manifest['extra'])) {
                return false;
            }

            if (!isset($manifest['extra']['lcframework'])) {
                return false;
            }

            return isset($manifest['extra']['lcframework']['theme']);
        } catch (Exception) {
            return false;
        }
    }
}
