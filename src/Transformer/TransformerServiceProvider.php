<?php

namespace LCFramework\Framework\Transformer;

use Illuminate\Support\ServiceProvider;
use LCFramework\Framework\Transformer\Repository\TransformerRepository;
use LCFramework\Framework\Transformer\Repository\TransformerRepositoryInterface;

class TransformerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->alias(
            TransformerRepositoryInterface::class,
            'lcframework.transformer'
        );
        $this->app->singleton(
            TransformerRepositoryInterface::class,
            TransformerRepository::class
        );
    }
}
