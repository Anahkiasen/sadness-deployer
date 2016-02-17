<?php

namespace SadnessDeployer\Tasks\Subtasks;

use SadnessDeployer\Configuration;
use SadnessDeployer\Tasks\AbstractTask;

class Dependencies extends AbstractTask
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);

        $flags = getenv('APP_DEBUG') ? '--no-dev' : '';
        if (!file_exists(__DIR__.'/../../../composer.phar')) {
            $this->getComposer();
        }

        $this->run([
            'composer self-update',
            'composer install --no-interaction --no-scripts '.$flags,
        ]);
    }

    //////////////////////////////////////////////////////////////////////
    ////////////////////////////// HELPERS ///////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * Setup composer if necessary.
     */
    private function getComposer()
    {
        $this->run([
            'php -r "readfile(\'https://getcomposer.org/installer\');" > composer-setup.php',
            'php composer-setup.php',
            'php -r "unlink(\'composer-setup.php\');"',
        ]);
    }
}
