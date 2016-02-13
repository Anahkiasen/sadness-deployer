<?php
namespace SadnessDeployer\Tasks;

class Dependencies extends AbstractTask
{
    /**
     * Dependencies constructor.
     */
    public function __construct()
    {
        $flags = env('APP_DEBUG') ? '--no-dev' : '';
        if (!file_exists(base_path('composer.phar'))) {
            $this->getComposer();
        }

        $this->run([
            'composer self-update',
            'composer update --no-interaction --no-scripts '.$flags,
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
