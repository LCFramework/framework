<?php

namespace LCFramework\Framework\Theme\Exception;

use Exception;
use LCFramework\Framework\Theme\Theme;

class ThemeNotFoundException extends Exception
{
    public static function theme(string|Theme $theme): static
    {
        $name = $theme;
        if ($name instanceof Theme) {
            $name = $name->getName();
        }

        return new(sprintf('Cannot find theme "%s"', $name));
    }
}
