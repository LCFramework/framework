<?php

namespace LCFramework\Framework\Module\Installer;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use LCFramework\Framework\Module\Facade\Modules;
use ZipArchive;

class ModuleInstaller implements ModuleInstallerInterface
{
    public function install(string $path): bool
    {
        if (! ($zip = $this->getPackagedModule($path))) {
            return false;
        }

        if (! ($index = $this->findComposerIndex($zip))) {
            return false;
        }

        if (! ($name = $this->getModuleName($zip, $index))) {
            return false;
        }

        if (Modules::find($name) !== null) {
            return false;
        }

        $paths = config('lcframework.modules.paths');
        if (empty($paths)) {
            return false;
        }

        return $this->extractModule($zip, $name, $paths);
    }

    protected function extractModule(
        ZipArchive $zip,
        string $name,
        array $paths
    ): bool {
        try {
            $directory = $this->createDirectory($name, $paths);

            $zip->extractTo($directory);

            return true;
        } catch (Exception) {
            return false;
        }
    }

    protected function createDirectory(string $name, array $paths): string
    {
        $path = Arr::first($paths);

        $directory = $path.'/'.$name;

        File::ensureDirectoryExists($directory);

        return $directory;
    }

    protected function getModuleName(ZipArchive $zip, int $index): ?string
    {
        try {
            $composer = json_decode($zip->getFromIndex($index), true);

            return $composer['name'] ?? null;
        } catch (Exception) {
            return null;
        }
    }

    protected function findComposerIndex(ZipArchive $zip): ?int
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
