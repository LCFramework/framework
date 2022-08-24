<?php

namespace LCFramework\Framework\Installer;

use Exception;
use Illuminate\Support\Facades\File;
use ZipArchive;

abstract class ComponentInstaller
{
    protected function extract(
        ZipArchive $zip,
        string $name,
        string $path
    ): bool {
        try {
            $directory = $this->createDirectory($name, $path);

            $zip->extractTo($directory);

            return true;
        } catch (Exception) {
            return false;
        }
    }

    protected function createDirectory(string $name, string $path): string
    {
        $directory = $path.'/'.$name;

        File::ensureDirectoryExists($directory);

        return $directory;
    }

    protected function getName(ZipArchive $zip, int $index): ?string
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

    protected function getArchive(string $path): ?ZipArchive
    {
        $zip = new ZipArchive();

        if (! $zip->open($path)) {
            return null;
        }

        return $zip;
    }
}
