<?php

namespace LCFramework\Framework\Theme\Models;

use Illuminate\Database\Eloquent\Model;
use LCFramework\Framework\Theme\Facade\Themes;
use Sushi\Sushi;

class Theme extends Model
{
    use Sushi;

    public $incrementing = false;

    protected $keyType = 'string';

    protected array $schema = [
        'id' => 'string',
        'name' => 'string',
        'description' => 'string',
        'path' => 'string',
        'parent' => 'string',
        'enabled' => 'integer',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function getRows()
    {
        $enabledTheme = Themes::enabled();

        return collect(Themes::all())
            ->map(fn ($theme): array => [
                'id' => $theme->getName(),
                'name' => $theme->getName(),
                'description' => $theme->getDescription(),
                'path' => $theme->getPath(),
                'parent' => $theme->getParent(),
                'enabled' => $enabledTheme?->getName() === $theme->getName(),
            ])
            ->values()
            ->all();
    }
}
