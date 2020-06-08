<?php

namespace ActivismeBe\Repositories\Criteria;

use Illuminate\Database\Eloquent\Model;
use ActivismeBe\Repositories\Contracts\RepositoryInterface;

/**
 * Class Criteria
 *
 * @package Dugajean\Repositories\Criteria
 */
abstract class Criteria
{
    /**
     * @param Model               $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    abstract public function apply($model, RepositoryInterface $repository);
}
