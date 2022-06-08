<?php

namespace Luttje\ScopedComponents;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Facades\Blade;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('scope', function () {
            return \Luttje\ScopedComponents\Scope::getStartScope();;
        });
        Blade::directive('endscope', function () {
            return \Luttje\ScopedComponents\Scope::getEndScope();;
        });
    }
}
