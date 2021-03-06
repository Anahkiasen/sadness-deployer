<?php

namespace SadnessDeployer\Tasks;

use SadnessDeployer\Commands\Command;
use SadnessDeployer\Configuration;

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
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
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
     * @param string|array|AbstractTask $commands
     */
    protected function run($commands)
    {
        $commands = $this->processCommands($commands);

        $this->commands = array_merge($this->commands, $commands);
    }

    /**
     * @param string|array|AbstractTask $commands
     */
    protected function runBefore($commands)
    {
        $commands = $this->processCommands($commands);

        $this->commands = array_merge($commands, $this->commands);
    }

    /**
     * @param string $option
     *
     * @return mixed
     */
    protected function option($option)
    {
        return $this->configuration->get($option);
    }

    //////////////////////////////////////////////////////////////////////
    ////////////////////////////// HELPERS ///////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * @param string|array|AbstractTask $commands
     *
     * @return Command[]
     */
    protected function processCommands($commands)
    {
        if (!is_array($commands)) {
            $commands = [$commands];
        }

        $queue = [];
        foreach ($commands as $command) {
            // Check if it's a class or a bash
            // command, create instance if so
            if (is_string($command) && class_exists($command)) {
                /** @var AbstractTask $command */
                $command = new $command($this->configuration);
                if ($command instanceof self) {
                    $queue = array_merge($queue, $command->getCommands());
                    continue;
                }
            }

            // Wrap if not a Command instance
            if (!$command instanceof Command) {
                $command = new Command($command);
            }

            $queue[] = $command;
        }

        return $queue;
    }
}
