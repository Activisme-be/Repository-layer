<?php

namespace Dugajean\Repositories\Events;

/**
 * Class RepositoryEntityCreated
 *
 * @package Dugajean\Repositories\Events
 */
class RepositoryEntityCreated extends RepositoryEventBase
{
    protected string $action = 'created';
}
