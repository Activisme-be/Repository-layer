<?php

namespace ActivismeBe\Repositories\Events;

/**
 * Class RepositoryEntityCreated
 *
 * @package ActivismeBe\Repositories\Events
 */
class RepositoryEntityCreated extends RepositoryEventBase
{
    protected string $action = 'created';
}
