<?php

namespace LCFramework\Framework\Module\Installer;

use Exception;
use Illuminate\Support\Arr;
use LCFramework\Framework\Installer\ComponentInstaller;
use LCFramework\Framework\Module\Facade\Modules;

class ModuleInstaller extends ComponentInstaller implements ModuleInstallerInterface
{
    public function install(string $path): ?string
    {
        if (!($zip = $this->getArchive($path))) {
            return null;
        }

        if (!($index = $this->findManifestIndex($zip))) {
            return null;
        }

        if (!($manifest = $this->getManifest($zip, $index))) {
            return null;
        }

        if (!$this->validate($manifest)) {
            return null;
        }

        $name = $manifest['name'];

        if (Modules::find($name) !== null) {
            return false;
        }

        $paths = config('lcframework.modules.paths');
        if (empty($paths)) {
            return null;
        }

        if (!$this->extract($zip, $name, Arr::first($paths))) {
            return null;
        }

        return $name;
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

            return isset($manifest['extra']['lcframework']['module']);
        } catch (Exception) {
            return false;
        }
    }
}
