<?php

namespace LCFramework\Framework\Module\Models;

use Illuminate\Database\Eloquent\Model;
use LCFramework\Framework\Module\Facade\Modules;
use Sushi\Sushi;

class Module extends Model
{
    use Sushi;

    public $incrementing = false;

    protected $keyType = 'string';

    protected array $schema = [
        'id' => 'string',
        'name' => 'string',
        'description' => 'string',
        'version' => 'string',
        'path' => 'string',
        'status' => 'string',
    ];

    public function getRows()
    {
        return collect(Modules::all())
            ->map(fn ($module): array => [
                'id' => $module->getName(),
                'name' => $module->getName(),
                'description' => $module->getDescription(),
                'version' => $module->getVersion(),
                'path' => $module->getPath(),
                'status' => $module->getStatus(),
            ])
            ->all();
    }
}
