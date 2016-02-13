<?php

namespace SadnessDeployer\Tasks\Subtasks;

use SadnessDeployer\Tasks\AbstractTask;

class Annotations extends AbstractTask
{
    /**
     * Annotations constructor.
     */
    public function __construct()
    {
        $this->run([
            'artisan route:scan',
            'artisan model:scan',
        ]);
    }
}
