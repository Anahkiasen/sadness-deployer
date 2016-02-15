<?php

namespace SadnessDeployer\Http;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use SadnessDeployer\BatchManager;
use SadnessDeployer\Tasks\AbstractTask;
use SadnessDeployer\TasksRunner;

class DeployController extends Controller
{
    /**
     * @var TasksRunner
     */
    protected $deployer;

    /**
     * @param TasksRunner $deployer
     */
    public function __construct(TasksRunner $deployer, Request $request)
    {
        $this->deployer = $deployer;

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

        return view('sadness-deployer::console', [
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

        return new $task(config('deploy'));
    }
}
