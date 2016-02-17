<?php

namespace SadnessDeployer\Tasks;

use SadnessDeployer\Configuration;
use SadnessDeployer\Tasks\Subtasks\Annotations;
use SadnessDeployer\Tasks\Subtasks\Backup;
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
    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);
        
        $this->run([
            // Shut down application
            'artisan down',
            Backup::class,

            // Update state
            Repository::class,
            Environment::class,
            Dependencies::class,
            Clear::class,
            Annotations::class,

            // Update database
            Database::class,
            Backup::class,
            'artisan up',

            // Optimize application
            Optimize::class,
        ]);
    }
}
