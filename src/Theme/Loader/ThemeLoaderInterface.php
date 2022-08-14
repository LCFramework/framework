<?php

namespace LCFramework\Framework\Theme\Loader;

use LCFramework\Framework\Theme\Theme;

interface ThemeLoaderInterface
{
    public function fromPath(string $path): Theme;

    public function fromArray(array $array): Theme;
}
