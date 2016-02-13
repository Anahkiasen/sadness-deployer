<?php
namespace SadnessDeployer\Tasks\Subtasks;

use SadnessDeployer\Tasks\AbstractTask;

class Repository extends AbstractTask
{
    /**
     * Repository constructor.
     */
    public function __construct()
    {
        $this->run([
            'git checkout '.$this->option('scm.branch'),
            'git reset --hard',
            'git clean -df',
            'git pull',
        ]);
    }
}
