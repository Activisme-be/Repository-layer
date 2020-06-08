<?php

namespace ActivismeBe\Repositories\Events;

use Illuminate\Database\Eloquent\Model;
use ActivismeBe\Repositories\Contracts\RepositoryInterface;

/**
 * Class RepositoryEventBase
 *
 * @package ActivismeBe\Repositories\Events
 */
abstract class RepositoryEventBase
{
    protected Model $model;

    protected RepositoryInterface $repository;

    protected string $action;

    /**
     * RepositoryEventBase constructor
     *
     * @param  RepositoryInterface $repository
     * @param  Model|null          $model
     * @return void
     */
    public function __construct(RepositoryInterface $repository, Model $model = null)
    {
        $this->repository = $repository;
        $this->model = $model;
    }

    /**
     * Getting the model instance
     *
     * @return Model|array
     */
    public function getModel()
    {
        return $this->model;
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    public function getAction(): string
    {
        return $this->action;
    }
}
