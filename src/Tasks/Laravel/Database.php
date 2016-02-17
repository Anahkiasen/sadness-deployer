<?php

namespace SadnessDeployer\Tasks\Laravel;

use SadnessDeployer\Configuration;
use SadnessDeployer\Tasks\AbstractTask;

class Database extends AbstractTask
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);

        $this->run('artisan migrate --force');
    }
}
