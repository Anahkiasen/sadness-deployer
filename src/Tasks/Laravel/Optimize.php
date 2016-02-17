<?php

namespace SadnessDeployer\Tasks\Laravel;

use SadnessDeployer\Configuration;
use SadnessDeployer\Tasks\AbstractTask;

class Optimize extends AbstractTask
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);

        $this->run([
            'artisan config:cache',
            'artisan route:cache',
            'artisan optimize',
        ]);
    }
}
