<?php

namespace LCFramework\Framework\Theme\Repository;

use LCFramework\Framework\Theme\Theme;

interface ThemeRepositoryInterface
{
    public function all(): array;

    public function enabled(): ?Theme;

    public function disabled(): array;

    public function enable(string|Theme $theme): void;

    public function disable(): void;

    public function find(string $name): ?Theme;

    public function findOrFail(string $name): Theme;

    public function validate(string|Theme $theme): bool;

    public function boot(): void;

    public function delete(string|Theme $theme): bool;

    public function install(string $path): bool;
}
