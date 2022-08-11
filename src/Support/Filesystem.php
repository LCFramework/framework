<?php

namespace LCFramework\Framework\Support;

use Illuminate\Filesystem\Filesystem as FilesystemBase;

class Filesystem extends FilesystemBase
{
    public function find(string $pattern, int $flags = 0): array
    {
        $files = glob($pattern, $flags);

        if ($files) {
            return $files;
        }

        $files = [];

        $directories = glob(
            dirname($pattern) . DIRECTORY_SEPARATOR . '*',
            GLOB_ONLYDIR | GLOB_NOSORT
        );

        if (!$directories) {
            $directories = [];
        }

        foreach ($directories as $directory) {
            $files = array_merge(
                $files,
                static::find(
                    $directory . DIRECTORY_SEPARATOR . basename($pattern),
                    $flags
                )
            );
        }

        return $files;
    }
}
