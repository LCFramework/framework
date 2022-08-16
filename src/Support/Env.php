<?php

namespace LCFramework\Framework\Support;

use Illuminate\Support\Facades\File;

class Env
{
    protected string $path;

    protected ?string $content = null;

    public function __construct(?string $path = null)
    {
        $this->path = $path ?? app()->environmentFilePath();
    }

    public static function make(?string $path = null): static
    {
        return new static($path);
    }

    public function put(string $key, $value): static
    {
        if ($this->content === null) {
            $this->load();
        }

        $this->content = $this->set($key, $value);

        return $this;
    }

    public function save(): bool
    {
        return File::put($this->path, $this->content, true);
    }

    protected function load(): void
    {
        $this->content = File::get($this->path, true);
    }

    protected function set(string $key, $value): string
    {
        $oldPair = $this->readKeyValuePair($key);

        if (preg_match('/\s/', $value) || str_contains($value, '=')) {
            $value = '"' . $value . '"';
        }

        $newPair = $key . '=' . $value;

        if ($oldPair !== null) {
            return preg_replace(
                '/^' .
                preg_quote($oldPair, '/') .
                '$/uimU',
                $newPair,
                $this->content
            );
        }

        return $this->content . "\n" . $newPair . "\n";
    }

    protected function readKeyValuePair(string $key): ?string
    {
        if (
            preg_match(
                "#^ *{$key} *= *[^\r\n]*$#uimU",
                $this->content,
                $matches)
        ) {
            return $matches[0];
        }

        return null;
    }
}
