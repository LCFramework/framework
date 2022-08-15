<?php

namespace LCFramework\Framework\Form\Builder;

use Filament\Forms\Components\Field;

class FormBuilder
{
    protected array $schema;

    public function __construct(array $schema)
    {
        $this->schema = $this->build($schema);
    }

    public static function make(array $schema): static
    {
        return new static($schema);
    }

    public function &get(string $name): ?Field
    {
        $schema = $this->find($this->schema, $name);

        dd($schema);

        $null = null;

        return $null;
    }

    protected function &find(array &$target, array|string $key)
    {
        $parts = is_array($key) ? $key : explode('.', $key);

        $cursor = null;
        foreach ($parts as $part) {
            if (in_array($part, $target)) {
                $cursor = &$target[$part];
            } else {
                $cursor = &$this->find($target->getChildComponents(), $part);
            }
        }

        return $cursor;
    }

    protected function build(array $schema): array
    {
        $built = [];

        foreach ($schema as $component) {
            $built[$component->getName()] = $component->schema(
                $this->build($component->getChildComponents())
            );
        }

        return $built;
    }
}
