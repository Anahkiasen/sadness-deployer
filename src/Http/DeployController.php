<?php

namespace SadnessDeployer\Http;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use League\Plates\Engine;
use SadnessDeployer\BatchManager;
use SadnessDeployer\Configuration;
use SadnessDeployer\Tasks\AbstractTask;
use SadnessDeployer\TasksRunner;

class DeployController extends Controller
{
    /**
     * @var TasksRunner
     */
    protected $deployer;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Engine
     */
    private $views;

    /**
     * @param Configuration $configuration
     * @param TasksRunner   $deployer
     * @param Engine        $views
     * @param Request       $request
     */
    public function __construct(Configuration $configuration, TasksRunner $deployer, Engine $views, Request $request)
    {
        $this->configuration = $configuration;
        $this->deployer = $deployer;
        $this->views = $views;

        // Set options
        $pretend = $request->get('pretend');
        $pretend = is_null($pretend) ? false : $pretend;
        $this->deployer->setPretend($pretend);
    }

    /**
     * @param BatchManager $batches
     * @param Request      $request
     * @param string       $task
     *
     * @return View
     */
    public function index(BatchManager $batches, Request $request, $task = 'deploy')
    {
        $task = $this->getTask($task);
        $method = $request->get('sync') ? 'runTask' : 'getCommandsFrom';
        $commands = $this->deployer->$method($task);

        // Store commands for retrieval
        $hash = $batches->set($commands);

        return $this->views->render('console', [
            'tasks' => $commands,
            'hash' => $hash,
        ]);
    }

    /**
     * @param BatchManager $batches
     * @param Request      $request
     * @param string       $hash
     * @param string       $command
     *
     * @return array
     */
    public function run(BatchManager $batches, Request $request, $hash, $command)
    {
        // Retrieve command
        $commands = $batches->get($hash);
        $command = Arr::get($commands, $command);
        if (!$command) {
            throw new InvalidArgumentException();
        }

        return $this->deployer->runCommand($command);
    }

    /**
     * @param string $handle
     *
     * @return AbstractTask
     */
    private function getTask($handle)
    {
        $task = sprintf('SadnessDeployer\Tasks\%s', ucfirst($handle));
        if (!class_exists($task)) {
            $task = sprintf('SadnessDeployer\Tasks\Subtasks\%s', ucfirst($handle));
        }

        if (!class_exists($task)) {
            throw new InvalidArgumentException('Invalid task '.$handle);
        }

        /** @var AbstractTask $task */
        $task = new $task($this->configuration);

        return $task;
    }
}
