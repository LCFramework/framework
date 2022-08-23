<?php

namespace LCFramework\Framework\Module\Installer;

use ZipArchive;

class ModuleInstaller implements ModuleInstallerInterface
{
    public function install(string $path): bool
    {
        if (! ($zip = $this->getPackagedModule($path))) {
            return false;
        }

        if (! ($index = $this->validate($zip))) {
            return false;
        }

        $composer = $zip->getFromIndex($index);

        dd($composer);

        return true;
    }

    protected function validate(ZipArchive $zip): ?int
    {
        if (! ($index = $zip->locateName('composer.json', ZipArchive::FL_NODIR))) {
            return null;
        }

        return $index;
    }

    protected function getPackagedModule(string $path): ?ZipArchive
    {
        $zip = new ZipArchive();

        if (! $zip->open($path)) {
            return null;
        }

        return $zip;
    }
}
