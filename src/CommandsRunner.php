<?php

namespace SadnessDeployer;

use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class CommandsRunner
{
    /**
     * @var array
     */
    protected $output = [];

    /**
     * @var bool
     */
    protected $pretend = false;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @param boolean $pretend
     */
    public function setPretend($pretend)
    {
        $this->pretend = $pretend;
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param string $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @return array
     */
    public function getOutput()
    {
        return $this->output;
    }

    //////////////////////////////////////////////////////////////////////
    ////////////////////////////// RUNNING ///////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * @param array $commands
     *
     * @return array
     */
    public function runCommands(array $commands)
    {
        foreach ($commands as $command) {
            $process = $this->run($command);
            if (!$process->status) {
                break;
            }
        }

        return $this->output;
    }

    /**
     * @param string|array $command
     *
     * @return object
     */
    public function run($command)
    {
        // Build process
        $sanitized = $this->sanitizeCommand($command);
        $process = new Process($sanitized, $this->basePath);

        // Run process
        $output = '';
        if (!$this->pretend) {
            $process->run(function ($type, $buffer) use (&$output) {
                $output .= $buffer;
            });
        }

        // Wait for process
        while ($process->isRunning()) {
            // ...
        }

        // Sanitize output
        $output = $output ?: '';
        $output = trim($output, PHP_EOL).PHP_EOL;
        $status = $process->getExitCode() === 0;

        // Register command
        $process = (object) [
            'command' => $command,
            'sanitized' => $process->getCommandLine(),
            'output' => $output,
            'status' => $status,
        ];

        $this->output[] = $process;

        return $process;
    }

    //////////////////////////////////////////////////////////////////////
    ////////////////////////////// HELPERS ///////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * @param string|array $command
     *
     * @return string
     */
    protected function sanitizeCommand($command)
    {
        $php = (new PhpExecutableFinder())->find(false);

        $command = is_array($command) ? implode(' ', $command) : $command;
        $command = str_replace('composer ', 'COMPOSER_HOME='.storage_path('composer').' php composer.phar ', $command);
        $command = str_replace('artisan ', 'php artisan ', $command);
        $command = str_replace('php ', $php.' ', $command);

        return $command;
    }
}
