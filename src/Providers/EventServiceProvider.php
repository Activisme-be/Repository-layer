<?php

namespace ActivismeBe\Repositories\Providers;

use ActivismeBe\Repositories\Events\RepositoryEntityCreated;
use ActivismeBe\Repositories\Events\RepositoryEntityDeleted;
use ActivismeBe\Repositories\Events\RepositoryEntityUpdated;
use ActivismeBe\Repositories\Listeners\CleanCacheRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Class EventServiceProvider
 *
 * @package ActivismeBe\Repositories\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the package.
     *
     * @var array
     */
    protected $listen = [
        RepositoryEntityCreated::class => [CleanCacheRepository::class],
        RepositoryEntityUpdated::class => [CleanCacheRepository::class],
        RepositoryEntityDeleted::class => [CleanCacheRepository::class],
    ];

    /**
     * Register the package his event listeners.
     */
    public function boot(): void
    {
        $events = app('events');

        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->listen($event, $listener);
            }
        }
    }

    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens(): array
    {
        return $this->listen;
    }
}
