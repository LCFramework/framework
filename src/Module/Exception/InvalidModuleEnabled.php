<?php

namespace LCFramework\Framework\Module\Exception;

use Exception;
use LCFramework\Framework\Module\Module;

class InvalidModuleEnabled extends Exception
{
    public static function module(string|Module $module): static
    {
        $name = $module;
        if ($name instanceof Module) {
            $name = $name->getName();
        }

        return new(sprintf('Cannot enable invalid module "%s"', $name));
    }
}
