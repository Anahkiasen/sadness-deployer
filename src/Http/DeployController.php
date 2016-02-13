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
     * @param string $task
     *
     * @return View
     */
    public function index($task = 'deploy')
    {
        return view('sadness-deployer::console', [
            'tasks' => $this->getCommands($task),
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
        // Retrieve the command in particular
        $commands = $this->getCommands($task);
        $command  = $commands[$command];

        // Set pretend mode
        $pretend = $request->get('pretend');
        $pretend = is_null($pretend) ? false : $pretend;
        $this->deployer->setPretend($pretend);

        return $this->deployer->run($command->command);
    }

    /**
     * @param string $task
     *
     * @return array
     */
    protected function getCommands($task)
    {
        $this->deployer->setPretend(true);
        $tasks = $this->deployer->$task();

        return $tasks;
    }
}
