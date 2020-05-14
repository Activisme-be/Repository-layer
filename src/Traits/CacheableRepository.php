<?php

namespace ActivismeBe\Repositories\Traits;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use ActivismeBe\Repositories\Contracts\CriteriaInterface;
use ActivismeBe\Repositories\Helpers\CacheKeys;
use ReflectionObject;
use Exception;

/**
 * Trait CacheableRepository
 *
 * @package Dugajean\Repositories\Traits
 */
trait CacheableRepository
{
    protected ?CacheRepository $cacheRepository = null;

    /**
     * Set Cache repository
     *
     * @param  CacheRepository $repository
     * @return $this
     */
    public function setCacheRepository(CacheRepository $repository): self
    {
        $this->cacheRepository = $repository;

        return $this;
    }

    /**
     * Return instance of Cache Repository
     *
     * @return CacheRepository
     */
    public function getCacheRepository(): CacheRepository
    {
        if (is_null($this->cacheRepository)) {
            $this->cacheRepository = app(config('repository.cache.repository', 'cache'));
        }

        return $this->cacheRepository;
    }

    /**
     * Skip cache
     *
     * @param  bool $status
     * @return $this
     */
    public function skipCache(bool $status = true): self
    {
        $this->cacheSkip = $status;

        return $this;
    }

    /**
     * Determine if the cache is skipped of not.
     *
     * @return bool
     */
    public function isSkippedCache(): bool
    {
        $skipped = isset($this->cacheSkip) ? $this->cacheSkip : false;
        $request = app('Illuminate\Http\Request');

        $skipCacheParam = config('repository.cache.params.skipCache', 'skipCache');

        if ($request->has($skipCacheParam) && $request->get($skipCacheParam)) {
            $skipped = true;
        }

        return $skipped;
    }

    /**
     * Determine if caching is allowed on the attribute.
     *
     * @param $method
     * @return bool
     */
    protected function allowedCache($method): bool
    {
        $cacheEnabled = config('repository.cache.enabled', true);

        if (! $cacheEnabled) {
            return false;
        }

        $cacheOnly = isset($this->cacheOnly) ? $this->cacheOnly : config('repository.cache.allowed.only', null);
        $cacheExcept = isset($this->cacheExcept) ? $this->cacheExcept : config('repository.cache.allowed.except', null);

        if (is_array($cacheOnly)) {
            return in_array($method, $cacheOnly);
        }

        if (is_array($cacheExcept)) {
            return !in_array($method, $cacheExcept);
        }

        if (is_null($cacheOnly) && is_null($cacheExcept)) {
            return true;
        }

        return false;
    }

    /**
     * Get cache key for the method
     *
     * @param  $method
     * @param  $args
     * @return string|null
     */
    public function getCacheKey($method, $args = null): ?string
    {
        $request = app('Illuminate\Http\Request');
        $args = serialize($args);
        $criteria = $this->serializeCriteria();
        $key = sprintf('%s@%s-%s', get_called_class(), $method, md5($args . $criteria . $request->fullUrl()));

        CacheKeys::putKey(get_called_class(), $key);

        return $key;
    }

    /**
     * Serialize the criteria making sure the Closures are taken care of.
     *
     * @return string
     */
    protected function serializeCriteria(): string
    {
        try {
            return serialize($this->getCriteria());
        } catch (Exception $e) {
            return serialize($this->getCriteria()->map(function ($criterion) {
                return $this->serializeCriterion($criterion);
            }));
        }
    }

    /**
     * Serialize single criterion with customized serialization of Closures
     *
     * @param  Dugajean\Repositories\Contracts\CriteriaInterface        $criterion
     * @return Dugajean\Repositories\Contracts\CriteriaInterface|array
     *
     * @throws \Exception
     */
    protected function serializeCriterion(CriteriaInterface $criterion)
    {
        try {
            serialize($criterion);

            return $criterion;
        } catch (Exception $e) {
            // We want to take care of the closure serialization errors,
            // other than that we will simply re-throw the exception.
            if ($e->getMessage() !== "Serialization of 'Closure' is not allowed") {
                throw $e;
            }

            $r = new ReflectionObject($criterion);

            return [
                'hash' => md5((string) $r),
                'properties' => $r->getProperties(),
            ];
        }
    }

