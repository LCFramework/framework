<?php

namespace LCFramework\Framework\Module\Repository;

use LCFramework\Framework\Module\Module;

interface ModuleRepositoryInterface
{
    public function all(): array;

    public function ordered(): array;

    public function enabled(): array;

    public function disabled(): array;

    public function status(string $status): array;

    public function enable(string|Module $module): void;

    public function disable(string|Module $module): void;

    public function setStatus(string|Module $module, string $status): void;

    public function find(string $name): ?Module;

    public function findOrFail(string $name): Module;

    public function validate(string|Module $module): bool;

    public function boot(): void;

    public function delete(string|Module $module): bool;
}
