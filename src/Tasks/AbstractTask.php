<?php

namespace SadnessDeployer\Tasks;

use Illuminate\Support\Arr;
use SadnessDeployer\Commands\Command;

abstract class AbstractTask
{
    /**
     * @var array
     */
    protected $commands = [];

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @param array $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return array
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * @param string|array $commands
     */
    protected function run($commands)
    {
        $commands = (array) $commands;
        foreach ($commands as &$command) {
            $command = new Command([
                'command' => $command,
                'status' => null,
                'output' => null,
                'done' => false,
            ]);
        }

        $this->commands = array_merge($this->commands, $commands);
    }

    /**
     * @param string $option
     *
     * @return mixed
     */
    protected function option($option)
    {
        return Arr::get($this->configuration, $option);
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
            'git checkout '.$this->option('scm.branch'),
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
        $file = '.env.'.$environment;
        if (!file_exists(base_path($file))) {
            $file = '.env.example';
        }

        $this->run([
            'rm .env',
            'cp '.$file.' .env',
        ]);
    }

    /**
     * Update dependencies.
     */
    protected function dependencies()
    {
        $flags = env('APP_DEBUG') ? '--no-dev' : '';
        if (!file_exists(base_path('composer.phar'))) {
            $this->getComposer();
        }

        $this->run([
            'composer self-update',
            'composer update --no-scripts '.$flags,
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
