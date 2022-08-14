<?php

namespace LCFramework\Framework\Theme\Loader;

use LCFramework\Framework\Support\Json;
use LCFramework\Framework\Theme\Theme;

class ThemeLoader implements ThemeLoaderInterface
{
    public function fromPath(string $path): Theme
    {
        $fullPath = realpath($path.'/composer.json');

        $json = Json::make($fullPath);

        return Theme::make(
            $json->get('name'),
            $json->get('description'),
            $path,
            $json->get('extra.lcframework.theme.parent'),
            $json->get('extra.lcframework.theme.providers', [])
        );
    }

    public function fromArray(array $array): Theme
    {
        return Theme::make(
            $array['name'],
            $array['description'],
            $array['path'],
            $array['parent'],
            $array['providers']
        );
    }
}
