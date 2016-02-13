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
        $this->deployer->setPretend(true);

        return view('sadness-deployer::console', [
            'tasks' => $this->deployer->$task(),
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
        $this->deployer->setPretend($request->get('pretend'));

        // Retrieve the command in particular
        $tasks   = $this->deployer->$task();
        $command = $tasks[$command];

        return $this->deployer->run($command->sanitized);
    }
}
