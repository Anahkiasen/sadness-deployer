<?php
namespace SadnessDeployer\Tasks\Subtasks;

use SadnessDeployer\Tasks\AbstractTask;

class Environment extends AbstractTask
{
    /**
     * Environment constructor.
     */
    public function __construct()
    {
        $environment = env('APP_ENV', 'production');
        $file = '.env.'.$environment;

        if (file_exists(base_path($file))) {
            $this->run('cp '.$file.' .env');
        }
    }
}
