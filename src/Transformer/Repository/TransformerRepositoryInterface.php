<?php

namespace LCFramework\Framework\Transformer\Repository;

use Closure;

interface TransformerRepositoryInterface
{
    public function register(
        string $name,
        array|string|Closure $callback
    ): void;

    public function transform(string $name, $value);
}
