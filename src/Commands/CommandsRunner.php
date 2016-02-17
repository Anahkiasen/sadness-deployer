<?php

namespace SadnessDeployer\Commands;

use Illuminate\Support\Collection;
use Symfony\Component\Process\Process;

class CommandsRunner
{
    /**
     * @var bool
     */
    protected $pretend = false;

    /**
     * @param bool $pretend
     */
    public function setPretend($pretend)
    {
        $this->pretend = $pretend;
    }

    //////////////////////////////////////////////////////////////////////
    ////////////////////////////// RUNNING ///////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * @param Command[] $commands
     *
     * @return Command[]
     */
    public function runCommands(array $commands)
    {
        $commands = new Collection($commands);
        foreach ($commands as &$command) {
            $command = $this->runCommand($command);
            if (!$command->status && !$this->pretend) {
                break;
            }
        }

        return $commands;
    }

    /**
     * @param Command $command
     *
     * @return Command
     */
    public function runCommand(Command $command)
    {
        // Build process
        $process = new Process($command->sanitized, base_path());

        // Run process
        $output = '';
        if (!$this->pretend) {
            //$process->run(function ($type, $buffer) use (&$output) {
            //    $output .= $buffer;
            //});
        }

        // Wait for process
        while ($process->isRunning()) {
            // ...
        }

        // Update command
        $command->output = trim($output, PHP_EOL).PHP_EOL;
        $command->status = ($process->getExitCode() === 0) || $this->pretend;
        $command->done = true;

        return $command;
    }
}
