<?php

namespace SadnessDeployer\Tasks\Subtasks;

use SadnessDeployer\Configuration;
use SadnessDeployer\Tasks\AbstractTask;

class Clear extends AbstractTask
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);

        $this->run([
            'artisan clear-compiled',
            'artisan cache:clear',
            'artisan config:clear',
            'artisan route:clear',
            'artisan twig:clean',
        ]);
    }
}
