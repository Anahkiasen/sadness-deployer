<?php

namespace SadnessDeployer\Tasks\Laravel;

use SadnessDeployer\Configuration;
use SadnessDeployer\Tasks\AbstractTask;

class ClearCaches extends AbstractTask
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
