<?php

namespace Armincms\Bios;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider; 
use Armincms\Bios\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'bios');

        $this->app->booted(function () {
            $this->routes();
        }); 
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
                ->namespace(__NAMESPACE__.'\\Http\\Controllers')
                ->prefix('nova-vendor/bios')
                ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    { 
        $this->commands([Console\ResourceCommand::class]);
    }
}
