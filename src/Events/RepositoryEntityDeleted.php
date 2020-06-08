<?php

namespace ActivismeBe\Repositories\Events;

/**
 * Class RepositoryEntityDeleted
 *
 * @package ActivismeBe\Repositories\Events
 */
class RepositoryEntityDeleted extends RepositoryEventBase
{
    protected string $action = 'deleted';
}
