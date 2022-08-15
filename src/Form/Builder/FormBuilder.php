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

    public function before($name, Component|array|Closure $components): static
    {
        return $this->insert($name, $components, 'before');
    }

    public function after($name, Component|array|Closure $components): static
    {
        return $this->insert($name, $components, 'after');
    }

    public function prepend(Component|array|Closure $components): static
    {
        array_unshift($this->schema, value($components));

        return $this;
    }

    public function append(Component|array|Closure $components): static
    {
        array_push($this->schema, value($components));

        return $this;
    }

    protected function insert(
        $name,
        Component|array|Closure $components,
        string $where
    ): static {
        $parent = &$this->findParent($this->schema, $name);
        if ($parent === null) {
            return $this;
        }

        $components = value($components);

        $index = $this->findComponentIndex($parent, $name);

        array_splice(
            $parent,
            $where === 'before' ? $index : ++$index,
            0,
            $components
        );

        return $this;
    }

    public function &get(string $name): ?Component
    {
        return $this->find($this->schema, $name);
    }

    public function forget(string $name, Closure $condition = null): static
    {
        if ($component = $this->get($name)) {
            $component
                ->hidden($condition ?? true)
                ->disabled($condition ?? true);
        }

        return $this;
    }

    public function build(): array
    {
        return $this->schema;
    }

    protected function &find(array &$schema, string $name): ?Component
    {
        foreach ($schema as $component) {
            if ($component instanceof Field && $component->getName() === $name) {
                return $component;
            }

            $childComponents = $component->getChildComponents();

            if ($child = $this->find($childComponents, $name)) {
                return $child;
            }
        }

        $default = null;

        return $default;
    }

    protected function &findParent(array &$schema, string $name): ?array
    {
        foreach ($schema as $component) {
            if ($component instanceof Field && $component->getName() === $name) {
                return $schema;
            }

            $childComponents = $component->getChildComponents();

            if ($child = $this->findParent($childComponents, $name)) {
                return $child;
            }
        }

        $default = [];

        return $default;
    }

    protected function findComponentIndex(array $parent, string $name): int
    {
        $index = 0;
        foreach ($parent as $component) {
            if ($component instanceof Field && $component->getName() === $name) {
                return $index;
            }

            $index++;
        }

        return $index;
    }
}