    /**
     * Get cache minutes
     *
     * @return int
     */
    public function getCacheMinutes(): int
    {
        $cacheMinutes = isset($this->cacheMinutes)
            ? $this->cacheMinutes
            : config('repository.cache.minutes', 30);

        return $cacheMinutes;
    }

    /**
     * Retrieve all data of the repository
     *
     * @param  array $columns
     * @return mixed
     */
    public function all(array $columns = ['*'])
    {
        if (! $this->allowedCache('all') || $this->isSkippedCache()) {
            return parent::all($columns);
        }

        $key = $this->getCacheKey('all', func_get_args());
        $minutes = $this->getCacheMinutes();

        $value = $this->getCacheRepository()->remember($key, $minutes, function () use ($columns) {
            return parent::all($columns);
        });

        $this->resetModel();
        $this->resetScope();

        return $value;
    }

    /**
     * Retrieve all the data of the repository, paginated
     *
     * @param  null            $limit
     * @param  array|string[]  $columns
     * @param  string          $method
     * @return mixed
     */
    public function paginate($limit = null, array $columns = ['*'], string $method = 'paginate')
    {
        if (! $this->allowedCache('paginate') || $this->isSkippedCache()) {
            return parent::paginate($limit, $columns, $method);
        }

        $key = $this->getCacheKey('paginate', func_get_args());
        $minutes = $this->getCacheMinutes();

        $value = $this->getCacheRepository()->remember($key, $minutes, function () use ($limit, $columns, $method) {
            return parent::paginate($limit, $columns, $method);
        });

        $this->resetModel();
        $this->resetScope();

        return $value;
    }

    /**
     * Find data by the identifier
     *
     * @param        $id
     * @param  array $columns
     * @return mixed
     */
    public function find($id, array $columns = ['*'])
    {
        if (! $this->allowedCache('find') || $this->isSkippedCache()) {
            return parent::find($id, $columns);
        }

        $key = $this->getCacheKey('find', func_get_args());
        $minutes = $this->getCacheMinutes();

        $value = $this->getCacheRepository()->remember($key, $minutes, function () use ($id, $columns) {
            return parent::find($id, $columns);
        });

        $this->resetModel();
        $this->resetScope();

        return $value;
    }

    /**
     * Find data by field and value in the repository
     *
     * @param        $fields
     * @param        $value
     * @param  array $columns
     * @return mixed
     */
    public function findByField($field, $value = null, array $columns = ['*'])
    {
        if (!$this->allowedCache('findByField') || $this->isSkippedCache()) {
            return parent::findByField($field, $value, $columns);
        }

        $key = $this->getCacheKey('findByField', func_get_args());
        $minutes = $this->getCacheMinutes();

        $value = $this->getCacheRepository()->remember($key, $minutes, function () use ($field, $value, $columns) {
            return parent::findByField($field, $value, $columns);
        });

        $this->resetModel();
        $this->resetScope();

        return $value;
    }

    /**
     * Find data by multiple fields
     *
     * @param  array $where
     * @param  array $columns
     * @return mixed
     */
    public function findWhere(array $where, array $columns = ['*'])
    {
        if (! $this->allowedCache('findWhere') || $this->isSkippedCache()) {
            return parent::findWhere($where, $columns);
        }

        $key = $this->getCacheKey('findWhere', func_get_args());
        $minutes = $this->getCacheMinutes();

        $value = $this->getCacheRepository()->remember($key, $minutes, function () use ($where, $columns) {
            return parent::findWhere($where, $columns);
        });

        $this->resetModel();
        $this->resetScope();

        return $value;
    }

    /**
     * Find data by criteria
     *
     * @param  CriteriaInterface $criteria
     * @return mixed
     */
    public function getByCriteria(CriteriaInterface $criteria)
    {
        if (! $this->allowedCache('getByCriteria') || $this->isSkippedCache()) {
            return parent::getByCriteria($criteria);
        }

        $key = $this->getCacheKey('getByCriteria', func_get_args());
        $minutes = $this->getCacheMinutes();

        $value = $this->getCacheRepository()->remember($key, $minutes, function () use ($criteria) {
            return parent::getByCriteria($criteria);
        });

        $this->resetModel();
        $this->resetScope();

        return $value;
    }
}
