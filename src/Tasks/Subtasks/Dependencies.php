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
        $folder = $this->option('paths.app');
        if (!file_exists($folder.'/composer.phar')) {
            $this->getComposer();
        }

        $this->run([
            'composer self-update',
            'composer install --no-interaction --no-scripts --prefer-dist '.$flags,
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
