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
    public function __construct(TasksRunner $deployer)
    {
        $this->deployer = $deployer;
    }

    /**
     * @param BatchManager $batches
     * @param string       $task
     *
     * @return View
     */
    public function index(BatchManager $batches, $task = 'deploy')
    {
        $task = $this->getTask($task);
        $commands = $this->deployer->getCommandsFrom(new $task());

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

        // Set pretend mode
        $pretend = $request->get('pretend');
        $pretend = is_null($pretend) ? false : $pretend;
        $this->deployer->setPretend($pretend);

        return $this->deployer->runCommand($command);
    }

    /**
     * @param string $task
     *
     * @return AbstractTask
     */
    private function getTask($task)
    {
        $task = sprintf('SadnessDeployer\Tasks\%s', ucfirst($task));
        if (!class_exists($task)) {
            throw new InvalidArgumentException('Invalid task '.$task);
        }

        return new $task();
    }
}
