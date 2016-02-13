<?php

namespace SadnessDeployer\Http;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
use SadnessDeployer\Tasks\AbstractTask;
use SadnessDeployer\Tasks\Deploy;
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
     * @param string $task
     *
     * @return View
     */
    public function index($task = 'deploy')
    {
        $task = $this->getTask($task);

        return view('sadness-deployer::console', [
            'tasks' => $this->deployer->getCommandsFrom(new $task()),
        ]);
    }

    /**
     * @param Request $request
     * @param string  $task
     * @param string  $command
     *
     * @return array
     */
    public function run(Request $request, $task, $command)
    {
        $task = $this->getTask($task);
        $command = $this->deployer->getCommandFrom($task, $command);

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
