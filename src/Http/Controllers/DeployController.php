<?php

namespace SadnessDeployer\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use League\Plates\Engine;
use Psr\Http\Message\ServerRequestInterface;
use SadnessDeployer\BatchManager;
use SadnessDeployer\Configuration;
use SadnessDeployer\Tasks\AbstractTask;
use SadnessDeployer\Tasks\Custom;
use SadnessDeployer\TasksRunner;

class DeployController
{
    /**
     * @var TasksRunner
     */
    protected $runner;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Engine
     */
    private $views;

    /**
     * @param Configuration          $configuration
     * @param TasksRunner            $runner
     * @param Engine                 $views
     * @param ServerRequestInterface $request
     */
    public function __construct(
        Configuration $configuration,
        TasksRunner $runner,
        Engine $views,
        ServerRequestInterface $request
    ) {
        $this->configuration = $configuration;
        $this->runner = $runner;
        $this->views = $views;

        // Set options
        $pretend = array_get($request->getQueryParams(), 'pretend');
        $pretend = !is_null($pretend);
        $this->runner->setPretend($pretend);
    }

    /**
     * @param BatchManager           $batches
     * @param ServerRequestInterface $request
     * @param string                 $task
     *
     * @return View
     */
    public function index(BatchManager $batches, ServerRequestInterface $request, $task = 'custom')
    {
        // Interactive mode
        $query = $request->getQueryParams();
        if (array_key_exists('interactive', $query)) {
            $this->configuration['tasks'] = $query['interactive'];
        }

        $task = $this->getTask($task);
        $sync = array_get($query, 'sync');
        $method = $sync ? 'runTask' : 'getCommandsFrom';
        $commands = $this->runner->$method($task);

        // Store commands for retrieval
        $hash = $batches->set($commands);

        return $this->views->render('index', [
            'tasks' => $commands,
            'hash' => $hash,
            'url' => $this->configuration->get('url'),
        ]);
    }

    /**
     * @param BatchManager $batches
     * @param string       $hash
     * @param string       $command
     *
     * @return array
     */
    public function run(BatchManager $batches, $hash, $command)
    {
        // Retrieve command
        $commands = $batches->get($hash);
        $command = Arr::get($commands, $command);
        if (!$command) {
            throw new InvalidArgumentException();
        }

        return $this->runner->runCommand($command)->toJson();
    }

    //////////////////////////////////////////////////////////////////////
    ////////////////////////////// HELPERS ///////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * @param string $handle
     *
     * @return AbstractTask
     */
    private function getTask($handle)
    {
        $task = $handle;
        $locations = [
            'SadnessDeployer\Tasks\%s',
            'SadnessDeployer\Tasks\Laravel\%s',
            'SadnessDeployer\Tasks\Subtasks\%s',
        ];

        foreach ($locations as $location) {
            $task = sprintf($location, ucfirst($handle));
            if (class_exists($task)) {
                break;
            }
        }

        if (!class_exists($task)) {
            throw new InvalidArgumentException('Invalid task '.$handle);
        }

        /** @var AbstractTask $task */
        $task = new $task($this->configuration);

        return $task;
    }
}
