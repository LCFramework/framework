<?php

namespace LCFramework\Framework\Theme;

use Illuminate\Contracts\Support\Arrayable;

class Theme implements Arrayable
{
    protected string $name;

    protected string $description;

    protected string $path;

    protected ?string $parent;

    protected array $providers = [];

    public function __construct(
        string $name,
        string $description,
        string $path,
        ?string $parent = null,
        array $providers = []
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->path = $path;
        $this->parent = $parent;
        $this->providers = $providers;
    }

    public static function make(
        string $name,
        string $description,
        string $path,
        ?string $parent = null,
        array $providers = []
    ): static {
        return new static($name, $description, $path, $parent, $providers);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPath(?string $path = null): string
    {
        if ($path === null) {
            return $this->path;
        }

        return $this->path.'/'.ltrim($path, '/\\');
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function getProviders(): array
    {
        return $this->providers;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'path' => $this->getPath(),
            'parent' => $this->getParent(),
            'providers' => $this->getProviders(),
        ];
    }
}
