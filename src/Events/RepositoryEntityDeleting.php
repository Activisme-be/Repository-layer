<?php

namespace Dugajean\Repositories\Events;

/**
 * Class RepositoryEntityDeleting
 *
 * @package Dugajean\Repositories\Events
 */
class RepositoryEntityDeleting extends RepositoryEventBase
{
    protected string $action = 'deleting';
}
