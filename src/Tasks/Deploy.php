<?php

namespace SadnessDeployer\Tasks;

use SadnessDeployer\Tasks\Subtasks\Annotations;
use SadnessDeployer\Tasks\Subtasks\Clear;
use SadnessDeployer\Tasks\Subtasks\Database;
use SadnessDeployer\Tasks\Subtasks\Dependencies;
use SadnessDeployer\Tasks\Subtasks\Environment;
use SadnessDeployer\Tasks\Subtasks\Optimize;
use SadnessDeployer\Tasks\Subtasks\Repository;

class Deploy extends AbstractTask
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->run([
            'artisan down',
            Repository::class,
            Environment::class,
            Dependencies::class,
            Clear::class,
            Annotations::class,
            Database::class,
            'artisan up',
            Optimize::class,
        ]);
    }
}
