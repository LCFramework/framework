<?php

namespace LCFramework\Framework\Form\Builder;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;

class FormBuilder
{
    protected array $schema;

    public function __construct(array $schema)
    {
        $this->schema = $schema;
    }

    public static function make(array $schema): static
    {
        return new static($schema);
    }

    public function get(string|Closure $callback): ?Component
    {
        $callback = $callback instanceof Closure
            ? $callback
            : fn(Component $component): bool => $component instanceof Field && $component->getStatePath() === $callback;

        return collect($this->getFlatSchema($this->schema))
            ->first($callback);
    }

    protected function getFlatSchema($schema): array
    {
        $components = [];

        foreach ($schema as $component) {
            $components[] = $component;
            $components[] = $this->getFlatSchema($component->getChildComponents());
        }

        return collect($components)->flatten()->all();
    }
}
