<?php

namespace LCFramework\Framework\Theme\Installer;

use Exception;
use Illuminate\Support\Arr;
use LCFramework\Framework\Installer\Component\ComponentInstaller;
use LCFramework\Framework\Theme\Facade\Themes;

class ThemeInstaller extends ComponentInstaller implements ThemeInstallerInterface
{
    public function install(string $path, ?string &$reason = null): ?string
    {
        if (! ($zip = $this->getArchive($path))) {
            $reason = 'Failed to find uploaded theme';
            return null;
        }

        if (! ($index = $this->findManifestIndex($zip))) {
            $reason = 'Failed to find theme manifest';
            return null;
        }

        if (! ($manifest = $this->getManifest($zip, $index))) {
            $reason = 'Failed to get theme manifest';
            return null;
        }

        if (! $this->validate($manifest)) {
            $reason = 'Theme manifest is invalid';
            return null;
        }

        $name = $manifest['name'];

        if (Themes::find($name) !== null) {
            $reason = 'Theme is already installed';
            return null;
        }

        $paths = config('lcframework.themes.paths');
        if (empty($paths)) {
            $reason = 'No configured theme installation paths';
            return null;
        }

        if (!$this->extract($zip, $name, Arr::first($paths))) {
            $reason = 'Failed to extract theme';
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

            return isset($manifest['extra']['lcframework']['theme']);
        } catch (Exception) {
            return false;
        }
    }
}
