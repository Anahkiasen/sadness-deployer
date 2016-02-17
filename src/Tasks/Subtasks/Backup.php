<?php
namespace SadnessDeployer\Tasks\Subtasks;

use SadnessDeployer\Tasks\AbstractTask;

class Backup extends AbstractTask
{
    /**
     * Database constructor.
     */
    public function __construct()
    {
        $this->run('artisan db:backup');
    }
}
