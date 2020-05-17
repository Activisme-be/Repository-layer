<?php

namespace ActivismeBe\Repositories\Providers;

use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use ActivismeBe\Repositories\Console\Commands\MakeCriteriaCommand;
use ActivismeBe\Repositories\Console\Commands\MakeRepositoryCommand;
use ActivismeBe\Repositories\Console\Commands\Creators\CriteriaCreator;
use ActivismeBe\Repositories\Console\Commands\Creators\RepositoryCreator;

/**
 * Class RepositoryProvider
 *
 * @package ActivismeBe\Repositories\Providers
 */
class RepositoryProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/repositories.php' => config_path('repositories.php')
        ], 'repositories');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerBindings();
        $this->registerMakeRepositoryCommand();
        $this->registerMakeCriteriaCommand();
        $this->registerServiceProviders();

        $this->commands(['command.repository.make', 'command.criteria.make']);
        $this->mergeConfigFrom(__DIR__ . '/../../config/repositories.php', 'repositories');
    }

    /**
     * Register the secondary service providers for the package.
     */
    protected function registerServiceProviders(): void
    {
        $this->app->bind(EventServiceProvider::class);
    }

    /**
     * Register the bindings.
     *
     * @return void
     */
    protected function registerBindings(): void
    {
        $this->app->instance('FileSystem', new Filesystem());

        $this->app->bind('Composer', static function ($app): Composer {
            return new Composer($app['FileSystem']);
        });

        $this->app->singleton('RepositoryCreator', static function ($app): RepositoryCreator {
            return new RepositoryCreator($app['FileSystem']);
        });

        $this->app->singleton('CriteriaCreator', static function ($app): CriteriaCreator {
            return new CriteriaCreator($app['FileSystem']);
        });
    }

    /**
     * Register the make:repository command.
     *
     * @return void
     */
    protected function registerMakeRepositoryCommand(): void
    {
        $this->app->singleton('command.repository.make', static function ($app): MakeRepositoryCommand {
            return new MakeRepositoryCommand($app['RepositoryCreator']);
        });
    }

    /**
     * Register the make:criteria command.
     *
     * @return void
     */
    protected function registerMakeCriteriaCommand(): void
    {
        $this->app->singleton('command.criteria.make', static function ($app): MakeCriteriaCommand {
            return new MakeCriteriaCommand($app['CriteriaCreator']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['command.repository.make', 'command.criteria.make'];
    }
}
