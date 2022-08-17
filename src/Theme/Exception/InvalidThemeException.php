<?php

namespace LCFramework\Framework\Theme\Exception;

use Exception;
use LCFramework\Framework\Theme\Theme;

class InvalidThemeException extends Exception
{
    public static function theme(string|Theme $theme): static
    {
        $name = $theme;
        if ($name instanceof Theme) {
            $name = $name->getName();
        }

        return new static(sprintf('Cannot enable invalid theme "%s"', $name));
    }
}
