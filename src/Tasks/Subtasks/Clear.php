<?php
namespace SadnessDeployer\Tasks\Subtasks;

use SadnessDeployer\Tasks\AbstractTask;

class Clear extends AbstractTask
{
    /**
     * Clear constructor.
     */
    public function __construct()
    {
        $this->run([
            'artisan clear-compiled',
            'artisan cache:clear',
            'artisan config:clear',
            'artisan route:clear',
            'artisan twig:clean',
        ]);
    }
}
