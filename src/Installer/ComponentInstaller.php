<?php

namespace LCFramework\Framework\Installer;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use ZipArchive;

abstract class ComponentInstaller
{
    abstract protected function validate(array $manifest): bool;

    public function publish(array $providers): void
    {
        try {
            $this->publishAssets($providers);
            Artisan::call('migrate');
        } catch (Exception) {
        }
    }

    protected function publishAssets(array $providers): void
    {
        foreach ($providers as $provider) {
            if (! method_exists($provider, 'publishAssets')) {
                continue;
            }

            $providerInstance = app($provider, [
                'app' => app(),
            ]);

            app()->call([$providerInstance, 'publishAssets']);

            if (! method_exists($provider, 'getPublishableTags')) {
                continue;
            }

            $tags = (array) app()->call([$providerInstance, 'getPublishableTags']) ?? [];

            foreach ($tags as $tag => $force) {
                $tag = is_string($tag) ? $tag : $force;
                $force = is_bool($force) ? $force : true;

                Artisan::call('vendor:publish', [
                    '--provider' => $provider,
                    '--tag' => $tag,
                    '--force' => $force,
                ]);
            }
        }
    }

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

    protected function getManifest(ZipArchive $zip, int $index): ?array
    {
        try {
            return @json_decode($zip->getFromIndex($index), true);
        } catch (Exception) {
            return null;
        }
    }

    protected function findManifestIndex(ZipArchive $zip): ?int
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
