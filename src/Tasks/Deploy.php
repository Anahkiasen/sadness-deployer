<?php

namespace SadnessDeployer\Tasks;

use SadnessDeployer\Configuration;
use SadnessDeployer\Tasks\Laravel\Annotations;
use SadnessDeployer\Tasks\Laravel\Backup;
use SadnessDeployer\Tasks\Laravel\ClearCaches;
use SadnessDeployer\Tasks\Laravel\Database;
use SadnessDeployer\Tasks\Laravel\Optimize;
use SadnessDeployer\Tasks\Subtasks\Dependencies;
use SadnessDeployer\Tasks\Subtasks\Environment;
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
            ClearCaches::class,
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
