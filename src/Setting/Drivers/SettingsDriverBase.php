<?php

namespace LCFramework\Framework\Setting\Drivers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

abstract class SettingsDriverBase implements SettingsDriverInterface
{
    protected array $data = [];

    protected array $updated = [];

    protected array $deleted = [];

    public function get(string $key, $default = null)
    {
        return Arr::get($this->data, $key, function () use ($key, $default) {
            $value = $this->boot($key, $default);
            Arr::set($this->data, $key, $value);

            return $value;
        });
    }

    public function put(string $key, $value): void
    {
        Arr::set($this->data, $key, $value);

        $this->updated[] = $key;
    }

    public function forget(string $key): void
    {
        Arr::forget($this->data, $key);

        $this->deleted[] = $key;
    }

    abstract public function save(): void;

    abstract protected function load(string $key);

    protected function boot(string $key, $default)
    {
        if (! ($value = $this->loadCache($key))) {
            $value = $this->load($key) ?? $default;
            $this->setCache($key, $value);
        }

        return $value;
    }

    protected function loadCache(string $key)
    {
        if (! $this->isCacheEnabled()) {
            return null;
        }

        $key = $this->getCacheKey($key);

        return Cache::get($key);
    }

    protected function setCache(string $key, $value): void
    {
        if (! $this->isCacheEnabled()) {
            return;
        }

        $key = $this->getCacheKey($key);
        $ttl = $this->getCacheTtl();

        Cache::pull($key, $value, $ttl);
    }

    protected function clearCache(string $key): void
    {
        if (! $this->isCacheEnabled()) {
            return;
        }

        $key = $this->getCacheKey($key);

        Cache::forget($key);
    }

    protected function isCacheEnabled(): bool
    {
        return (bool) config('lcframework.settings.cache.enabled', true);
    }

    protected function getCacheKey(string $key): string
    {
        return config('lcframework.settings.cache.key').'.'.$key;
    }

    protected function getCacheTtl(): ?int
    {
        $ttl = config('lcframework.settings.cache.ttl');

        return $ttl === -1 ? null : $ttl;
    }

    protected function decode(string $value)
    {
        return json_decode($value, true);
    }

    protected function encode($value): string
    {
        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        }

        return json_encode($value);
    }
}
