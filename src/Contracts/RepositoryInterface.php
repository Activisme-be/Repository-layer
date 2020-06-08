<?php

namespace ActivismeBe\Repositories\Contracts;

/**
 * Interface RepositoryInterface
 *
 * @package ActivismeBe\Repositories\Contracts
 */
interface RepositoryInterface
{
    /**
     * Method for getting all the records from the storage.
     *
     * @param  array $columns
     * @return mixed
     */
    public function all($columns = ['*']);

    /**
     * Getting all the records in an paginated way.
     *
     * @param  int   $perPage
     * @param  array $columns
     * @return mixed
     */
    public function paginate($perPage = 1, $columns = ['*']);

    /**
     * Method for creating a record in the storage
     * @param  array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Save a model without massive assignment
     *
     * @param  array $data
     * @return bool
     */
    public function saveModel(array $data);

    /**
     * Method for updating a record in the storage.
     *
     * @param  array $data
     * @param  int   $id
     * @return mixed
     */
    public function update(array $data, $id);

    /**
     * Method for deleting a record.
     *
     * @param  int $id
     * @return mixed
     */
    public function delete($id);

    /**
     * Method for getting the records based on their unique identifier.
     *
     * @param  int   $id
     * @param  array $columns
     * @return mixed
     */
    public function find($id, $columns = ['*']);

    /**
     * Method for getting all the record on the given attribute.
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $columns
     * @return mixed
     */
    public function findBy($field, $value, $columns = ['*']);

    /**
     * Find all the records by the matching attribute.
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $columns
     * @return mixed
     */
    public function findAllBy($field, $value, $columns = ['*']);

    /**
     * Find a collection of model by the given query collections.
     *
     * @param  mixed $where
     * @param  array $columns
     * @return mixed
     */
    public function findWhere($where, $columns = ['*']);
}
