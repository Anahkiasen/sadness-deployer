<?php

namespace SadnessDeployer\Tasks\Subtasks;

use SadnessDeployer\Configuration;
use SadnessDeployer\Tasks\AbstractTask;

class Repository extends AbstractTask
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);

        $this->run([
            'git checkout '.$this->option('scm.branch'),
            'git reset --hard',
            'git clean -df',
            'git pull',
        ]);
    }
}
