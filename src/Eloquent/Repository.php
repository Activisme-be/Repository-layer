<?php

namespace Dugajean\Repositories\Eloquent;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container as App;
use Dugajean\Repositories\Criteria\Criteria;
use Illuminate\Contracts\Pagination\Paginator;
use Dugajean\Repositories\Contracts\CriteriaInterface;
use Dugajean\Repositories\Contracts\RepositoryInterface;
use Dugajean\Repositories\Exceptions\RepositoryException;

/**
 * Class Repository
 *
 * @package Dugajean\Repositories\Eloquent
 */
abstract class Repository implements RepositoryInterface, CriteriaInterface
{
    /**
     * @var App
     */
    private $app;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Model
     */
    protected $newModel;

    /**
     * @var Collection
     */
    protected $criteria;

    /**
     * @var bool
     */
    protected $skipCriteria = false;

    /**
     * Prevents from overwriting same criteria in chain usage
     *
     * @var bool
     */
    protected $preventCriteriaOverwriting = true;

    /**
     * @param App        $app
     * @param Collection $collection
     *
     * @throws \Dugajean\Repositories\Exceptions\RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(App $app, Collection $collection)
    {
        $this->app = $app;
        $this->criteria = $collection;

        $this->resetScope();
        $this->makeModel();
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public abstract function model();

    /**
     * Method for getting all the records from the storage.
     *
     * @param  array $columns   The colums you want to display in your view.
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->get($columns);
    }

    /**
     * Method for loading relations in the application.
     *
     * @param  array $relations
     * @return $this
     */
    public function with(array $relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * List all the values in based on their key.
     *
     * @param  string $value
     * @param  string $key
     * @return array
     */
    public function lists($value, $key = null)
    {
        $this->applyCriteria();

        $lists = $this->model->pluck($value, $key);

        if (is_array($lists)) {
            return $lists;
        }

        return $lists->all();
    }

    /**
     * Getting all the records in an paginated way.
     *
     * @param  int    $perPage  The amount of columns u want to display per page.
     * @param  array  $columns  The columns u want to use in your view.
     * @param  string $method   The method identifier for your pagination.
     * @return Paginator
     */
    public function paginate($perPage = 25, $columns = ['*'], $method = 'full')
    {
        $this->applyCriteria();

        $paginationMethod = $method !== 'full' ? 'simplePaginate' : 'paginate';

        return $this->model->$paginationMethod($perPage, $columns);
    }

    /**
     * Method for creating a record in the storage.
     *
     * @param  array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Save a model without massive assignment
     *
     * @param  array $data
     * @return bool
     */
    public function saveModel(array $data)
    {
        foreach ($data as $k => $v) {
            $this->model->$k = $v;
        }

        return $this->model->save();
    }

    /**
     * Method for updating a record in the storage.
     *
     * @param  array  $data         The new data array where for the database record.
     * @param  mixed  $id           The unique identifier column from your database table.
     * @param  string $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute = 'id')
    {
        return $this->model->where($attribute, '=', $id)->update($data);
    }

    /**
     * Method for mass updating a record.
     *
     * @param  array $data
     * @param  int   $id    The unique identifier column from your database table.
     * @return mixed
     */
    public function updateRich(array $data, $id)
    {
        if (!($model = $this->model->find($id))) {
            return false;
        }

        return $model->fill($data)->save();
    }

    /**
     * Method for deleting a record.
     *
     * @param  mixed $id    The unique identifier column from your database table.
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * Method for getting the records based on their unique identifier.
     *
     * @param  int   $id        The unique identifier column from your database table.
     * @param  array $columns   The columns u want to use in your views.
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->find($id, $columns);
    }

    /**
     * Method for getting all the records based on the given attribute.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $columns
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    /**
     * Find all the records by the matching attribute.
     *
     * @param  string $attribute    The database column name
     * @param  mixed  $value        The value u want to search in the database column
     * @param  array  $columns      The columns u want in your output
     * @return mixed
     */
    public function findAllBy($attribute, $value, $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->where($attribute, '=', $value)->get($columns);
    }

    /**
     * Find a collection of models by the given query conditions.
     *
     * @param  array $where
     * @param  array $columns
     * @param  bool  $or
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function findWhere($where, $columns = ['*'], $or = false)
    {
        $this->applyCriteria();

        $model = $this->model;

        foreach ($where as $field => $value) {
            if ($value instanceof \Closure) {
                $model = (!$or)
                    ? $model->where($value)
                    : $model->orWhere($value);
            } elseif (is_array($value)) {
                if (count($value) === 3) {
                    [$field, $operator, $search] = $value;
                    $model = (!$or)
                        ? $model->where($field, $operator, $search)
                        : $model->orWhere($field, $operator, $search);
                } elseif (count($value) === 2) {
                    [$field, $search] = $value;
                    $model = (!$or)
                        ? $model->where($field, '=', $search)
                        : $model->orWhere($field, '=', $search);
                }
            } else {
                $model = (!$or)
                    ? $model->where($field, '=', $value)
                    : $model->orWhere($field, '=', $value);
            }
        }

        return $model->get($columns);
    }

    /**
     * Method for creating a model.
     *
     * @return Model
     *
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function makeModel(): Model
    {
        return $this->setModel($this->model());
    }

    /**
     * Set Eloquent Model to instantiate
     *
     * @param  $eloquentModel
     * @return Model
     *
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function setModel($eloquentModel)
    {
        $this->newModel = $this->app->make($eloquentModel);

        if (!$this->newModel instanceof Model) {
            throw new RepositoryException("Class {$this->newModel} must be an instance of " . Model::class);
        }

        return $this->model = $this->newModel;
    }

    /**
     * Returns clean entity of model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->newModel;
    }

    /**
     * Method for resetting a scope.
     *
     * @return $this
     */
    public function resetScope()
    {
        $this->skipCriteria(false);

        return $this;
    }

    /**
     * Method for skipping the criteria.
     *
     * @param  bool $status
     * @return $this
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;

        return $this;
    }

    /**
     * Method for getting criteria.
     *
     * @return mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * Method for getting criteria based on criteria.
     *
     * @param  Criteria $criteria
     * @return $this
     */
    public function getByCriteria(Criteria $criteria)
    {
        $this->model = $criteria->apply($this->model, $this);

        return $this;
    }

    /**
     * Method for pushing creteria.
     *
     * @param  Criteria $criteria
     * @return $this
     */
    public function pushCriteria(Criteria $criteria)
    {
        if ($this->preventCriteriaOverwriting) {
            $key = $this->criteria->search(function ($item) use ($criteria) {
                return (is_object($item) && (get_class($item) == get_class($criteria)));
            });

            if (is_int($key)) {
                $this->criteria->offsetUnset($key);
            }
        }

        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * Method for applying repository criteria(s) to your method.
     *
     * @return $this
     */
    public function applyCriteria()
    {
        if ($this->skipCriteria === true) {
            return $this;
        }

        foreach ($this->getCriteria() as $criteria) {
            if ($criteria instanceof Criteria) {
                $this->model = $criteria->apply($this->model, $this);
            }
        }

        return $this;
    }
}
