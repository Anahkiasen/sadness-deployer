<?php

namespace SadnessDeployer\Tasks;

class Deploy extends AbstractTask
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->run('artisan down');
        $this->repository();
        $this->environment();
        $this->dependencies();
        $this->clear();
        $this->annotations();
        $this->optimize();
        $this->database();
        $this->run('artisan up');
    }
}
