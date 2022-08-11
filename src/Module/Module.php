<?php

namespace LCFramework\Framework\Module;

use Illuminate\Contracts\Support\Arrayable;

class Module implements Arrayable
{
    protected string $name;

    protected string $description;

    protected string $version;

    protected string $path;

    protected string $status;

    protected array $dependencies;

    protected array $providers;

    public function __construct(
        string $name,
        string $description,
        string $version,
        string $path,
        string $status,
        array $dependencies = [],
        array $providers = []
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->version = $version;
        $this->path = $path;
        $this->status = $status;
        $this->dependencies = $dependencies;
        $this->providers = $providers;
    }

    public static function make(
        string $name,
        string $description,
        string $version,
        string $path,
        string $status,
        array $dependencies = [],
        array $providers = []
    ): static {
        return new static(
            $name,
            $description,
            $version,
            $path,
            $status,
            $dependencies,
            $providers
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getPath(?string $path = null): string
    {
        if ($path === null) {
            return $this->path;
        }

        return $this->path.'/'.ltrim($path, '/\\');
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function enabled(): bool
    {
        return $this->status === 'enabled';
    }

    public function disabled(): bool
    {
        return $this->status === 'disabled';
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
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
            'version' => $this->getVersion(),
            'path' => $this->getPath(),
            'status' => $this->getStatus(),
            'dependencies' => $this->getDependencies(),
            'providers' => $this->getProviders(),
        ];
    }
}
