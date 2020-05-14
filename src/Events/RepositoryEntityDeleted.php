<?php

namespace Dugajean\Repositories\Events;

/**
 * Class RepositoryEntityDeleted
 *
 * @package Dugajean\Repositories\Events
 */
class RepositoryEntityDeleted extends RepositoryEventBase
{
    protected string $action = 'deleted';
}
