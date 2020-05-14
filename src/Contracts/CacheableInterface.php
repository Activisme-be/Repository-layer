<?php

namespace Dugajean\Repositories\Contracts;

use Illuminate\Contracts\Cache\Repository as CacheRepository;

/**
 * Interface CacheableInterface
 *
 * @package Dugajean\Repositories\Contracts
 */
interface CacheableInterface
{
    /**
     * Set Cache Repository
     *
     * @param CacheRepository $repository
     *
     * @return $this
     */
    public function setCacheRepository(CacheRepository $repository): self;

    /**
     * Return instance of Cache Repository
     *
     * @return CacheRepository
     */
    public function getCacheRepository(): CacheRepository;

    /**
     * Get Cache key for the method
     *
     * @param  $method
     * @param  $args
     * @return string
     */
    public function getCacheKey($method, $args = null): string;

    /**
     * Get cache minutes
     *
     * @return int
     */
    public function getCacheMinutes(): int;


    /**
     * Skip Cache
     *
     * @param  bool $status
     * @return $this
     */
    public function skipCache(bool $status = true): self;
}
