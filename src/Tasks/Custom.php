<?php

namespace SadnessDeployer\Tasks;

use SadnessDeployer\Configuration;

class Custom extends AbstractTask
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);

        $this->run($configuration->get('tasks'));
    }
}
