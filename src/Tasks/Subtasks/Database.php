<?php
namespace SadnessDeployer\Tasks\Subtasks;

use SadnessDeployer\Tasks\AbstractTask;

class Database extends AbstractTask
{
    /**
     * Database constructor.
     */
    public function __construct()
    {
        $this->run([
            'artisan migrate --force',
            'artisan db:backup',
        ]);
    }
}
