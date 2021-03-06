<?php

namespace Aiman\ContentManager;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Aiman\ContentManager\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'content-manager');

        $this->app->booted(function () {
            $this->routes();
        });

        $this->publishMigrations();

        $this->publishConfig();


        Nova::serving(function (ServingNova $event) {
            Nova::provideToScript([
                'content_manager' => config('content-manager')
            ]);
        });
    }
    /**
     * Publish required migration
     */
    private function publishMigrations()
    {
        $this->publishes([
            __DIR__.'/Http/Migrations/create_contents_table.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_contents_table.php'),
        ], 'content-manager-migration');
    }

    private function publishConfig()
    {
        $this->publishes([
            __DIR__.'/../config/content-manager.php' => config_path('content-manager.php'),
        ], 'config');
    }

    public function register()
    {
        try {
            $this->mergeConfigFrom(__DIR__.'/../config/content-manager.php', 'content-manager');
        } catch (\Exception $exception){

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
                ->prefix('nova-vendor/content-manager')
                ->group(__DIR__.'/../routes/api.php');
    }

}
