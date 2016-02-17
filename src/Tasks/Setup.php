<?php

namespace SadnessDeployer\Tasks;

use SadnessDeployer\Configuration;

class Setup extends Deploy
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);

        $this->run([
            'git init',
            'git remote add origin '.$this->option('scm.url'),
            'git fetch -pt',
            'git clean -df',
            'git checkout '.$this->option('scm.branch'),
        ]);

        parent::__construct();
    }
}
