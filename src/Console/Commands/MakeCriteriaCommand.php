<?php

namespace ActivismeBe\Repositories\Console\Commands;

use ActivismeBe\Repositories\Console\Commands\Creators\CriteriaCreator;

/**
 * Class MakeCriteriaCommand
 *
 * @package ActivismeBe\Repositories\Console\Commands
 */
class MakeCriteriaCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:criteria';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new criteria class';

    /**
     * MakeCriteriaCommand constructor
     *
     * @param  CriteriaCreator $creator
     * @return void
     */
    public function __construct(CriteriaCreator $creator)
    {
        parent::__construct($creator);
    }
}
