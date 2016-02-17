<?php

namespace SadnessDeployer\Tasks\Subtasks;

use SadnessDeployer\Configuration;
use SadnessDeployer\Tasks\AbstractTask;

class Environment extends AbstractTask
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);

        $environment = getenv('APP_ENV') ?: 'production';
        $file = '.env.'.$environment;

        if (file_exists($this->option('paths.app').$file)) {
            $this->run('cp '.$file.' .env');
        }
    }
}
