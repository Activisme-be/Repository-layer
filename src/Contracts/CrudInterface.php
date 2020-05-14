<?php

namespace ActivismeBe\Repositories\Contracts;

interface CrudInterface
{
    /**
     * Create a entity in the database storage.
     *
     * @param  array $properties
     * @return mixed
     */
    public function create(array $properties);

    /**
     * Get an entity from the database.
     *
     * @param  mixed $id
     * @return mixed
     */
    public function read($id);

    /**
     * Update an database entity in the application.
     *
     * @param  array $properties
     * @param  mixed $id
     * @return mixed
     */
    public function update(array $properties, $id);

    /**
     * Delete database entities in the application.
     *
     * @param  int|array $id
     * @return mixed
     */
    public function delete($id);
}
