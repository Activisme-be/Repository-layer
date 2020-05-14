<?php

namespace Dugajean\Repositories\Listeners;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Dugajean\Repositories\Contracts\RepositoryInterface;
use Dugajean\Repositories\Events\RepositoryEventBase;
use Dugajean\Repositories\Helpers\CacheKeys;

/**
 * Class CleanCacheRepository
 *
 * @package Dugajean\Repositories\Listeners
 */
class CleanCacheRepository
{
    protected ?CacheRepository $cache = null;

    protected ?RepositoryInterface $repository = null;

    protected ?Model $model = null;

    protected ?string $action = null;

    public function __construct()
    {
        $this->cache = app(config('repository.cache.repository', 'cache'));
    }

    public function handle(RepositoryEventBase $event)
    {
        try {
            $cleanEnabled = config("repository.cache.clean.enabled", true);

            if ($cleanEnabled) {
                $this->repository = $event->getRepository();
                $this->model = $event->getModel();
                $this->action = $event->getAction();

                if (config("repository.cache.clean.on.{$this->action}", true)) {
                    $cacheKeys = CacheKeys::getKeys(get_class($this->repository));

                    if (is_array($cacheKeys)) {
                        foreach ($cacheKeys as $key) {
                            $this->cache->forget($key);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
