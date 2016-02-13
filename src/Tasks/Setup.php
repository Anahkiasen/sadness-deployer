<?php

namespace SadnessDeployer\Tasks;

class Setup extends Deploy
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
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
