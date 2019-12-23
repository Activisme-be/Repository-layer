<?php

namespace Dugajean\Repositories\Console\Commands;

use Dugajean\Repositories\Console\Commands\Creators\RepositoryCreator;

/**
 * Class MakeRepositoryCommand
 *
 * @package Dugajean\Repositories\Console\Commands
 */
class MakeRepositoryCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * MakeRepositoryCommand constructor
     *
     * @param  RepositoryCreator $creator
     * @return void
     */
    public function __construct(RepositoryCreator $creator)
    {
        parent::__construct($creator);
    }
}
