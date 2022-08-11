<?php

namespace LCFramework\Framework\Module\Loader;

use LCFramework\Framework\Module\Module;

interface ModuleLoaderInterface
{
    public function fromPath(string $path): Module;

    public function fromArray(array $array): Module;
}
