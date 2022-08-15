<?php

namespace LCFramework\Framework\Transformer\Repository;

use Closure;

interface TransformerRepositoryInterface
{
    public function add(string $name, array|string|Closure $callback): void;

    public function transform(string $name, $value);
}
