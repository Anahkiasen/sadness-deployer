<?php

namespace SadnessDeployer\Http;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SadnessDeployer\Deployer;

class DeployController extends Controller
{
    /**
     * @var Deployer
     */
    protected $deployer;

    /**
     * @param Deployer $deployer
     */
    public function __construct(Deployer $deployer)
    {
        $this->deployer = $deployer;
    }

    /**
     * @param Request $request
     * @param string  $task
     *
     * @return View
     */
    public function index(Request $request, $task = 'deploy')
    {
        return view('sadness-deployer::console', [
            'tasks' => $this->runTask($request, $task),
        ]);
    }

    /**
     * @param string $task
     * @param string $command
     *
     * @return array
     */
    public function run(Request $request, $task, $command)
    {
        $tasks = $this->runTask($request, $task);
        $command = $tasks[$command];

        return $this->deployer->run($command->sanitized);
    }

    /**
     * @param Request $request
     * @param string  $task
     *
     * @return View
     */
    protected function runTask(Request $request, $task)
    {
        $this->deployer->setPretend($request->get('pretend'));

        return $this->deployer->$task();
    }
}
