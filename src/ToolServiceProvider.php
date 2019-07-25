<?php

namespace Joonas1234\NovaSimpleCms;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Joonas1234\NovaSimpleCms\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'nova-simple-cms');

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            //
        });

        $this->publishes([
            __DIR__.'/../config/nova_simple_cms.php' => config_path('nova/simple_cms.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../config/blueprints/example.php' => config_path('blueprints/example.php'),
            __DIR__.'/../resources/views/templates/example.blade.php' => resource_path('views/vendor/nova-simple-cms/templates/example.blade.php'),

        ], 'example');

        if (! class_exists('CreatePagesTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../database/migrations/create_pages_table.php.stub' => database_path('migrations/'.$timestamp.'_create_pages_table.php'),
            ], 'migrations');
        }
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
            ->prefix('nova-vendor/nova-simple-cms')
            ->group(__DIR__.'/../routes/api.php');

        Route::middleware('web')
            ->group(__DIR__.'/../routes/web.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/nova_simple_cms.php', 'nova.simple_cms');
    }
}
