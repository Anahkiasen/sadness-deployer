<?php

namespace SadnessDeployer;

use Illuminate\Support\Arr;
use SadnessDeployer\Commands\Command;
use SadnessDeployer\Commands\CommandsRunner;
use SadnessDeployer\Tasks\AbstractTask;

/**
 * @mixin CommandsRunner
 */
class TasksRunner
{
    /**
     * @var CommandsRunner
     */
    protected $runner;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * @param CommandsRunner $runner
     */
    public function __construct(CommandsRunner $runner)
    {
        $this->runner = $runner;
    }

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->runner, $name], $arguments);
    }

    //////////////////////////////////////////////////////////////////////
    /////////////////////////////// TASKS ////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * @param AbstractTask $task
     * @param int|null     $command
     *
     * @return Command[]
     */
    public function getCommandsFor(AbstractTask $task, $command = null)
    {
        $task->setConfiguration($this->configuration);

        return $task->getCommands();
    }

    /**
     * @param AbstractTask $task
     * @param int          $command
     *
     * @return Command
     */
    public function getCommandFor(AbstractTask $task, $command)
    {
        return Arr::get($this->getCommandsFor($task), $command);
    }

    /**
     * @param AbstractTask $task
     * @param int|null     $command
     *
     * @return array|mixed
     */
    public function runTask(AbstractTask $task, $command = null)
    {
        $commands = $this->getCommandsFor($task, $command);

        return $this->runCommands($commands);
    }
}
