<?php

namespace SadnessDeployer;

use Closure;

class Deployer
{
    /**
     * @var CommandsRunner
     */
    protected $runner;

    /**
     * @param CommandsRunner $runner
     */
    public function __construct(CommandsRunner $runner)
    {
        $this->runner = $runner;
        $this->runner->setBasePath(base_path());
    }

    /**
     * Deploy the application.
     *
     * @param Closure|null $callback
     *
     * @return array
     */
    public function deploy(Closure $callback = null)
    {
        $callback = $callback ?: function (self $runner) {
            $runner->run('artisan down');
            $runner->repository();
            $runner->environment();
            $runner->dependencies();
            $runner->clear();
            $runner->annotations();
            $runner->optimize();
            $runner->database();
            $runner->run('artisan up');
        };

        $callback($this);

        return $this->runner->getOutput();
    }

    /**
     * @param string|array $commands
     */
    public function run($commands)
    {
        $this->runner->runCommands((array) $commands);
    }

    //////////////////////////////////////////////////////////////////////
    /////////////////////////////// TASKS ////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * Update repository.
     */
    protected function repository()
    {
        $this->runner->runCommands([
            'git reset --hard',
            'git clean -df',
            'git pull',
        ]);
    }

    /**
     * Copy environment file.
     *
     * @param string $from
     */
    protected function environment($from = 'production')
    {
        $this->runner->runCommands([
            'rm .env',
            'cp .env.'.$from.' .env',
        ]);
    }

    /**
     * Update dependencies.
     */
    protected function dependencies()
    {
        if (!file_exists($this->runner->getBasePath().'/composer.phar')) {
            $this->getComposer();
        }

        $this->runner->runCommands([
            'composer self-update',
            'composer update --no-scripts --no-dev',
        ]);
    }

    /**
     * Clear various caches.
     */
    protected function clear()
    {
        $this->runner->runCommands([
            'artisan cache:clear',
            'artisan config:clear',
            'artisan route:clear',
            'artisan twig:clean',
        ]);
    }

    /**
     * Scan annotations.
     */
    protected function annotations()
    {
        $this->runner->runCommands([
            'artisan route:scan',
            'artisan model:scan',
        ]);
    }

    /**
     * Optimize the application.
     */
    protected function optimize()
    {
        $this->runner->runCommands([
            'artisan config:cache',
            'artisan route:cache',
            'artisan config:cache',
            'artisan optimize',
        ]);
    }

    /**
     * Update the database.
     */
    protected function database()
    {
        $this->runner->runCommands([
            'artisan migrate --force',
            'artisan db:backup',
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
        $this->runner->runCommands([
            'php -r "readfile(\'https://getcomposer.org/installer\');" > composer-setup.php',
            'php composer-setup.php',
            'php -r "unlink(\'composer-setup.php\');"',
        ]);
    }
}
