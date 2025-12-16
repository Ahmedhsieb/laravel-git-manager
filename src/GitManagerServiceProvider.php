<?php

namespace Ahmedhsieb\GitManager;

use Illuminate\Support\ServiceProvider;
use Ahmedhsieb\GitManager\Console\Commands\GitManagerSetup;
use Ahmedhsieb\GitManager\Console\Commands\GitManagerConfig;
use Ahmedhsieb\GitManager\Http\Middleware\GitManagerAuth;

class GitManagerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        // Merge package config with app config
        $this->mergeConfigFrom(
            __DIR__.'/config/git-manager.php', 'git-manager'
        );

        // Register GitManager as singleton
        $this->app->singleton('git-manager', function ($app) {
            return new \Ahmedhsieb\GitManager\Services\GitManager();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Publish config
        $this->publishes([
            __DIR__.'/config/git-manager.php' => config_path('git-manager.php'),
        ], 'git-manager-config');

        // Publish views
        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/git-manager'),
        ], 'git-manager-views');

        // Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'git-manager');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                GitManagerSetup::class,
                GitManagerConfig::class,
            ]);
        }

        // Register middleware
        $this->app['router']->aliasMiddleware('git-manager-auth', GitManagerAuth::class);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides()
    {
        return ['git-manager'];
    }
}