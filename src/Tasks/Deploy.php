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
        $this->run(new Dependencies());
        $this->clear();
        $this->annotations();
        $this->database();
        $this->run('artisan up');
        $this->optimize();
    }
}
