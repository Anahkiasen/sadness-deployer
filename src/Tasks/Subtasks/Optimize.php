<?php
namespace SadnessDeployer\Tasks\Subtasks;

use SadnessDeployer\Tasks\AbstractTask;

class Optimize extends AbstractTask
{
    /**
     * Optimize constructor.
     */
    public function __construct()
    {
        $this->run([
            'artisan config:cache',
            'artisan route:cache',
            'artisan optimize',
        ]);
    }
}
