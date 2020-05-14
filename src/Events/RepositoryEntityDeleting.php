<?php

namespace ActivismeBe\Repositories\Events;

/**
 * Class RepositoryEntityDeleting
 *
 * @package ActivismeBe\Repositories\Events
 */
class RepositoryEntityDeleting extends RepositoryEventBase
{
    protected string $action = 'deleting';
}
