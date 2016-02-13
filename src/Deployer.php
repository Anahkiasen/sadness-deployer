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
     * @var string
     */
    protected $branch;

    /**
     * @param CommandsRunner $runner
     */
    public function __construct(CommandsRunner $runner)
    {
        $this->runner = $runner;
        $this->runner->setBasePath(base_path());
    }

    /**
     * @param string $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    //////////////////////////////////////////////////////////////////////
    ////////////////////////////// OPTIONS ///////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * @param boolean $pretend
     */
    public function setPretend($pretend)
    {
        $this->runner->setPretend($pretend);
    }

    //////////////////////////////////////////////////////////////////////
    /////////////////////////////// DEPLOY ///////////////////////////////
    //////////////////////////////////////////////////////////////////////

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
        $this->run([
            'git checkout '.$this->branch,
            'git reset --hard',
            'git clean -df',
            'git pull',
        ]);
    }

    /**
     * Copy environment file.
     */
    protected function environment()
    {
        $environment = env('APP_ENV', 'production');

        $this->run([
            'rm .env',
            'cp .env.'.$environment.' .env',
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

        $this->run([
            'composer self-update',
            'composer update --no-scripts --no-dev',
        ]);
    }

    /**
     * Clear various caches.
     */
    protected function clear()
    {
        $this->run([
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
        $this->run([
            'artisan route:scan',
            'artisan model:scan',
        ]);
    }

    /**
     * Optimize the application.
     */
    protected function optimize()
    {
        $this->run([
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
        $this->run([
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
        $this->run([
            'php -r "readfile(\'https://getcomposer.org/installer\');" > composer-setup.php',
            'php composer-setup.php',
            'php -r "unlink(\'composer-setup.php\');"',
        ]);
    }
}
