<?php

namespace LCFramework\Framework\Module\Exception;

use Exception;
use LCFramework\Framework\Module\Module;

class ModuleNotFoundException extends Exception
{
    public static function module(string|Module $module): static
    {
        $name = $module;
        if ($name instanceof Module) {
            $name = $name->getName();
        }

        return new(sprintf('Cannot find module "%s"', $name));
    }
}
