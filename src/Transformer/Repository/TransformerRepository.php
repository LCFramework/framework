<?php

namespace LCFramework\Framework\Transformer\Repository;

use Closure;
use Illuminate\Contracts\Foundation\Application;

class TransformerRepository implements TransformerRepositoryInterface
{
    protected Application $app;

    protected array $callbacks = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function add(string $name, array|string|Closure $callback): void
    {
        if (! isset($this->callbacks[$name])) {
            $this->callbacks[$name] = [];
        }

        $this->callbacks[$name][] = $callback;
    }

    public function transform(string $name, $value)
    {
        $callbacks = $this->callbacks[$name] ?? [];

        foreach ($callbacks as $callback) {
            $value = $this->resolve($callback, $value);
        }

        return $value;
    }

    protected function resolve(array|string|Closure $callback, $value)
    {
        return $this->app->call($callback, [
            'value' => $value
        ]);
    }
}
